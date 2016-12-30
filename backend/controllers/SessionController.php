<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\controllers;
use backend\models\sessionModel;
use common\controllers\GlobalBaseController as BaseController;

/**
 * Session controller
 */
class SessionController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function actionGetData()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = \Yii::$app->response->headers;
        $headers->add('Content-type', 'text/xml');

        $result = sessionModel::findSession();
        
        $this->getGridDelete($result);
        
        $xml = \Yii::$app->dataDumpComponent->getXML($result, $this->getConfigGridSessionHeader());
        
        return $this->renderPartial('/default/xmlMask', array("xml" => $xml));
    }
    
    /**
     * @inheritdoc
     */
    private function getConfigGridSessionHeader()
    {
        $sessionModel = new sessionModel();
    	$gridSettings = $sessionModel->gridSessionSettings();

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
    private function getGridDelete(&$result)
    {
        foreach($result as $k => $data) {
            $botoesExcluir = \Yii::getAlias('@dhtmlxImg').'/default/close.png^Excluir^javascript:objGlobal.excluirSession(' . $data['id_session'] . ')^_self';
            $result[$k]['excluir'] = $botoesExcluir;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function actionSave()
    {
        try {
            $post = \Yii::$app->request->post();
            $sessionModel = (!empty($post['id_session'])) ? sessionModel::findOne($post['id_session']) : new sessionModel();
            $sessionModel->name = $post['name'];
            $sessionModel->save();
            $msg = $sessionModel->id_session;
            $status = true;
            
        } catch (\Exception $exc) {
            $msg = $exc->getMessage();
            $status = false;
            
        }
        
        return json_encode(['msg'=>$msg, 'status'=>$status]);
    }
    
    /**
     * @inheritdoc
     */
    public function actionDelete()
    {
        try {
            $post = \Yii::$app->request->post();
            $sessionModel = sessionModel::findOne($post['id_session']);
            $sessionModel->delete();
            $msg = "";
            $status = true;
            
        } catch (\Exception $exc) {
            $msg = $exc->getMessage();
            $status = false;
            
        }
        
        return json_encode(['msg'=>$msg, 'status'=>$status]);
    }
}
