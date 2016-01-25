<?
	include_once('deleteOldVendorImages.php');
	$uid = $_REQUEST['uid'];
	$dir = $_REQUEST['dir'];
	$maxSize = $_REQUEST['maxSize'];
	$permissions = 0755;
	$fileNameOnly = "";
	
	//$imageName = $uid."_".$dir;
	$imageName = $uid."_".$dir."_".$maxSize;
	
	if (!is_dir('images/'.$dir))
		mkdir('images/'.$dir,0755);
	chmod('images/'.$dir,0755);
	
	if (!is_dir('images/'.$dir.'/src'))
		mkdir('images/'.$dir.'/src',0755);
	chmod('images/'.$dir.'/src',0755);
	
	$files = array_diff(scandir('images/uploadedImages'), array('..', '.'));
	$found = false;
	
	foreach($files as $fileName){
		//echo "looking for: ".$imageName." in file: ".$fileName;
		if (strpos($fileName,$imageName) !== false){
			//echo " -- matched\n";
			$newFileName = renameAndMove($fileName,$uid,$dir,$permissions);
			if ($newFileName !== false){
				resizeImage($newFileName,$maxSize);
				$path_parts = pathinfo($newFileName);
				$fileNameOnly = $path_parts['basename'];
				$found = true;
			}
			break;
		}else{
			//echo " -- NOT matched\n";
		}
	}
	
	$results = ['found'=>$found,'fileName'=>$fileNameOnly];
	echo json_encode($results);
	
	
	/*********************functions*******************/
	
	function renameAndMove($fileName,$uid,$dir,$permissions){
		$returnValue = false;
		//echo 'rename("images/uploadedImages/'.$fileName.','.$newSRCFileName.');\n';
		if (file_exists('images/uploadedImages/'.$fileName)){
			$path_parts = pathinfo('images/uploadedImages/'.$fileName);
			$newSRCFileName = 'images/'.$dir.'/src/'.$uid.'.'.$path_parts['extension'];
			rename('images/uploadedImages/'.$fileName,$newSRCFileName);
			chmod($newSRCFileName,$permissions);
			$newFileName = 'images/'.$dir.'/'.$uid.'.'.$path_parts['extension'];
			copy($newSRCFileName,$newFileName);
			chmod($newFileName,$permissions);
			deleteOldVendorImages($newFileName);
			$returnValue = $newFileName;
			
		}
		return ($returnValue);
	}
	
	function resizeImage($filePath,$maxSize){
		//resize image
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
				imagecopyresampled($newImage, $srcImage, 0, 0, 0, 0, $newWidth,$newHeight,$orgWidth,$orgHeight);
		
				imagedestroy($srcImage);
			
				$unlinked = true;
				if (!unlink($filePath))
						$unlinked = false;
						
				if ($unlinked)
				{
					if( $image_type == IMAGETYPE_JPEG ) { 
						imagejpeg($newImage,$filePath,75); 
					} elseif( $image_type == IMAGETYPE_GIF ) {   
						imagegif($newImage,$filePath); 
					} elseif( $image_type == IMAGETYPE_PNG ) {   
						imagepng($newImage,$filePath); 
					} 
					
							
					if( $permissions != null) {   
						chmod($filePath,$permissions);
					}
			
				}
				imagedestroy($newImage);
			  }//end if resize
		} //end if valid file type	
	}

?>