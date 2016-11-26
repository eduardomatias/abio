<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\DhtmlxAsset;
  
use yii\helpers\Html;

DhtmlxAsset::register($this);

?>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
	<noscript>
		<meta http-equiv="Refresh" content="1;erroJavascript.php">
	</noscript>
	
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php $this->endBody() ?>
<script>

	<?php
	/*
	Referência do script "layout toolbar+grid ":

	Todo o script desenvolvido, deve ser armazenado dentro do objeto SYSTEM.

	Para inicializar a tela utilize o comando:
	* SYSTEM.boot();


	Para acessar os objetos renderizados basta utilizar os seguintes caminhos dentro do objeto:
	Layout:
	* SYSTEM.Layout.outerLayout => para o layout externo onde se renderiza a toolbar e o InnerLayout.
	* SYSTEM.Layout.innerLayout => para o layout interno, onde é renderizado o filtro ( cell id = 'a' ) e o grid ( cell id = 'b' ) 
	* SYSTEM.Layout.innerLayout.tela => para a celula principal (unica celula).


	Toolbar:
	* SYSTEM.Toolbar.core => para o objeto DHTMLx da toolbar

	Ferramentas do SYSTEM:

	Layout:
	* SYSTEM.Layout.t1("string") => para mudar o titulo da primeira e unica celula.

	Toolbar:
	* SYSTEM.Toolbar.icones( [icondeId1,iconeid2,...] ) => mostra os icones cujo ids estão na array passada como parâmetro 
	A lista de icones disponível está descrita no aquivo dhxtoolbar.xml que se encontra em /libs/layoutMask/dhxtoolbar.xml
	* SYSTEM.Toolbar.titulo('teste') => modifica o titulo da toolbar

	*/
	?>
    
    

	var SYSTEM = (function(){

				//Preloader
		        $.blockUI.defaults.css.border =  'none';
				$.blockUI.defaults.css.padding = '0px';
				$.blockUI.defaults.css.textAlign = 'center';
				$.blockUI.defaults.css.backgroundColor =  'rgba(8, 4, 4, 1))';
				$.blockUI.defaults.css.opacity=  .3;

				// Preload mensagem padrão da tela de bloqueio	
				$.blockUI.defaults.message =  '<img src="<?=\Yii::getAlias('@assetsPath');?>/layoutMask/loading.svg">';
        
				// aciona o bloquio de tela quando inicia o ajax
				//remove o bloqueio de tela quando acaba o ajax
				$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

		var cesta = {
			
		};
		
		cesta.boot = function(){
			SYSTEM.Layout = loadLayout();
			loadGrid();
			SYSTEM.Toolbar  = loadToolbar();
		}

		return cesta;
	})();

	function loadLayout(){
		var outerLayout = new dhtmlXLayoutObject(document.body, "1C");
		var innerLayout = outerLayout.cells("a").attachLayout("1C");
			
		outerLayout.cells("a").hideHeader();
		innerLayout.cells("a").hideHeader();
		
		
		
		return{
			innerLayout: innerLayout,
			outerLayout: outerLayout,
			tela: innerLayout.cells("a"),
			t1: function(titulo){
				innerLayout.cells("a").showHeader();
				innerLayout.cells("a").setText(titulo);
			}
		}
	}


	function loadToolbar(){
		var toolbar = SYSTEM.Layout.outerLayout.cells("a").attachToolbar();
		toolbar.setIconsPath("<?=\Yii::getAlias('@assetsPath');?>/layoutMask/imgs/");
		toolbar.loadXML("<?=\Yii::getAlias('@assetsPath');?>/layoutMask/dhxtoolbar.xml?etc=" + new Date().getTime());
		toolbar.attachEvent("onXLE", function(){
			toolbar.addSpacer("titulo");
			toolbar.forEachItem(function(itemId){
				toolbar.hideItem(itemId);
			});
		});
		return {
			core: toolbar,
			icones: function(iconsIds){
				setTimeout(function(){ 
					for(var i = 0; iconsIds.length > i ;i++){
						toolbar.showItem(iconsIds[i]);
					}
				}, 1000);
			},
			titulo: function (titulo){
	            setTimeout(function(){
					toolbar.showItem('titulo');
					toolbar.setItemText('titulo', titulo);
				}, 1000);
			}
		}
	}

	function loadGrid(){
		SYSTEM.Grid = SYSTEM.Layout.innerLayout.cells("a").attachGrid();
		SYSTEM.Grid.enableRowsHover(true,'hover');
	}


</script>
<?= $content ?>
</body>
</html>
<?php $this->endPage() ?>

