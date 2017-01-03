<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;
use common\models\GlobalModel;

/**
 * Description of SessionModel
 *
 * @author eduardo
 */
class SessionModel extends GlobalModel
{
	
    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'safe'],
        ];
    }
    
    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return 'session';
    }
    
    /**
    * @inheritdoc
    */
    public static function primaryKey()
    {
        return ['id_session'];
    }
    
    /**
    * @inheritdoc
    */
    public static function findSession($id_session = null)
    {
        $whereCustom = (is_numeric($id_session)) ? "WHERE id_session=$id_session" : "";
        $query = "SELECT id_session, name
                  FROM session
                  $whereCustom 
                  ORDER BY name";

        $connection = \Yii::$app->db;
        $command = $connection->createCommand($query);
        $reader = $command->query();

        return $reader->readAll();
    }

    /**
    * @inheritdoc
    */
    public static function findSessionCompany($id_company)
    {
        $query = "SELECT s.id_session, s.name
                  FROM session s
                  INNER JOIN company_sessions cs ON(cs.id_session=s.id_session)
                  WHERE cs.id_company=$id_company
                  ORDER BY s.name";

        $connection = \Yii::$app->db;
        $command = $connection->createCommand($query);
        $reader = $command->query();

        return $reader->readAll();
    }
    
    /**
    * @inheritdoc
    */
    public function gridSessionSettings()
    {

        return [
            ['sets' => ['title' => 'CÓDIGO', 'width'=>'90', 'type'=>'ro', 'sort'=>'str', 'align'=>'center', 'id'  => 'id_session' ], 'filter' => ['title'=>'#text_filter']],
            ['sets' => ['title' => 'SESSÃO', 'width'=>'*',  'type'=>'ed', 'sort'=>'str', 'align'=>'left',   'id'  => 'name' ], 'filter' => ['title'=>'#text_filter']], 
            ['sets' => ['title'=> 'AÇÕES', 'width'=>'50',   'type'=>'img','sort'=>'str', 'align'=>'center', 'id' => 'excluir'],'filter' => ['title'=>'#rspan']],
        ];
    }
}
