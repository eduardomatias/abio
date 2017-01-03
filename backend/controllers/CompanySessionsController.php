<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\controllers;
use backend\models\SessionModel;
use backend\models\CompanyModel;
use backend\models\CompanySessionsModal;
use common\controllers\GlobalBaseController as BaseController;

/**
 * Company Sessions controller
 */
class CompanySessionsController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function actionDelete()
    {
        try {
            $post = \Yii::$app->request->post();
            $sessionModel = CompanySessionModel::findOne($post['id_company_session']);
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
