<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id_user
 * @property integer $id_company
 * @property string $login
 * @property string $password
 * @property integer $user_type
 * @property string $created
 *
 * @property Journal[] $journals
 * @property Notification[] $notifications
 * @property Company $idCompany
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_company', 'user_type'], 'integer'],
            [['login', 'password', 'user_type'], 'required'],
            [['created'], 'safe'],
            [['login'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 500],
            [['id_company'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['id_company' => 'id_company']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_user' => 'Id User',
            'id_company' => 'Id Company',
            'login' => 'Login',
            'password' => 'Password',
            'user_type' => 'User Type',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJournals()
    {
        return $this->hasMany(Journal::className(), ['id_user' => 'id_user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::className(), ['id_user' => 'id_user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdCompany()
    {
        return $this->hasOne(Company::className(), ['id_company' => 'id_company']);
    }
}
