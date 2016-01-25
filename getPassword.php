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
	$isAdmin = false;
	$vendor = NULL;
	
	if (isset($_SESSION['username'])){
		$username = $_SESSION['username'];
		$vendor = $vpc->findVendorWithEmail($username);
		if (!is_null($vendor)){
			echo $vendor->password;
		}else
			echo "";
	}
	
?>