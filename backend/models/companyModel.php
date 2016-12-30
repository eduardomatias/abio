<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;
use common\models\GlobalModel;

/**
 * Description of companyModel
 *
 * @author eduardo
 */
class companyModel extends GlobalModel 
{
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'state', 'city', 'uf', 'imprensa_estadual'], 'required'],
            [['name'], 'string', 'max' => 50],
        ];
    }
    
    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return 'company';
    }
    
    /**
    * @inheritdoc
    */
    public static function primaryKey()
    {
        return ['id_company'];
    }
    
    /**
     * @inheritdoc
     */
    public function gridSessionSettings()
    {
        return [
            ['sets' => ['title' => 'CÓDIGO', 'width'=>'90', 'type'=>'ro', 'sort'=>'str', 'align'=>'center', 'id'  => 'id_session' ], 'filter' => ['title'=>'#text_filter']],
            ['sets' => ['title' => 'SESSÃO', 'width'=>'*', 'type'=>'ro', 'sort'=>'str', 'id'  => 'name' ], 'filter' => ['title'=>'#text_filter']],
            ['sets' => ['title'=> 'AÇÕES','hidden'=>true ,'width'=>'50', 'type'=>'img', 'sort'=>'str', 'align'=>'center', 'id' => 'excluir'],'filter' => ['title'=>'#rspan']],
        ];
    }
}
