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
	include_once('scripts/validationClass/validator.php');
	$vendorObj = unserialize($_REQUEST['vendorObj']);
	$vpc = new vendorsClass('vendorList.xml');
	$username = "";
	$isAdmin = false;
	$vendor = NULL;
	$currentUser_id = "";
	$validToEdit = false;
	$saved = false;
	
	//print_r($vendorObj);
	if (isset($_SESSION['username']) && (!is_null($vendorObj))){
		$username = $_SESSION['username'];
		$vendor = $vpc->findVendorWithEmail($username);
		if (!is_null($vendor)){
			$isAdmin = $vendor->isAdmin();
			$currentUser_id = $vendor->id;
		}
	
		if ($vendorObj['id'] == $currentUser_id){
			$validToEdit = true;
		}else{
			if ($isAdmin) $validToEdit = true;
		}
		
		if (!((is_numeric($vendorObj['id'])) && ($vendorObj['id'] != "")))
			$validToEdit = false;
		

		$usingVendor = NULL;
		if ($validToEdit){
			$usingVendor = $vpc->getVendorByID($vendorObj['id']);
		}
		
		if (!is_null($usingVendor)){
			/*if (isset($vendorObj['vendorType']))
				$usingVendor->vendorType = $vendorObj['vendorType'];
			if (isset($vendorObj['vendorMemberShipType']))
				$usingVendor->vendorMemberShipType = $vendorObj['vendorMemberShipType'];*/
			$usingVendor->name = $vendorObj['name'];
			$usingVendor->email = $vendorObj['email'];
			$usingVendor->website = $vendorObj['website'];
			$usingVendor->address = $vendorObj['address'];
			$usingVendor->facebook = $vendorObj['facebook'];
			$usingVendor->showFacebook = $vendorObj['showFacebook'];
			$usingVendor->instagram = $vendorObj['instagram'];
			$usingVendor->showInstagram = $vendorObj['showInstagram'];
			$usingVendor->twitter = $vendorObj['twitter'];
			$usingVendor->showTwitter = $vendorObj['showTwitter'];
			$usingVendor->pintrist = $vendorObj['pintrist'];
			$usingVendor->showPintrist = $vendorObj['showPintrist'];
			$usingVendor->linkedin = $vendorObj['linkedin'];
			$usingVendor->showLinkedin = $vendorObj['showLinkedin'];
			$usingVendor->googlePlus = $vendorObj['googlePlus'];
			$usingVendor->showGooglePlus = $vendorObj['showGooglePlus'];
			$usingVendor->youtube = $vendorObj['youtube'];
			$usingVendor->showYoutube = $vendorObj['showYoutube'];
			$usingVendor->vimeo = $vendorObj['vimeo'];
			$usingVendor->showVimeo = $vendorObj['showVimeo'];
			$usingVendor->introText = $vendorObj['introText'];
			$usingVendor->ticketToSavings = $vendorObj['ticketToSavings'];
			$usingVendor->ticketToSavingsAmount = trim($vendorObj['ticketToSavingsAmount'],"$");
			$usingVendor->logo = $vendorObj['logo'];
			$usingVendor->headshotImage = $vendorObj['headshotImage'];
			//$usingVendor->categories = array();
			/*foreach($vendorObj['categories'] as $category){
				array_push($usingVendor->categories,$category);
			}*/
			$usingVendor->categoryArray = array();
			foreach($vendorObj['categories'] as $category){
				array_push($usingVendor->categoryArray,$category);
			}
			$usingVendor->phoneArray = array();
			$aCC = new allCarriersClass();
			foreach($vendorObj['phones'] as $phone){
				$carrier = $aCC->findCarrierByID($phone['carrierID']);
				$vPhoneObj = new vendorPhoneClass($phone['phoneNumber'],$phone['type'],$phone['allowClientsToText'],$phone['allowTeamToText'],$phone['textTypeAllowed'],$carrier);
				array_push($usingVendor->phoneArray,$vPhoneObj);
			}
			if (count($usingVendor->phoneArray) == 0){
				array_push($usingVendor->phoneArray,new vendorPhoneClass());
			}
			
			//print_r($usingVendor->phoneArray);
			//echo "Vendor\n";
//			print_r($usingVendor);
//			echo "\n----\n";
			$usingVendor->save();
			/*$vpc = new vendorsClass('vendorList.xml');
			$savedVendor = $vpc->getVendorByID($usingVendor->id);
			echo "Vendor After Save\n";
			print_r($savedVendor);
			echo "\n----\n";*/
//	print_r($vpc);
			$saved = true;
		
		} //end if usingVendor != null
	
	} //end if session is set etc.
	
	echo json_encode($saved);
	
	

?>