<!-- Force latest IE rendering engine or ChromeFrame if installed -->
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<![endif]-->
<meta charset="utf-8">
<!-- Bootstrap styles -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<!-- Generic page styles -->
<!-- blueimp Gallery styles -->
<link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="css/jquery.fileupload.css">
<link rel="stylesheet" href="css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="css/jquery.fileupload-ui-noscript.css"></noscript>
<link rel="stylesheet" href="js/vendor/datepicker/datepicker3.css">
<link rel="stylesheet" href="css/style.css?aasd=asd45255">

<div class="container">
    <!-- The file upload form used as target for the file upload widget -->
    <form id="fileupload" action="" method="POST" enctype="multipart/form-data" onsubmit="return testeData();">
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <span class="publishDate">
                    <label>Data: <label>
                        <input type="text" name="data" id="dataJournal" required style="width: 140px;" maxlength="10" >
                </span>
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Adicionar arquivos...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button id="sendFiles" type="submit" class="btn btn-primary start" onclick="alerta = false;">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Enviar arquivos</span>
                </button>
                <!--
                <button type="reset" class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel upload</span>
                </button>
                -->
<!--                <button id="delete" type="button" class="btn btn-danger delete">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Excluir</span>
                </button>-->
               <!--<input type="checkbox" class="toggle">-->
                <!-- The global file processing state -->
                <span class="fileupload-process"></span>
            </div>
            <!-- The global progress state -->
<!--            <div class="col-lg-5 fileupload-progress fade">
                 The global progress bar 
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                 The extended global progress state 
                <div class="progress-extended">&nbsp;</div>
            </div>-->
        </div>
        <!-- The table listing the files available for upload/download -->
        <table id="tablejornais" role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
        
    </form>
    <br>

</div>
<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name journalName">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td class="title" id="td-caderno">
            <label>Caderno: 
                <select filled="false" name="caderno" class="caderno" required>                   
                </select>
            </label>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled style="display:none">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade" style="display:none">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="js/vendor/datepicker/bootstrap-datepicker.js"></script>
<!-- blueimp Gallery script -->
<script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="js/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->

<script>
$(document).ready(function(){

                    $.ajax({
                        type: "GET",
                        url: "../../frontend/web/index.php?r=caderno-edicoes/get-session-by-company-logged", // substitua por qualquer URL real
                        dataType: 'json'
                    }).done(function (r) {
                       console.log(r);
                       window.options = r;
                       
                       $('#fileupload').on('focus','select[filled="false"]', function(){
                            console.log(this)
                            $(this).empty();
                            for (i in r) {
                                $(this).append('<option value="'+ i +'">'+r[i]+'</option>'); 
                            }
                            
                            $(this).attr('filled', 'true');
                            
                        }) 
                       //opts = JSON.stringify(r);
                       //localStorage.setItem('options', opts);
                       	var $secondChoice = $("#second-choice");
                        
                        $('#template-download').on('change', "select", function (e) {
                            this.empty();
                            $.each(r, function(index, value) {
                                    this.append("<option>" + value + "</option>");
                            });
                        });
                    });
                
                
            $("#dataJournal").datepicker({autoclose: true, format: 'dd/mm/yyyy', language: "pt-BR", todayHighlight: true});
            $("#dataJournal").datepicker('show');
            
          $('#sendFiles').click(function(){              
                executed = false;
                
                $('select[name="caderno"]').each(function(i){
                    if ($(this).val() == '' && executed == false) {
                       parent.dhtmlx.message({
                            title: "Atenção",
                            type: "alert-warning",
                            text: "Todos os cadernos precisam estar selecionados antes de enviar.",
                        });
                        executed = true
                    }
                
            
            });
        });
            
    $('.fileinput-button').click(function(){   
        localStorage.setItem('validateRun','false');
        var date = $('input#dataJournal').val();
            if (date == '' || date == 'undefined') {
                 parent.dhtmlx.message({
                    title: "Atenção",
                    type: "alert-warning",
                    text: "Selecione uma data antes de adicionar o jornal.",
                });
                $("#dataJournal").datepicker('show');
                return false;                        
            }
    });  
        
        var requestSent = false;
            // validar data do jornal
            $('input#dataJournal').change(function(){                
                date = $(this).val();                
                 if(!requestSent) {
                     requestSent = true;
                    $.ajax({
                        type: "POST",
                        url: "../../frontend/web/index.php?r=caderno-edicoes/data-journal", // substitua por qualquer URL real
                        data: {dt:date },
                        dataType: 'text'
                    }).done(function (r) {
                        if(r == 'existe'){
                           $('input#dataJournal').val('');
                            parent.dhtmlx.message({
                                    title: "Atenção",
                                    type: "alert-warning",
                                    text: "A data selecionada já possui jornal cadastrado. escolha uma nova data ou exclua o jornal cadastrado anteriormente na listagem de jornais.",
                                });
                            requestSent = false;
                            $("#dataJournal").datepicker('show');
                        } 
                        else{
                            requestSent = false;
                           $('input#dataJournal').val(date);
                       }
                    });
                }

            });

});


$.widget('blueimp.fileupload', $.blueimp.fileupload, {

    options: {
        acceptFileTypes: /(\.|\/)(pdf)$/i,
    },

    processActions: {

        validate: function (data, options) {
            if (options.disabled) {
                return data;
            }
            
            var dfd = $.Deferred(),
                file = data.files[data.index];
                
        /* ---------------------- Validando se o ja foi inserido um caderno com o memso nome ----------------------*/
            if ( $('#tablejornais > tbody > tr:visible .journalName').length > 1) { 
                duplicate = [];
                $('#tablejornais > tbody > tr:visible .journalName').each(function(k,v){
                  if (file.name == $(this).html()){
                       duplicate.push($(this).closest('tr'))
                  }
                });
                if (duplicate.length > 1) {
                    if (localStorage.getItem('alertDuplicate') === null) {
                     parent.dhtmlx.message({
                                title: "Atenção",
                                type: "alert-warning",
                                text: "Não é permitido fazer upload de 2 arquivos com o mesmo nome.",
                        });
                    }
                    localStorage.setItem('alertDuplicate', false);
                    lastTr = duplicate.length -1;
                    duplicate[lastTr].remove();
                }
            }
            /* -----------------------------------------------------------------------------------------------------*/
            
            
            
           /* ---------------------- Validando se o arquivo é do formato pdf --------------------------*/ 
            if (!options.acceptFileTypes.test(file.type)) {
                validateRun = localStorage.getItem('validateRun');
                if (validateRun == 'false') {
                    parent.dhtmlx.message({
                            title: "Atenção",
                            type: "alert-warning",
                            text: "São permitidos apenas arquivos no formato PDF.",
                    });
                    $('.cancel').closest('tr').remove()
                }
                localStorage.setItem('validateRun', 'true');
                file.error = 'Invalid file type.';
                dfd.rejectWith(this, [data]);
                validateRun = true;
            } else {
                dfd.resolveWith(this, [data]);
            }
            /* ----------------------------------------------------------------------------------------*/ 
            
            return dfd.promise();
        }

    }

});
    var alerta = false;
    
     $('#fileupload').bind('fileuploadstop', function (e, data) {         
        parent.parent.W.uploadCaderno.close();
        parent.parent.gridJournal.recarregaGrid();
     });
    $('#fileupload').bind('fileuploadsubmit', function (e, data) {
        var inputs = data.context.find(':input');
        
        // Testa se o caderno foi preenchido
        if (inputs.filter(function () {
                return !this.value && $(this).prop('required');
            }).first().focus().length) {
            data.context.find('button').prop('disabled', false);
            return false;
        }
                
        // testa caderno repetido
        var cadernosSelecionados = [];
        $('select.caderno').each(function(index) {
            if(jQuery.inArray($(this).val(),cadernosSelecionados)) {
                cadernosSelecionados.push($(this).val());
            } else {
                data.context.find('button').prop('disabled', false);
                if(!alerta){
                    parent.dhtmlx.message({
                        title: "Atenção",
                        type: "alert-warning",
                        text: "Não é possível cadastrar um caderno mais de uma vez para o mesmo jornal.",
                    });
                    
                    alerta = true;
                }
                $(this).focus();
                return false;
            }
        });
        
        // testa data do jornal
        var dataJournal = $('input#dataJournal').val();
        if(!dataJournal){
            data.context.find('button').prop('disabled', false);
           $('input#dataJournal').focus();
            return false;
        }
        
        data.formData = inputs.serializeArray();
        data.formData[0].fileName = data.files[0].name;
        data.formData[0].dataJournal = dataJournal;

        parent.parent.W.processaPDF(data.formData);
        
      
        
    });
      //Date picker
//    $('#dataJournal').datepicker('show').on('changeDate', function(ev){
//        $('#dataJournal').datepicker({autoclose:true});
//       // alert(ev.date.valueOf());
//    });
           // .datepicker("update", new Date())
    
    
    


//$("#dataJournal2").datepicker();
</script>
