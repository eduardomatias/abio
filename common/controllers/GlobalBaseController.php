<?php

/**
*
* GlobalController
* Classe responsável por agrupar funções de uso global dos controllers
*
* NOTA:
* Gentileza não alterar as funções dessa classe,
* pois impactará em todos os controllers que a utilizam.
*
* Para modificações, sobrescreva o método desejado no controller específico.
* 
* @author Charlan Santos
*/

namespace common\models;

use \app\models\GlobalModel as GlobalModel;
use Yii;
use yii\web\Controller;

class GlobalBaseController extends Controller
{

   /**
    * Constantes utilizada nas consultas que retornam dados a serem utilizados
    * nos componentes combo e autocomplete
    */
    const ALIAS_ID_COMBO = 'ID';
    const ALIAS_TEXT_COMBO = 'TEXTO';
    
   /**
    * @var array Botões de ação do grid
    */
    protected $btns;
    
   /**
    * @var string prefixo da função do modelo que recuperará os dados do grid
    */
    protected $prefixQueryFn = 'gridQuery';
    
   /**
    * @var string prefixo da função do modelo que recuperará a configuração do grid
    */
    protected $prefixSettingsFn = 'gridSettings';
    
    
    /*
     * 
     *
     * @autor Vitor Silva
     *
     * @return 
     *
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        $rotaController = $action->controller->getRoute();
        $temPermissao = $this->seg()->validaPermissaoAcao($rotaController,$_SESSION['gid']);
        if ($temPermissao) {
            return parent::beforeAction($action);
        } else {
            $msg = Yii::t('app','Você não tem permissão para executar esta ação.');
            echo json_encode(array('mensagem'=>$msg, 'tipo'=>'erro'));
        }
    }
    
   /*
   * Obtém o texto atual inserido do autocomplete
   *
   * @autor Charlan Santos
   *
   * @return string
   *
   */
   protected function getSeachText()
   {
    	$data = Yii::$app->request->get();
        return isset($data['mask']) ? $data['mask'] : '';
   }

   
   /*
    * Obtém os dados em xml do grid dinamicamente. 
    *
    * @autor Charlan Santos
    *
    * @param array $nameModelFn - Nome das funções que tem a consulta e gridSettings
    * Ex: [ 
    *       'gridXmlFn' => 'nomeFuncaoQueRecuperaDadosDoGrid',
    *       'gridSettings' => 'nomeFuncaoQueRecuperaConfigDoGrid'
    *      ]
    * Ou uma string com o sufixo da função no modelo. Nesse caso o prefixo será padrão:
    *  gridQuery + Sufixo
    *  gridSettings + Sufixo
    *  
    * @param yii\db\ActiveRecord $instanceModel
    *
    * @return xml - dados em xml do grid    
    *
    */
   public function globalGetXmlGrid($nameModelFn, yii\db\ActiveRecord $instanceModel)
   {  
       $configGrid = '';

       /* Verfica se foi informado o nome completo ou somente o sufixo das funções que serão chamadas do modelo ou */ 
       if ( !is_array($nameModelFn) ) {
           $nameModelFn = ucfirst($nameModelFn);
           
           $namesModelFn['gridXmlFn'] = $this->prefixQueryFn . $nameModelFn;
           $namesModelFn['gridSettings'] = $this->prefixSettingsFn . $nameModelFn;
       }
       
       $this->setHeaderXml();
        
       $result = $this->globalGetGridData($namesModelFn['gridXmlFn'], '', true, $instanceModel);
   
       $configGrid = $this->globalGetConfigGridHeader($namesModelFn['gridSettings'], $instanceModel);
   
       $btns = $this->getBtnsGrid($configGrid['btnsAvailable']);
   
       unset($configGrid['btnsAvailable']);
   
       $this->setBtnsGrid($result, $btns);
   
       $xml = Yii::$app->dataDumpComponent->getXML($result, $configGrid );
   
       return $this->renderPartial('@app/views/default/xmlMask', array("xml" => $xml));
   }
   
   private function globalGetGridData($gridXmlFn, $param, $throwException, $instanceModel)
   {
       return $this->callMethodDynamically($gridXmlFn, $param, $throwException, $instanceModel);
   }
   
   /*
    * Retorna o cabeçalho do grid para ser usado no método dataDumpComponent::getXML()
    *
    * @autor Charlan Santos
    *
    * @param string $table
    * @param string $columnId
    * @param string $columnText
    * @param string $where
    * @param string $className - Classe que contém a função personalizada
    * @param string $functionName - Nome da função personalizada
    *
    * @return xml - xml do combo
    * 
    */
    public function actionCombo($table, $columnId, $columnText, $where= null, $className = null, $functionName = null)
    {
     	Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-type', 'text/xml');

		if (!empty($className) && !empty($functionName)) {
        	$data = $className::$functionName($table, $columnId, $columnText,$where);
    	} else {
    	 	$data = GlobalModel::findCombo($table, $columnId, $columnText,$where);
    	}

    	$dataList = '';
    	if (!empty($data)){
        	$dataList = \yii\helpers\ArrayHelper::map($data, GlobalModel::ALIAS_ID_COMBO, GlobalModel::ALIAS_TEXT_COMBO);
		}

        $xml = Yii::$app->dataDumpComponent->getXmlCombo($dataList);

        return $this->renderPartial('@app/views/default/xmlMask', array("xml" => $xml));
    }
    
    /*
     * Retorna o cabeçalho do grid para ser usado no método dataDumpComponent::getXML()
     *
     * @autor Charlan Santos, Eduardo Matias e Mateus Dutra
     *
     * @param string $table
     * @param string $columnId 
     * @param string $columnText
     * @param string $where
     * @param string $className - Classe que contém a função personalizada
     * @param string $functionName - Nome da função personalizada
     * 
     * @return xml - xml do combo autocomplete
     *
     */
    public function actionAutocomplete($table, $columnId, $columnText, $where= null, $className = null, $functionName = null)
    {
    	Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-type', 'text/xml');

    	$searchText = $this->getSeachText();

		if (!empty($className) && !empty($functionName)) {
        	$data = $className::$functionName($table, $columnId, $columnText, $searchText, $where);
    	} else {
    	 	$data = GlobalModel::findAutocomplete($table, $columnId, $columnText, $searchText, $where);
    	}

       $dataList = '';
    	if (!empty($data)){
    		$count = count($data);
    		for($i = 0; $i < $count; $i++) {
    			$value = $data[$i][GlobalModel::ALIAS_TEXT_COMBO];
    			$data[$i][GlobalModel::ALIAS_TEXT_COMBO] = preg_replace('/[!@#$%&*()-+=ªº^~,.:;?<>°ºª\x00-\x1f\"\'\{\}\[\]\(\)]/', '', $value);
    		}
    		
        	$dataList = \yii\helpers\ArrayHelper::map($data, GlobalModel::ALIAS_ID_COMBO, GlobalModel::ALIAS_TEXT_COMBO);
		}

        $xml = Yii::$app->dataDumpComponent->getXmlCombo($dataList, false);
        
        return $this->renderPartial('@app/views/default/xmlMask', array("xml" => $xml));
    }
    
    public function setHeaderXml()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-type', 'text/xml');
    }
    
    
    /*
     * Seta os botões que aparecerão no grid
     *
     * @autor Charlan Santos
     *
     * @param array $result - multidimensional com dados a serem exibido no grid
     *                ex retorno de yii\db\DataReader->readAll()
     * @param array $btns - retorno de getActions
     * 
     * @return array 
     *
     */
    protected function setBtnsGrid(&$result, $btns)
    {
        foreach($result as $k => $data) {
             
            foreach ($btns as $btnId => $btn) {
                $result[$k][$btnId] = $btn;
            }
        }
    }
    
    /*
     * Retorna os botões que aparecerão no grid
     *
     * @autor Charlan Santos
     *
     * @param array $actions ex: ['editar', 'excluir']
     *
     * @return array 
     */
    protected function getBtnsGrid(array $actions)
    {        
        $this->btns = [
            'editar' => '../libs/layoutMask/imgs/editar.png^'.Yii::t("app","Editar").'^javascript:Form.runAction("update")^_self',
            'excluir' => '../libs/layoutMask/imgs/excluir.png^'.Yii::t("app","Excluir").'^javascript:Form.runAction("excluir")^_self',
        ];
        
       return array_intersect_key($this->btns, array_flip($actions));       
    }
    
    /*
     * Retorna o cabeçalho do grid para ser usado no método dataDumpComponent::getXML()
     *
     * @autor Charlan Santos
     *
     * @param string $gridNameFn - Nome da função que retorna a configuração do grid
     * @param yii\db\ActiveRecord $instanceModel
     *
     */
    protected function globalGetConfigGridHeader($gridNameFn, yii\db\ActiveRecord $instanceModel)
    {
        $config = [];
    
        $gridSettings = $this->callMethodDynamically($gridNameFn, '', true, $instanceModel);
    
        foreach ($gridSettings as $k => $data) {
            	
            if (!empty($data['sets'])  ||  !empty($data['filter'])) {
                $config['header'][0][] = $data['sets'];
    
                if (isset($data['filter'])) {
                    $config['header'][1][] = $data['filter'];
                }
            } else if (!empty($data['btnsAvailable'])) {
                $config['btnsAvailable'] = $data['btnsAvailable'];
            }
        }
    
        return $config;
    }
}
