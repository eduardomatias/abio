<?php

namespace backend\controllers;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use backend\models\CompanyModel;
use backend\models\CompanySessionsModal;
use common\controllers\GlobalBaseController as BaseController;

/**
 * Company controller
 */
class CompanyController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'create', 'get-session', 'save'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Create action.
     *
     * @return view
     */
    public function actionCreate()
    {
        return $this->render('formCompany');
    }

    /**
     * GetSession action.
     *
     * @return xml
     */
    public function actionGetSession()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = \Yii::$app->response->headers;
        $headers->add('Content-type', 'text/xml');
        
        $xml = \Yii::$app->dataDumpComponent->getXML([], $this->getConfigGridSessionHeader());
        
        return $this->renderPartial('/default/xmlMask', array("xml" => $xml));
    }
    
    /**
     * @inheritdoc
     */
    private function getConfigGridSessionHeader()
    {
        $companyModel = new CompanyModel();
    	$gridSettings = $companyModel->gridSessionSettings();

        $config = [];

    	foreach ($gridSettings as $data) {
            $config['header'][0][] = $data['sets'];

            if (isset($data['filter'])) {
                $config['header'][1][] = $data['filter'];
            }
        }

        return $config;
    }
    
    /**
     * @inheritdoc
     */
    public function actionSave()
    {
        try {

            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();

            $post = \Yii::$app->request->post();
            
            // set session data
            $dados[1] = json_decode($post['grid_sessions'],true);
            unset($post['grid_sessions']);
            
            // set company data
            $dados[0] = $post;
            
            // save data
            $companyModel = new CompanyModel();
            $companyModel->saveRelated($dados, ['CompanySessionsModal'=>'id_company'],false);
            
            // send image
            $companyModel->imageFile = UploadedFile::getInstance($companyModel, 'logo_url');
            $companyModel->upload();
            
            $msg = "";
            $status = true;
            $transaction->commit();
            
        } catch (\Exception $exc) {
            $msg = $exc->getMessage();
            $status = false;
            $transaction->rollBack();
            
        }
        
        print_r("<script>parent.sendDataCallbackDefault(true, '" . json_encode(['message'=>$msg, 'status'=>$status]) . "');</script>");
    }
    
    /**
     * @inheritdoc
     */
    private function Upload() {
        if (Yii::$app->request->isPost) {
            $model = new CompanyModel();
            $model->logo_url = UploadedFile::getInstance($model, 'logo_url');
            if ($model->upload()) {
                return;
            }
        }
    }
    
}
