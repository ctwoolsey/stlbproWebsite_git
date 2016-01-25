<?
	if (isset($_REQUEST['ssid']) && ($_REQUEST['ssid'] != "0")){
		$ssid=$_REQUEST['ssid'];
		session_id($ssid);
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
		if (!is_null($vendor))
			$isAdmin = $vendor->isAdmin();
	}
	
	if ($isAdmin){
		if ($_REQUEST['vendorID'] != ""){
			$success = $vpc->removeVendor($_REQUEST['vendorID']);
			
			//print_r($vpc);
			echo json_encode($success);
		}
	}else{
		echo json_encode(false);
	}
?>