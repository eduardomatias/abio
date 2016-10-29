<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\journal;
use app\models\journal_pages;
use app\models\log;
use Smalot\PdfParser;

class ImportarEdicaoController extends Controller
{
    private $emailOrigem = "charlan.job@gmail.com";
    private $emailDestino = "charlan.job@gmail.com";
    private $emailDestinatario = "Charlan";
    private $emailCorpo = "";
    private $emailTitulo = "";
    
    private $typeLog = 1; // importacao de edicao

    /**
     * @inheritdoc
     */
    public function processaPdf()
    {
        
        $pdfPendente = $this->listaPdfPendente();
        
        // loop nos registro do banco
        foreach ($pdfPendente['pdfDb'] as $journal) {
            
            // verifica se pdf não existe
            $pathCompleto = $journal['path'].$journal['file_name'];
            if(!is_file($pathCompleto)){
                $this->logErro(['message'=>'O PDF (' . $pathCompleto . ') não foi encontrado.']);
                continue;
            }
            
            try {
                // le pdf
                $textPDF = $this->lerPdf($pathCompleto);

                // trata pdf
                $textPDFTratado = $this->trataPdf($textPDF);

                // salva pdf no banco e move o arquivo
                $this->salvaMovePdf([
                    'id_journal'=>$journal['id_journal'],
                    'content'=>$textPDFTratado,
                    'page_number'=>0,
                    'path'=>$journal['path'],
                    'file_name'=>$journal['file_name'],
                    ]);
                
            } catch (\Exception $e) {
                continue;
            }
        
        }
    }
    
    /**
     * @inheritdoc
     */
    private function listaPdfPendente()
    {
        // busca pdf pendente no banco
        $sql = "SELECT id_journal,journal_number,file_name,path FROM journal WHERE processing_date IS NULL ORDER BY upload_date";
        $pdfDb = journal::findBySql($sql)->all();
        
        // busca pdf pendente na pasta
        // $pdfPasta = CFileHelper::findFiles("/uploads/unprocessed/");
        $pdfPasta = [];
        
        return ['pdfDb'=>$pdfDb, 'pdfPasta'=>$pdfPasta];
    }
    
    /**
     * @inheritdoc
     */
    private function lerPdf($path)
    {
        try {
            $parser = new PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();
            
        } catch (\Exception $e) {
            $this->logErro(['message'=>'Erro ao tentar ler o PDF (' . $path . ')']);
            throw $e;
            
        }
        
        return $text;
    }
    
    /**
     * @inheritdoc
     */
    private function trataPdf($text)
    {
        // Retira quebra de linhas
        $search = array ("\r\n", "\r", "\n");
        $replace = array(' ', ' ', ' ');
        $text1 = str_replace($search, $replace, $text);

        // Corrige separações de sílabas.
        $text2 = preg_replace('/([a-zA-Z])\- ([a-zA-Z])/', '\1\2', $text1);
        
        return $text2;
    }
    
    /**
     * @inheritdoc
     */
    private function salvaPdf($data)
    {
        $connecton = Yii::app()->db;
        $transaction = $connecton->beginTransaction();
        
        try {

            // atualiza data do processamento do PDF
            $journal = journal::findOne($data['id_journal']);
            $journal->processing_date = Date('Y-m-d H:i:s');
            $journal->save();
            
            // cadastra as paginas do jornal
            $journal_pages = new journal_pages();
            $journal_pages->id_journal = $data['id_journal'];
            $journal_pages->content = $data['content'];
            $journal_pages->page_number = $data['page_number'];
            $journal_pages->save();

            // move pdf
            $this->movePdf('uploads/unprocessed/' . $data['file_name'], 'uploads/processed/' . $data['path']);
            
            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollBack();
            $this->logErro(['message'=>'Journal Number ('.$data['journal_number'].') - ' . $e]);
            throw $e;
            
        }
        
        return true;
    }
    
    /**
     * @inheritdoc
     */
    private function movePdf($origem, $destino)
    {
        if(!$this->verificaPath($destino)){
            throw new Exception('Erro ao tentar criar o diretórios "'.$destino.'"');
        }
        
        if(!rename($origem, $destino)){
            throw new Exception('Erro ao tentar mover o PDF (' . $origem . ' para ' . $destino . ').');
        }
        
        return true;
    }
    
    /**
     * @inheritdoc
     * se não existir cria
     */
    private function verificaPath($path)
    {
        $pastas = explode('/', $path);
        $arqPdf = $pastas[count($pastas)-1];
        unset($pastas[count($pastas)-1]); // remove o arquivo
        
        $dir = '';
        foreach ($pastas as $pasta) {
            $dir .= $pasta.'/';
            if(!is_dir($dir)){
                if(!mkdir($dir, 0777)){
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * @inheritdoc
     * param $log[message, error]
     */
    private function logErro($log, $enviaEmail = false)
    {
        if(isset($log['message'])){
            
            $log = new log();
            $log->message = $log['message'];
            $log->error = (isset($log['error'])) ? $log['error'] : "";
            $log->type = $this->typeLog;
            $log->save();

            // envia email
            if($enviaEmail){
                $this->emailErro($log);
            }
            
        }
    }
    
    /**
     * @inheritdoc
     */
    private function emailErro($log)
    {
        $this->emailCorpo = "ABIO \n\n" . 
                            $log['message'] . "\n\n" . 
                            (isset($log['error'])) ? "ERRO: " . $log['error']:"";
        
        $this->enviaEmail();
    }
    
    /**
     * @inheritdoc
     */
    private function enviaEmail()
    {        
        Yii::$app->mailer->compose()
        ->setTo($this->emailOrigem)
        ->setFrom([$this->emailDestino => $this->emailDestinatario])
        ->setSubject($this->emailTitulo)
        ->setTextBody($this->emailCorpo)
        ->send();
    }
    
}