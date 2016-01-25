<?php
ini_set('include_path',ini_get('include_path').':/home/dvaqpvvw/php/:'); 
ini_set('include_path',ini_get('include_path').':/home/dvaqpvvw/php/MIME/:');
ini_set('include_path',ini_get('include_path').':/home/dvaqpvvw/php/PEAR/:');
require_once '/home/dvaqpvvw/php/MIME/Type.php';

 $filename = urldecode($_REQUEST['filename']);
 $shortFilename = urldecode($_REQUEST['shortFilename']);
 $mimeType = MIME_Type::autoDetect($filename);
 header("Content-disposition: attachment; filename=".$shortFilename);
 header("Content-type:".$mimeType);
 readfile($filename);
 
 
 function getMimeType(){
	 
 }
?>