<?
	if (isset($_REQUEST['ssid']) && ($_REQUEST['ssid'] != "0")){
		$ssid=$_REQUEST['ssid'];
		session_id($ssid);
	}
	session_start();
	
	if (isset($_SESSION['username'])){
		include_once('vendorsClass.php');
		
		$vC = new vendorsClass();
		$vendor = $vC->getVendorByID($_REQUEST['id']);
		$vendor->setAdminStatus($_REQUEST['adminStatus']);
		$vendor->save();
		echo json_encode(true);
	}else{
		echo json_encode(false);
	}
	
	
?>