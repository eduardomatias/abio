<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "session".
 *
 * @property integer $id_session
 * @property string $name
 *
 * @property JournalSession[] $journalSessions
 */
class Session extends \yii\db\ActiveRecord
{
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
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_session' => 'Id Session',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJournalSessions()
    {
        return $this->hasMany(JournalSession::className(), ['id_session' => 'id_session']);
    }
}
