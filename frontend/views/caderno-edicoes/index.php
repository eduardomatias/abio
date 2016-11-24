<?php
/* @var $this yii\web\View */
use frontend\assets\DhtmlxAsset;
DhtmlxAsset::register($this);
$this->title = 'Caderno de edições';
?>

<input type="button" value="upload do caderno" onclick="W.enviaCanerno()" />

<script>
    W = function(){};
    W.uploadCaderno = {};
    var gridJournal;
    
    W.uploadCaderno.init = function() {
        WindowsDhtmlx = new dhtmlXWindows();
        W.uploadCaderno.window = WindowsDhtmlx.createWindow("uploadCaderno", 0,0, 800, 525);
        W.uploadCaderno.window.button('minmax1').hide();
        W.uploadCaderno.window.button('park').hide();
        W.uploadCaderno.window.denyResize();
        W.uploadCaderno.window.center();
        W.uploadCaderno.close();
        W.uploadCaderno.window.attachEvent("onClose", function(win){
            W.uploadCaderno.close();
        });
    };
    W.uploadCaderno.close = function() {
        W.uploadCaderno.window.hide();
        W.uploadCaderno.window.setModal(false);
    };
    W.uploadCaderno.show = function() {
        W.uploadCaderno.window.show();
        W.uploadCaderno.window.setModal(true);
    };
    
    W.processaPDF = function(data) {
        console.log(data);
        
        file = data[0].fileName;
        dt = data[0].dataJournal;
        tp = data[0].value;
        
        url = 'index.php?r=caderno-edicoes/processa-caderno';
        params = 'file='+file+'&dt='+dt+'&tp='+tp;
        dhtmlxAjax.post(url, params, function (a){
            if(a.xmlDoc.status === 200){
                dhtmlx.alert ({
                    text:"As alterações foram salvas!", 
                    callback: function(){
                        W.uploadCaderno.close();
                        gridJournal.recarregaGrid();
                    }
                });
            }
        });
        
    };
    
    W.enviaCanerno = function() {
        W.uploadCaderno.window.setText('Enviar Cadernos');
        W.uploadCaderno.window.attachURL('index.php?r=caderno-edicoes/win-upload-caderno');
        W.uploadCaderno.show();
    }
	
	var validarDataJournal = function (dt){
		url = 'index.php?r=caderno-edicoes/data-journal';
		params = 'dt='+dt;
		dhtmlxAjax.post(url, params, function (a){
			return (a.xmlDoc.response != 'existe');
		});
	}
    
    document.addEventListener("DOMContentLoaded", function(event) {
        
        window.testeLayout = new dhtmlXLayoutObject("layoutObj", "1C");
	window.testeLayout.cells("a").setText('Jornais cadastrados');
			
	gridJournal = window.testeLayout.cells("a").attachGrid();
        gridJournal.setHeader("Data,Jornal,Usuário,Excluir");
        gridJournal.setInitWidths("100,*,100,100");
        gridJournal.setColAlign("center,left,left,center");
        gridJournal.setColTypes("ro,ro,ro,img");
        gridJournal.init();
        gridJournal.recarregaGrid = function() {
            //gridJournal.load('index.php?r=caderno-edicoes/grid-journal');
        }
        gridJournal.recarregaGrid();
        
        W.uploadCaderno.init();
    });
    
</script>

<div id="layoutObj" style="position: relative; top: 0px; left: 0px; right: 0px; width: 100%!important; height: 350px;"></div>
