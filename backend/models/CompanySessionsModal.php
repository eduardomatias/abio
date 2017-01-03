<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;
use common\models\GlobalModel;

/**
 * Description of CompanySessionsModal
 *
 * @author eduardo
 */
class CompanySessionsModal extends GlobalModel
{
	
    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [['id_session', 'id_company'], 'required'],
            [['id_session', 'id_company'], 'unique', 'targetAttribute' => ['id_session', 'id_company']],
        ];
    }
    
    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return 'company_sessions';
    }
    
    /**
    * @inheritdoc
    */
    public static function primaryKey()
    {
        return ['id_session_company'];
    }
    
    /**
    * @inheritdoc
    */
    public static function findCompanySessions($id_company_sessions = null)
    {
        $whereCustom = (is_numeric($id_company_sessions)) ? "WHERE id_session_company=$id_company_sessions" : "";
        $query = "SELECT *
                  FROM company_sessions
                  $whereCustom";

        $connection = \Yii::$app->db;
        $command = $connection->createCommand($query);
        $reader = $command->query();

        return $reader->readAll();
    }

}
