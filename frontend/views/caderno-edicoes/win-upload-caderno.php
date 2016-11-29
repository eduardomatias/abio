<?php

use frontend\assets\DhtmlxAsset;
DhtmlxAsset::register($this);

$this->title = '';
$token = Yii::$app->request->getCsrfToken();


?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) { 
        window.testeLayout = new dhtmlXLayoutObject("layoutObj", "1C");
	window.testeLayout.cells("a").hideHeader();
        window.testeLayout.cells('a').attachURL("../../vendor/FileUpload/index.php");
    });
</script>

<div id="layoutObj" style="position: relative; top: 0px; left: 0px; right: 0px; width: 100%; height: 350px; border:0px;"></div>