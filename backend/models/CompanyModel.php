<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;

use common\models\GlobalModel;
use yii\web\UploadedFile;

/**
 * Description of CompanyModel
 *
 * @author eduardo
 */
class CompanyModel extends GlobalModel {

    public $logo_url;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['name', 'state', 'city', 'uf', 'imprensa_estadual', 'logo_url'], 'safe'],
            [['name'], 'unique'],
            [['logo_url'], 'image', 'extensions' => 'png, jpg',
              'minWidth' => 100, 'maxWidth' => 1000,
              'minHeight' => 100, 'maxHeight' => 1000,],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'company';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey() {
        return ['id_company'];
    }

    /**
     * @inheritdoc
     */
    public function gridSessionSettings() {
        return [
            ['sets' => ['title' => 'CÓDIGO', 'width' => '90', 'type' => 'ro', 'sort' => 'str', 'align' => 'center', 'id' => 'id_session'], 'filter' => ['title' => '#text_filter']],
            ['sets' => ['title' => 'SESSÃO', 'width' => '*', 'type' => 'ro', 'sort' => 'str', 'id' => 'name'], 'filter' => ['title' => '#text_filter']],
            ['sets' => ['title' => 'AÇÕES', 'hidden' => true, 'width' => '50', 'type' => 'img', 'sort' => 'str', 'align' => 'center', 'id' => 'excluir'], 'filter' => ['title' => '#rspan']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function upload() {
        if ($this->validate()) {
            $this->logo_url->saveAs('images/logo_imprensa/' . $this->logo_url->baseName . '.' . $this->logo_url->extension);
            return true;
        } else {
            return false;
        }
    }

}
