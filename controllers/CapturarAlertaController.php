<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\occurrence;
use app\models\notification;

class CapturarAlertaController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function actionCriaOcorrencia()
    {
        if(!empty(($ocorrencias = Notification::buscaAlertaSemOcorrencia()))){
            foreach ($ocorrencias as $oco) {
                $id_not = $oco['id_notification'];
                $termo  = $oco['name'];
                $text   = $oco['content'];
                $id_jo  = $oco['id_journal_pages'];
                
                foreach ($this->marcaTermo($text, $termo) as $o) {
                    $this->salvaOcorrencia([
                        'id_notification' => $id_not,
                        'id_journal_page' => $id_jo,
                        'content' => $o,
                    ]);
                }
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    private function salvaOcorrencia($data)
    {
        $occurrence = new occurrence();
        $occurrence->id_notification = $data['id_notification'];
        $occurrence->id_journal_page = $data['id_journal_page'];
        $occurrence->content = $data['content'];
        return $occurrence->save();
    }
    
    /**
     * @inheritdoc
     */
    private function marcaTermo($text, $termo, $return=[])
    {
        $qtd     = 30;
        $posicao = strripos($text, $termo);
        $inicio  = (($posicao-$qtd) < 0) ? 0 : $posicao-$qtd;
        $return[] = str_replace($termo, "<b>$termo</b>", substr($text, $inicio, strlen($termo) + (2*$qtd)));
        
        $resto = substr($text, 0, $posicao);
        if(strripos($resto, $termo)) {
            $return = $this->marcaTermo($resto, $termo, $return);
        }
        
        return $return;
    }
    
}