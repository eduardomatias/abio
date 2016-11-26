<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

class FrameAsset extends AssetBundle
{
    public $basePath = '@vendor';
    public $baseUrl = '@assetsPath';

    public $css = [
		'dhtmlx/dhtmlxFrame.css',		
    ];
    public $js = [
		
		'blockUI/jquery.blockUI.js',
		'dhtmlx/dhtmlx.js',
		'frontFrame/dhtml_commands.js',
		//'dist/js/app.js',
	    'jquery/jquery.slimscroll.min.js'		    
    ];
  
      public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    
     
}
