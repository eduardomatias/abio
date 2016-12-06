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
$upload_handler = new UploadHandler();

/*
require('UploadHandler.php');
class CustomUploadHandler extends UploadHandler {
    protected function trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range) {
            $name = 'First_' . microtime(true);
            $name = str_replace('.', '', $name);
            return uniqid(). ".pdf";
        }
}

$upload_handler = new CustomUploadHandler();