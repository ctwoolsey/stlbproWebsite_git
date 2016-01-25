<?
	function deleteOldVendorImages($filePath){
		//echo 'deleting: '.$filePath."\n";
		$path_parts = pathinfo($filePath);
		$path = $path_parts['dirname'];
		$fileNameOnly = $path_parts['filename'];
		$extension = $path_parts['extension'];
		deleteOthers($path.'/',$fileNameOnly,$extension);
		//now src dir
		deleteOthers($path.'/src/',$fileNameOnly,$extension);
	}
	
	function deleteOthers($path,$fileNameOnly,$fileExtension){
		$files = array_diff(scandir($path), array('..', '.'));
		$found = false;
		
		foreach($files as $fileName){
			$path_parts = pathinfo($fileName);
			$otherFileNameOnly = $path_parts['filename'];
			//echo "comparing: ".$otherFileNameOnly." with: ".$fileNameOnly." where ext: ".$path_parts['extension']." and keep ext: ".$fileExtension."\n";
			if (($otherFileNameOnly == $fileNameOnly) && ($path_parts['extension'] !=  $fileExtension))
				unlink($path.$fileName);
		}
	}


?>