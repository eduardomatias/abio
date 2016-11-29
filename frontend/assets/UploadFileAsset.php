<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class UploadFileAsset extends AssetBundle
{
    
    public $sourcePath = '@assetsPath/FileUpload';
    
    public $basePath = '@vendor';
    public $baseUrl = '@assetsPath/FileUpload';
	
    public $css = [
        'css/style.css',
        'css/jquery.fileupload.css',
        'css/jquery.fileupload-ui.css',
        'css/jquery.fileupload-noscript.css',
        'css/jquery.fileupload-ui-noscript.css',
    ];
    public $js = [
        'js/vendor/jquery.ui.widget.js',
        'js/jquery.iframe-transport.js',
        'js/jquery.fileupload.js',
        'js/jquery.fileupload-process.js',
        'js/jquery.fileupload-image.js',
        'js/jquery.fileupload-audio.js',
        'js/jquery.fileupload-video.js',
        'js/jquery.fileupload-validate.js',
        'js/jquery.fileupload-ui.js',
        'js/main.js',
        
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}