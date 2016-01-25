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
		if (($_REQUEST['email'] != "") && ($_REQUEST['bizName'] != "")){
			$newVendor = new vendorProfileClass();
			$newVendor->email =	urldecode($_REQUEST['email']);
			$newVendor->name= urldecode($_REQUEST['bizName']);
			$newVendor->vendorType= urldecode($_REQUEST['vendorType']);
			$newVendor->vendorMembershipType= urldecode($_REQUEST['vendorMembershipType']);
	//		print_r($newVendor);
			$newVendor->save();
			//$vpc = new vendorsClass('vendorList.xml');
			//echo "\nprinting\n";
			//print_r($vpc);
			$createdVendor = ["id"=>$newVendor->id,"email"=>$newVendor->email];
			echo json_encode($createdVendor);
		}
	}else{
		echo json_encode(false);
	}
?>