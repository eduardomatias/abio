<?php

/* @var $this yii\web\View */
use backend\assets\DhtmlxAsset;
DhtmlxAsset::register($this);

$this->title = 'Cadastro de empresa';
?>

<div id="layoutObj" class="site-index" style="position: relative; top: 0px; left: 0px; right: 0px; width: 100%; height: 530px; border:0px;"></div>
<iframe name="upload_area" frameBorder="0" height="0" style="display: none"></iframe>

<script>
    //dhtmlx.image_path='./codebase/imgs/';

    sendDataCallbackDefault = function(loader, response) {

        if (typeof response === 'undefined') {
            response = loader.xmlDoc.response;
        }

        response = JSON.parse(response);
        if (response.status) {

            if (typeof response.message === 'undefined' || response.message == '') {
                response.message = "Operação realizada com sucesso!";
            }

            dhtmlx.alert({text: response.message , ok: "ok", callback: function(){location.reload();}});

        } else {

            if (typeof response.message === 'undefined' || response.message == '') {
                response.message = "Erro ao realizar a operação.";
            }

            dhtmlx.alert({
                title: "Atenção!",
                type:"alert-error errorCustom",
                text: response.message,
            });
        }
    }
    
    
    document.addEventListener("DOMContentLoaded", function(event) {
        objGlobal = {};
        form_empresa = {};
        var main_layout = new dhtmlXLayoutObject('layoutObj', '3T');

        var a = main_layout.cells('a');
        a.fixSize(true,true);
        a.hideArrow();
        a.setText('Dados da empresa');
        a.setHeight(100);
        var formData = [
            { type:"settings" , labelWidth:80, inputWidth:250, position:"absolute"  },
            {type: "block", list: [
                { type:"input" , name:"name", label:"Nome:", labelWidth:250, labelLeft:5, labelTop:5, inputLeft:5, inputTop:21, required:true},
                { type:"file" , name:"CompanyModel[logo_url]", label:"Logo:", labelWidth:250, labelLeft:275, labelTop:5, inputLeft:275, inputTop:21, required:true},
                { type:"input" , hidden:true, name:"grid_sessions", required:true}
            ]},
            {type: "block", list: [
                { type:"button" , name:"salvar", label:"Salvar", value:"Salvar", width:"100", inputWidth:100, inputLeft:575, inputTop:25}
            ]}
        ];

        form_html = a.attachHTMLString('<form action="./index.php?r=company/save" target="upload_area" id="realForm" method="POST" enctype="multipart/form-data"><div id="dhxForm"></div></form>');
        var form_empresa = new dhtmlXForm("dhxForm", formData);
        form_empresa.attachEvent("onButtonClick", function(id) {
            switch (id) {
                case 'salvar':
                    
                    form_empresa.setItemValue('grid_sessions', '');

                    dataSerialize = grid_sessoes_vinculadas.serializeToJsonStrMMS();
                    if(dataSerialize == '[]'){
                        dhtmlx.alert({text: "Para cadastrar uma empresa é necessário vincular pelo menos uma sessão!", ok: "ok"});
                        return false;
                    }

                    // set dados da grid no form para enviar os dados
                    form_empresa.setItemValue('grid_sessions', dataSerialize);
                    
                    document.getElementById("realForm").submit();
                    
                    //url = './index.php?r=company/save';
                    //form_empresa.send(url, "post", form_empresa.sendDataCallbackDefault);
                    
                break;
            }
        });
        



        var b = main_layout.cells('b');
        b.fixSize(true,true);
        b.hideArrow();
        b.setHeight(380);
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
        c.setHeight(380);
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