<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Description of notification
 *
 * @author
 */
class Notification extends ActiveRecord
{
    public static function notification()
    {
        return 'notification';
    }
    
    public static function buscaAlertaSemOcorrencia()
    {
        $sql = "SELECT n.id_notification, n.name, jp.content, jp.id_journal_pages
                FROM notification n
                INNER JOIN journal_pages jp ON(jp.content LIKE CONCAT('%', n.name ,'%'))
                INNER JOIN journal_session js ON(js.id_journal = jp.id_journal)
                LEFT JOIN occurrence o ON(o.id_journal_page = jp.id_journal_pages)
                WHERE n.created <= js.processing_date AND o.id_occurrence IS NULL
                GROUP BY n.id_notification, n.name, jp.content, jp.id_journal_pages";
        return \Yii::$app->db->createCommand($sql)->queryAll();
    }
}
