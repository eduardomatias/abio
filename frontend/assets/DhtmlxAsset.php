<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

 namespace frontend\assets;

use yii\web\AssetBundle;

class DhtmlxAsset extends AssetBundle
{
    public $basePath = '@vendor';
    public $baseUrl = '@assetsPath';
	
    public $css = [
		'dhtmlx/dhtmlx_telas_sistema.css',		
		'custom_scroll/customscroll.css',        
    ];
    public $js = [
		'dhtmlx/dhtmlx.js',
		'blockUI/jquery.blockUI.js',		
		'custom_scroll/customscroll.js',
		'jquery/lib.js',
    ];
   
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}