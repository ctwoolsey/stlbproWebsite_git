<?php
/**
 * upload.php
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

#!! IMPORTANT: 
#!! this file is just an example, it doesn't incorporate any security checks and 
#!! is not recommended to be used in production environment as it is. Be sure to 
#!! revise it and customize to your needs.

// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* 
// Support CORS
header("Access-Control-Allow-Origin: *");
// other CORS headers if any...
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	exit; // finish preflight CORS requests here
}
*/

// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Settings
$targetDir = urldecode($_REQUEST["folder"]);
$targetDir = trim($targetDir,"/");
$srcDir = $targetDir . DIRECTORY_SEPARATOR . "src";


$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds
$permissions = 0777;


// Create target dir & SRC dir
if (!file_exists($targetDir)) {
	@mkdir($targetDir,0777,true);
}
if (!file_exists($srcDir)) {
	@mkdir($srcDir,0777,true);
}


// Get a file name
if (isset($_REQUEST["name"])) {
	$fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
	$fileName = $_FILES["file"]["name"];
} else {
	$fileName = uniqid("file_");
}

$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

// Remove old temp files	
if ($cleanupTargetDir) {
	if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		$uploadResult = ['uploaded'=>false,'code'=>100,'message'=>'Failed to open temp directory.'];
		die(json_encode($uploadResult));
		//note that the suggested code below does not trigger the Error event in the code
		//die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// If temp file is current file proceed to the next
		if ($tmpfilePath == "{$filePath}.part") {
			continue;
		}

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
			@unlink($tmpfilePath);
		}
	}
	closedir($dir);
}	

// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
	$uploadResult = ['uploaded'=>false,'code'=>102,'message'=>'Failed to open output stream.'];
	die(json_encode($uploadResult));
	//die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
	if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
		$uploadResult = ['uploaded'=>false,'code'=>103,'message'=>'Failed to move uploaded file.'];
		die(json_encode($uploadResult));
		//die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
		$uploadResult = ['uploaded'=>false,'code'=>101,'message'=>'Failed to open input stream.'];
		die(json_encode($uploadResult));
		//die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
} else {	
	if (!$in = @fopen("php://input", "rb")) {
		$uploadResult = ['uploaded'=>false,'code'=>101,'message'=>'Failed to open input stream.'];
		die(json_encode($uploadResult));
		//die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
}

while ($buff = fread($in, 4096)) {
	fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
	// Strip the temp .part suffix off 
	rename("{$filePath}.part", $filePath);
	$extension = pathinfo($filePath);
	$extension = $extension['extension'];
	rename($filePath, $targetDir . DIRECTORY_SEPARATOR . $_REQUEST['fileName'].".".$extension);
	$filePath = $targetDir . DIRECTORY_SEPARATOR . $_REQUEST['fileName'].".".$extension;
	$storeOriginalFilePath = $srcDir . DIRECTORY_SEPARATOR. $_REQUEST['fileName'].".".$extension;
	
	//copy the original to a SRC folder, check to see if exists first
	$unlinkedNewFile = true;
	
	if ((file_exists($storeOriginalFilePath)) && (!unlink($storeOriginalFilePath))){
		$unlinkedNewFile = false;
	}
	
	if ($unlinkedNewFile)
		copy($filePath,$storeOriginalFilePath);

	//resize image
	$maxSize = $_REQUEST['size'];
	$resize = true;
	$image_info = getimagesize($filePath); 
	$image_type = $image_info[2]; 
	$srcImage = NULL;
	$validFileType = true;

	if( $image_type == IMAGETYPE_JPEG ) { 
		$srcImage = imagecreatefromjpeg($filePath); 
	} elseif( $image_type == IMAGETYPE_GIF ) {   
		$srcImage = imagecreatefromgif($filePath); 
	} elseif( $image_type == IMAGETYPE_PNG ) {   
		$srcImage = imagecreatefrompng($filePath); 
	} else
		$validFileType = false;

	if ($validFileType){
		$orgWidth = imagesx($srcImage);
		$orgHeight = imagesy($srcImage);
	
		$newWidth = 0;
		$newHeight = 0;
				
		if ($orgWidth > $orgHeight)
		{
			$resize = ($orgWidth > $maxSize)?true:false;
			$newWidth = $maxSize;
			$newHeight = $newWidth*($orgHeight/$orgWidth);
		}
		else
		{
			$resize = ($orgHeight > $maxSize)?true:false;
			$newHeight = $maxSize;
			$newWidth = $newHeight*($orgWidth/$orgHeight);
		}		
		
		$newImage = NULL;
		if ($resize)
		{
			$newImage = imagecreatetruecolor($newWidth,$newHeight);
			if($image_type == IMAGETYPE_GIF or $image_type == IMAGETYPE_PNG){
				imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
				imagealphablending($newImage, false);
				imagesavealpha($newImage, true);
   		    }
			imagecopyresampled($newImage, $srcImage, 0, 0, 0, 0, $newWidth,$newHeight,$orgWidth,$orgHeight);
	//	}
	//	else{
	//		$newImage = imagecreatetruecolor($orgWidth,$orgHeight);
	//		imagecopyresampled($newImage, $srcImage, 0, 0, 0, 0, $orgWidth,$orgHeight,$orgWidth,$orgHeight);
	//		$newWidth = $orgWidth;
	//		$newHeight = $orgHeight;	
	//	}
	
		imagedestroy($srcImage);
	
		$unlinked = true;
		if (!unlink($filePath))
				$unlinked = false;
				
		if ($unlinked)
		{
			if( $image_type == IMAGETYPE_JPEG ) {
				//echo "jpeg";
				imagejpeg($newImage,$filePath,75); 
			} elseif( $image_type == IMAGETYPE_GIF ) {   
				//echo "gif";
				imagegif($newImage,$filePath); 
			} elseif( $image_type == IMAGETYPE_PNG ) {   
				//echo "png";
				imagepng($newImage,$filePath); 
			} 
			
					
			if( $permissions != null) {   
				chmod($filePath,$permissions);
				chmod($storeOriginalFilePath,$permissions); 
			}
	
		}
		imagedestroy($newImage);
	  }//end if resize
	} //end if valid file type
}

include_once('deleteOldVendorImages.php');
deleteOldVendorImages($filePath);


// Return Success JSON-RPC response
$uploadResult = ['uploaded'=>true,'filePath'=>$filePath];
die(json_encode($uploadResult));
//die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
?>