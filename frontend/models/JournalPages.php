<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "journal_pages".
 *
 * @property integer $id_journal_pages
 * @property integer $id_journal
 * @property string $content
 * @property integer $page_number
 *
 * @property Journal $idJournal
 * @property Occurrence[] $occurrences
 */
class JournalPages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'journal_pages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_journal', 'content', 'page_number'], 'required'],
            [['id_journal', 'page_number'], 'integer'],
            [['content'], 'string'],
            [['id_journal'], 'exist', 'skipOnError' => true, 'targetClass' => Journal::className(), 'targetAttribute' => ['id_journal' => 'id_journal']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_journal_pages' => 'Id Journal Pages',
            'id_journal' => 'Id Journal',
            'content' => 'Content',
            'page_number' => 'Page Number',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdJournal()
    {
        return $this->hasOne(Journal::className(), ['id_journal' => 'id_journal']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOccurrences()
    {
        return $this->hasMany(Occurrence::className(), ['id_journal_page' => 'id_journal_pages']);
    }
}
