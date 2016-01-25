<?
	include_once('vendorsClass.php');
	
	$vC = new vendorsClass();
	$vendor = $vC->getVendorByID($_REQUEST['id']);
	/*echo "vendor\n";
	print_r($vendor);
	echo "\n*******\n";*/
	
	echo json_encode($vendor->isAdmin());
?>