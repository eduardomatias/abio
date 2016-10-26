<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class User extends ActiveRecord
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

   
   

}
