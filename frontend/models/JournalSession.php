<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "journal_session".
 *
 * @property integer $id_journal_session
 * @property integer $id_session
 * @property integer $id_journal
 * @property string $path
 * @property string $file_name
 * @property string $content
 *
 * @property Journal $idJournal
 * @property Session $idSession
 */
class JournalSession extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'journal_session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_session', 'id_journal', 'path', 'file_name', 'content'], 'required'],
            [['id_session', 'id_journal'], 'integer'],
            [['content'], 'string'],
            [['path', 'file_name'], 'string', 'max' => 100],
            [['id_journal'], 'exist', 'skipOnError' => true, 'targetClass' => Journal::className(), 'targetAttribute' => ['id_journal' => 'id_journal']],
            [['id_session'], 'exist', 'skipOnError' => true, 'targetClass' => Session::className(), 'targetAttribute' => ['id_session' => 'id_session']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_journal_session' => 'Id Journal Session',
            'id_session' => 'Id Session',
            'id_journal' => 'Id Journal',
            'path' => 'Path',
            'file_name' => 'File Name',
            'content' => 'Content',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJournal()
    {
        return $this->hasOne(Journal::className(), ['id_journal' => 'id_journal']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSession()
    {
        return $this->hasOne(Session::className(), ['id_session' => 'id_session']);
    }
}
