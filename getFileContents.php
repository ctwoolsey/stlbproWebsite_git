<?
	$ssid = 0;
	if (isset($_REQUEST['ssid']) && ($_REQUEST['ssid'] != "0")){
		$ssid=$_REQUEST['ssid'];
		session_id($ssid);
		$ssidLink_continuing = "&ssid=".$ssid;
		$ssidLink_starting = "?ssid=".$ssid;
	}
	session_start();
	$vpc = new vendorsClass('vendorList.xml');
	$username = "";
	$isAdmin = false;
	$vendor = NULL;
	
	if (isset($_SESSION['username'])){
		$username = $_SESSION['username'];
		$vendor = $vpc->findVendorWithEmail($username);
		if (!is_null($vendor)){
			$isAdmin = $vendor->isAdmin();
		}
	}
	
	if ($isAdmin){
		$fileToGet = $_REQUEST['fileToGet'];
		//$fileContents = urlencode(file_get_contents($fileToGet));
		$fileContents = file_get_contents($fileToGet);
		echo $fileContents;
		//echo json_encode($fileContents);
	}else{
		echo "You don't have permission to do this.";
	}
?>