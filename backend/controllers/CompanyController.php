<?php

namespace backend\controllers;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\companyModel;
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
                        'actions' => ['logout', 'index', 'create','get-session'],
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
        $companyModel = new companyModel();
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
}
