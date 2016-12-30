<?php

/* @var $this yii\web\View */
use backend\assets\DhtmlxAsset;
DhtmlxAsset::register($this);

$this->title = 'Cadastro de empresa';
?>

<div id="layoutObj" class="site-index" style="position: relative; top: 0px; left: 0px; right: 0px; width: 100%; height: 550px; border:0px;"></div>
<script>
    //dhtmlx.image_path='./codebase/imgs/';

    document.addEventListener("DOMContentLoaded", function(event) {
        objGlobal = {};
        var main_layout = new dhtmlXLayoutObject('layoutObj', '4I');

        var a = main_layout.cells('a');
        a.fixSize(true,true);
        a.hideArrow();
        a.setText('Dados da empresa');
        a.setHeight(100);
        var str = [
            { type:"settings" , labelWidth:80, inputWidth:250, position:"absolute"  },
            {type: "block", list: [
                { type:"input" , name:"name", label:"Nome:", labelWidth:250, labelLeft:5, labelTop:5, inputLeft:5, inputTop:21  },
                { type:"image" , name:"logo_url", label:"Logo:", labelWidth:250, labelLeft:275, labelTop:5, inputLeft:275, inputTop:21,imageWidth: 50, imageHeight: 50, url: "<?= \Yii::getAlias('@dhtmlxImg')."/default/close.png"?>"},
            ]},
            {type: "block", list: [
                { type:"button" , name:"form_button_1", label:"Salvar", value:"Salvar", width:"100", inputWidth:100, inputLeft:575, inputTop:25  }
            ]}
        ];

        var form_empresa = a.attachForm(str);



        var b = main_layout.cells('b');
        b.fixSize(true,true);
        b.hideArrow();
        b.setHeight(300);
        b.setText('Sessões da empresa');
        var grid_sessoes_vinculadas = b.attachGrid();
        grid_sessoes_vinculadas.setIconsPath('./codebase/imgs/');
        grid_sessoes_vinculadas.setHeader(["código","Sessão","Excluir"]);
        grid_sessoes_vinculadas.setColAlign('left,left,center');
        grid_sessoes_vinculadas.setColSorting('int,str,str');
        grid_sessoes_vinculadas.setColWidth(2, '60');
        grid_sessoes_vinculadas.init();
	grid_sessoes_vinculadas.load('./index.php?r=company/get-session');
        grid_sessoes_vinculadas.enableDragAndDrop(true);
        


        var c = main_layout.cells('c');
        c.fixSize(true,true);
        c.hideArrow();
        c.setHeight(300);
        c.setText("Sessões do sistema <button id='btnAddSession' onclick='objGlobal.adicionarSession()' class='button-right icon-adicionar' title='Adicionar sessão'></button>");
        var grid_sessoes = c.attachGrid();
        grid_sessoes.setIconsPath('./codebase/imgs/');
        grid_sessoes.setHeader(["Código","Sessão","Excluir"]);
        //grid_sessoes.setColTypes("ro,ed,img");

        grid_sessoes.setColSorting('int,str,str');
        grid_sessoes.attachEvent('onEditCell', function(stage,rId,cInd,nValue,oValue){
            if(stage == 2 && cInd == 1 && oValue != nValue){
                id_session = this.cells(rId,0).getValue();
                url = './index.php?r=session/save';
                param = '&id_session='+id_session+'&name='+nValue;
		dhtmlxAjax.post(url, param, function (a){
                    if(a.xmlDoc.status == 200){
                        retorno = JSON.parse(a.xmlDoc.response);
                        if(retorno.status){
                            // mensagem de sucesso
                            txtAlert = (id_session) ? "Sessão atualizada com sucesso.":"Sessão cadastrada com sucesso.";
                            dhtmlx.alert ({text:txtAlert});

                            // preenche o id da nova sessao se a mesma nao existir
                            if(retorno.msg && !id_session)
                                grid_sessoes.cells(rId,0).setValue(retorno.msg);
                            
                            return true;
                        }
                    }
                    
                    // mensagem de falha
                    txtAlert = (id_session) ? "Sessão não foi atualizada!":"Sessão não cadastrada!";
                    dhtmlx.alert ({text:txtAlert + " " + (retorno.msg || "")});
                    return false;
                    
		});
                return true;
            } else 
                return true;
        });
        grid_sessoes.init();
	var urlLoad = './index.php?r=session/get-data';
	grid_sessoes.load(urlLoad);
        grid_sessoes.enableDragAndDrop(true);
        
        
        
        var d = main_layout.cells('d');
        d.fixSize(true,true);
        d.hideHeader();
        d.hideArrow();
        d.setHeight(50);
        
        
        objGlobal.adicionarSession = function() {
            newId = grid_sessoes.uid();
            arrayNewRow = ['','','<?= \Yii::getAlias('@dhtmlxImg')."/default/close.png^Excluir^javascript:objGlobal.excluirSession('+ newId +')^_self"?>']; 		
            grid_sessoes.addRow(newId, arrayNewRow);
            grid_sessoes.selectRow(grid_sessoes.getRowIndex(newId),true,true,true);
        };
        
        objGlobal.excluirSession = function() {
            dhtmlx.confirm({
                ok:"Sim", cancel:"Não",
                text: "Deseja realmente excluir a sessão?" ,
                callback:function(excluir) {
                    if (excluir) {
                        registroSelecionado = grid_sessoes.getSelectedRowId();
                        id_session = grid_sessoes.cells(registroSelecionado,0).getValue();
                        if(!id_session){
                            grid_sessoes.deleteRow(registroSelecionado);
                            return true;
                        }
                        
                        url = './index.php?r=session/delete';
                        param = '&id_session='+id_session;
                        dhtmlxAjax.post(url, param, function (a){
                            if(a.xmlDoc.status == 200){
                                retorno = JSON.parse(a.xmlDoc.response);
                                if(retorno.status){
                                    // remove a linha do grid + mensagem de sucesso
                                    grid_sessoes.deleteRow(registroSelecionado);
                                    dhtmlx.alert ({text:"Sessão excluída!"});
                                    return true;
                                }
                            }
                            // mensagem de falha
                            dhtmlx.alert ({text: "Sessão não excluída!" + " " + (retorno.msg || "")});
                            return false;
                        });
                    }
                }
            });
        };

    });
</script>