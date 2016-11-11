<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property integer $id_company
 * @property string $name
 * @property string $state
 * @property string $city
 * @property string $uf
 *
 * @property User[] $users
 */
class Company extends \yii\db\ActiveRecord
{
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
    public function rules()
    {
        return [
            [['name', 'state', 'city', 'uf'], 'required'],
            [['name', 'state', 'city'], 'string', 'max' => 50],
            [['uf'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_company' => 'Id Company',
            'name' => 'Name',
            'state' => 'State',
            'city' => 'City',
            'uf' => 'Uf',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id_company' => 'id_company']);
    }
}
