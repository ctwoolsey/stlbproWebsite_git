<?
	if (isset($_REQUEST['fName'])){
		$fnamePortion = $_REQUEST['fName'];
		
		$files = array_diff(scandir('images/uploadedImages'), array('..', '.'));
		
		foreach($files as $fileName){
			if (strpos($fileName,$fnamePortion) !== false){
				unlink('images/uploadedImages/'.$fileName);
				break;
			}
		}
	
	}
	
	
?>