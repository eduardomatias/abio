<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
   use yii\web\Response; 
   use yii\helpers\ArrayHelper;


class ApiController extends ActiveController
{
   public $modelClass = 'app\models\User';
  
 public function behaviors()
  {
      return ArrayHelper::merge(parent::behaviors(), [
          [
              'class' => 'yii\filters\ContentNegotiator',
              'only' => ['view', 'index'],  // in a controller
              // if in a module, use the following IDs for user actions
              // 'only' => ['user/view', 'user/index']
              'formats' => [
                  'application/json' => Response::FORMAT_JSON,
              ],
              'languages' => [
                  'en',
                  'de',
              ],
          ],
      ]);
  }
  
  
  
  public function teste()
  {
      echo'teste';
      die;
  }
}
