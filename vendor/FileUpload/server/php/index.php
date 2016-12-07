<?php
/*
 * jQuery File Upload Plugin PHP Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');
//$upload_handler = new UploadHandler();

class CustomUploadHandler extends UploadHandler {
    protected function trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range) {
        $extension = pathinfo($name , PATHINFO_EXTENSION);
        $extension = ($extension) ? "." . $extension : ".pdf";
        
        $newFileName = uniqid() . $extension;
        $caderno     = $this->get_post_param('caderno');
        $dataJournal = $this->get_post_param('dateJournal');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://localhost/abio/frontend/web/index.php?r=caderno-edicoes/processa-caderno");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "file=".$newFileName."&dt=".$dataJournal."&tp=".$caderno);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        
        /*
        $content = http_build_query(array(
            'file' => $newFileName,
            'dt' => $dataJournal,
            'tp' => $caderno,
        ));

        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'content' => $content,
            )
        ));

        $result = file_get_contents('http://localhost/abio/frontend/web/index.php?r=caderno-edicoes/processa-caderno', null, $context);
        
        */
        
        
        
        return $newFileName;
    }
}

$upload_handler = new CustomUploadHandler();