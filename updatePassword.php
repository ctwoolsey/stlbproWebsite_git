<?
	if (isset($_REQUEST['ssid']) && ($_REQUEST['ssid'] != "0")){
		$ssid=$_REQUEST['ssid'];
		session_id($ssid);
	}
	session_start();
	
	if (isset($_SESSION['username'])){
		include_once('vendorsClass.php');
		
		$vpc = new vendorsClass('vendorList.xml');
		$username = $_SESSION['username'];
		$vendor = $vpc->findVendorWithEmail($username);
		if (!is_null($vendor)){	
			$vendor->password = $_REQUEST['password'];
			$vendor->save();
			echo json_encode(true);
		}else
			echo json_encode(false);
	
	}else{
		echo json_encode(false);
	}
	
	
?>