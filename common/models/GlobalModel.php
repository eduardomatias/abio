<?php

/**
* GlobalModel
* Classe responsável por agrupar funções de uso global dos Modelos
*
* NOTA:
* Gentileza não alterar as funções dessa classe, 
* pois impactará em todos os modelos que a utilizam.
*
* Para modificações, sobrescreva o método desejado no modelo específico.
* @author Charlan Santos
**/

namespace common\models;

use yii\db\ActiveQuery;
use \yii\db\ActiveRecord;
use \yii\db\Exception;

class GlobalModel extends ActiveRecord
{

    /**
     * Constantes utilizada nas consultas que retornam dados a serem utilizados
     * nos componentes combo e autocomplete
     */
    const ALIAS_ID_COMBO = 'ID';
    const ALIAS_TEXT_COMBO = 'TEXTO';
    

    /**
    * @inheritdoc
    */
    public static function findCustom($table, $columnId, $columnText)
    {
        if (empty($table) || empty($columnId) || empty($columnText)) {
            return false;            
        }
        
        $query =  "SELECT $columnId, $columnText FROM $table";
        
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($query);
        $reader = $command->query();
        
        return $reader->readAll();
    }
    
    /**
    * @inheritdoc
    */
    public static function findCombo($table, $columnId, $columnText,$whereCustom='')
    {
        if (empty($table) || empty($columnId) || empty($columnText)) {
            return false;
        }
		
		$whereCustom = ($whereCustom) ? "$whereCustom AND" : $whereCustom;
		
        $query =  "SELECT DISTINCT $columnId AS ".self::ALIAS_ID_COMBO.", $columnText AS ".self::ALIAS_TEXT_COMBO." 
        			FROM (SELECT * FROM $table ORDER BY $columnText)
					WHERE $whereCustom ROWNUM < 100 
					ORDER BY $columnText";

        $connection = \Yii::$app->db;
        $command = $connection->createCommand($query);
        $reader = $command->query();

        return $reader->readAll();
    }
    
    /**
    * @inheritdoc
    * 
    */
    public static function findAutocomplete($table, $columnId, $columnText, $filter = null, $whereCustom = null)
    {
        if (empty($table) || empty($columnId) || empty($columnText)) {
            return false;
        }

        $where = "";
        
        if (!empty($filter)) { 
        	$where .= " UPPER(CONVERT(".$columnText.", 'US7ASCII')) LIKE CONVERT('".strtoupper($filter)."%', 'US7ASCII') AND ";
        }
		
        if (!empty($whereCustom)) { 
        	$where .= " ($whereCustom) AND ";
        }
        
        $query =  "SELECT DISTINCT $columnId AS ".self::ALIAS_ID_COMBO.", $columnText AS ".self::ALIAS_TEXT_COMBO." 
				   FROM (SELECT * FROM $table ORDER BY $columnText)
				   WHERE $where
						 ROWNUM < 100 
				   ORDER BY $columnText";

        $connection = \Yii::$app->db;
        $command = $connection->createCommand($query);
        $reader = $command->query();

        return $reader->readAll();
    }
    
	
     /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = NULL) {
        try {
            
            parent::save($runValidation, $attributeNames);            
            
            $modelErro = $this->getFirstErrors();
            
            if (!empty($modelErro)) {
                $errorMsg = ['message' => [
                    'dev' => array_values($modelErro)[0], 
                    'prod' => array_values($modelErro)[0]],
                ];                
            }
            
            
        } catch (\Exception $e) {            
            
            $errorMsg = ['message' => ['dev' => $e->getMessage()]];
        }
        
        if (!empty($errorMsg)) {
            $errorMsg = \Yii::$app->v->getErrorMsgCurrentEnv($errorMsg);   
            throw new \Exception($errorMsg);            
        }

		return true;
    }
    
   public function getSpecificScenario($scenario)
    {
        if (empty($scenario)) {
            return false;
        }
        
        $scenarioc = array();
        $rules = $this->rules();
        
        for ($i = 0; $i < count($rules); $i++){
            if (isset($rules[$i]['on'])) {
                if ($rules[$i]['on'][0] == $scenario) {
                    $scenarioc[$i]['fields'] =  $rules[$i][0];
                    $scenarioc[$i]['validator'] = $rules[$i][1];
                }
            }
        }
        
        return $scenarioc;
    }

private function _saveMultiple($dados, $model=null, $relacao=null, $flgAtivo)
	{
		try{

			preg_match('/.*(?<=\\\\)/si', get_class($this), $match);

			if (empty($match[0])) {
				return false;
			}
	
			$namespace = $match[0];
	
		
			if(!empty($relacao)) {
				$modelRelacao = $namespace . $relacao[0];
				$idRelacao = $relacao[1];
				$valorRelacao = $relacao[2];
				
			} else {
				$modelRelacao = $namespace . $model;
				$idRelacao = null;
				$valorRelacao = null;
				
			}
			
			$pkModel = $modelRelacao::primaryKey()[0];
			
			
			foreach($dados as $key => $col) {
				if($idRelacao)
					$col[$idRelacao] = $valorRelacao;
				
				
				if (empty($col[$pkModel])) { 
					if (empty($col[$flgAtivo])) {
						continue;
					}
					$m = new $modelRelacao();
					unset($col[$pkModel]);	
				} else {
					$m = $modelRelacao::findOne($col[$pkModel]);
				}
				
				$m->setAttributes($col);
				$m->save();
				
			}
		
        } catch (\Exception $e) {
            throw $e;
        }
		
	}
	
	
    private function _saveRelated($dados, $relacao, $flgAtivo)
    {
        try {
            
			/*----------- Caso esteja usando o dhtmlxForm.sendData() no form abaixo obtenho o id que ele envia automaticamente ----------*/
            $id = $this->primaryKey()[0];
            if (isset($dados[0]['id'])) {
                $dados[0][$id] = $dados[0]['id'];
            }
			/*---------------------------------------------------------------------------------------------------------------------------------------------------*/
			
		    /*----------- Verifica se deve atualizar o registro ao invés de cria-lo-------------------*/
			$modelPai = $this;
			if(!empty($dados[0][$id])){
				$modelPai = $this->findOne($dados[0][$id]);
			}
			/*----------------------------------------------------------------------------------------------*/
			
            $modelPai->setAttributes($dados[0]);
            
			if ($modelPai->save()) {
			
				/*----------- Salva o modelo filho ----------*/
                $idValor = $modelPai->{$id};
				$this->_saveMultiple($dados[1], null, [array_keys($relacao)[0],array_values($relacao)[0],$idValor], $flgAtivo);
				/*------------------------------------------------*/	
			
            }
			
        } catch (\Exception $e) {
            throw $e;
        }

    }
	
	
    private function saveRelatedAndMultiple($tipo, $dados, $model, $relacao, $flgAtivo, $transacao)
	{
	
		try{
		
			if($transacao) {
				$connection = \Yii::$app->db;
				$transaction = $connection->beginTransaction();
			}
			
			if($tipo==1) {
				$this->_saveRelated($dados, $relacao, $flgAtivo);
			} else { 
				$this->_saveMultiple($dados, $model, null, $flgAtivo);
			}
			
			if($transacao) {
				$transaction->commit();
			}
			return true;
			
        } catch (\Exception $e) {
			if($transacao) {
				$transaction->rollBack();
			}
            throw $e;
        }
		
	}
	
	
    /**
	* saveRelated
	* Salva em duas tabelas relacionadas "pai e filho(s)"
	*
	* @access Public
	* @author Eduardo M. Pereira
	* @package GlobalModel
	* @since 12/2016
	* @param Array $dados
	* 		$dados[0] = dados do pai
	* 		$dados[1] = dados do(s) filho(s)
	* 		Ex:
	* 		$dados = [
	* 		        0 => ['LISTA' => 'BLA', 'DESCRICAO_LISTA' =>'BLA'],
	* 		        1 =>[
	* 		             ['ITEM_LISTA' => 'BLA', 'DESC_ITEM' =>'BLA'],
	* 		             ['ITEM_LISTA' => 'BLA', 'DESC_ITEM' =>'BLA'],
	* 		            ],
	* 		         ];
	* @param Array $relacao
	* 		[0]: Nome do model filho(funciona apenas se o model do filho estiver no mesmo diretorio do pai),
	* 		[1]: campo que relaciona o pai com o(s) filho(s)
	* 		[2]: valor do pai para amarrar o(s) filho(s)
	* 		Ex: $relacao = [0=>'PerguntaCheckListModel', 1=>'ECM24_ID_CHECK_LIST',2=>1];
	* @param String $flgAtivo = nome do campo flg ativo do modelo
	* @param Boolean $transacao
	* @return true|Exception
	*/
    public function saveRelated($dados, $relacao, $flgAtivo, $transacao = true)
	{
		return $this->saveRelatedAndMultiple(1, $dados, null, $relacao, $flgAtivo, $transacao);
	}
	
	
    /**
	* saveRelated
	* Salva em duas tabelas relacionadas "pai e filho(s) by Renato Russo"
	*
	* @access Public
	* @author Eduardo M. Pereira
	* @package GlobalModel
	* @since 12/2016
	* @param Array $dados
	* 		$dados[0] = dados do pai
	* 		$dados[1] = dados do(s) filho(s)
	* 		Ex: $dados = [['ITEM_LISTA' => 'BLA', 'DESC_ITEM' =>'BLA'], ['ITEM_LISTA' => 'BLA', 'DESC_ITEM' =>'BLA']];
	* @param String $model = Nome do modelo para salvar os dados
	* @param String $flgAtivo = nome do campo flg ativo do modelo
	* @param Boolean $transacao
	* @return true|Exception
	*/
   public function saveMultiple($dados, $model, $flgAtivo, $transacao = true)
	{
		return $this->saveRelatedAndMultiple(2, $dados, $model, null, $flgAtivo, $transacao);
	}
}
