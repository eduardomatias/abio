<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\models;

use yii\db\ActiveRecord;

/**
 * Description of journal
 *
 * @author
 */
class Journal_session extends ActiveRecord
{
    public static function journal_session()
    {
        return 'journal_session';
    }
}
