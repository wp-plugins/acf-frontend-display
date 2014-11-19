<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);
require('upload.class.php');
require_once("../../../../../../../wp-load.php");

$fileSubfolder = $_GET['type'];
if($fileSubfolder == ''){
    $fileSubfolder = 'uigen';
}
//$fileSubfolder = 'fuck';
$upload_dir = wp_upload_dir();


$uigen_upload_args = array(
	'subfolder' => $fileSubfolder,
	'upload_main_url' => $upload_dir['baseurl'],
	'upload_main_dir' => $upload_dir['basedir']
	);


$upload_handler = new UploadHandler($uigen_upload_args);
