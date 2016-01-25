<?
	$ssid = 0;
	if (isset($_REQUEST['ssid']) && ($_REQUEST['ssid'] != "0")){
		$ssid=$_REQUEST['ssid'];
		session_id($ssid);
		$ssidLink_continuing = "&ssid=".$ssid;
		$ssidLink_starting = "?ssid=".$ssid;
	}
	session_start();
	include_once('vendorsClass.php');
	$vpc = new vendorsClass('vendorList.xml');
	$username = "";
	$success = false;
	$vendor = NULL;
	
	$reasonArray = "";
	if (isset($_SESSION['username'])){
		$username = $_SESSION['username'];
		$vendor = $vpc->findVendorWithEmail($username);
		if (!is_null($vendor)){
			$success = true;
			$reasonArray = explode(";",$vendor->reasonNoPost);
		}
	}
	
	echo json_encode(["success"=>$success,"reasons"=>$reasonArray]);	
	
	
?>