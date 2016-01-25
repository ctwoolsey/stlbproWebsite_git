<?
	$ssid = 0;
	if (isset($_REQUEST['ssid']) && ($_REQUEST['ssid'] != "0")){
		$ssid=$_REQUEST['ssid'];
		session_id($ssid);
		$ssidLink_continuing = "&ssid=".$ssid;
		$ssidLink_starting = "?ssid=".$ssid;
	}
	session_start();
	if (isset($_SESSION['username'])){
		include_once('vendorsClass.php');
		include_once('scripts/Mobile-Detect-2.8.11/Mobile_Detect.php');
		$mobileDetect = new Mobile_Detect();
		$vpc = new vendorsClass('vendorList.xml');
		$username = "";
		$isAdmin = false;
		$vendor = NULL;
	
	
		$username = $_SESSION['username'];
		$vendor = $vpc->findVendorWithEmail($username);
		if (!is_null($vendor)){
			$isAdmin = $vendor->isAdmin();
		}
	}
	
	if ($isAdmin){
		include_once('vendorsClass.php');
		include_once('Mail.php');
		require_once('/home/dvaqpvvw/php/Mail/mime.php');
		
		$vC = new vendorsClass();
		$admins = $vC->getAdmins();
		$vendor = $vC->getVendorByID($_REQUEST['vendorID']);
	
		
		$crlf = "\r\n";
		$hdrs = array( 
				'From' => 'noreply@stlbridalpros.com', 
				'Subject' => 'Welcome to St. Louis Bridal Professionals' 
				); 
		
		$mime = new Mail_mime($crlf); 
		
		$mime->addHTMLImage("images/stlbridalprosLogo_200x180.jpg", "image/jpeg");
		$logo_cid=$mime->_html_images[count($mime->_html_images)-1]['cid'];
		
		$mime->addHTMLImage("images/adminLogin.gif", "image/gif");
		$loginButton_cid=$mime->_html_images[count($mime->_html_images)-1]['cid'];
		
		$emailBody = "<p>Welcome to <b>St. Louis Bridal Professionals</b>!</p>";
		$emailBody .= "<p> A profile has been created for you, but you must fill in the information before your information will be made available on the website.</p>";
		$emailBody .= '<p><a href="http://www.stlbridalpros.com/vendorLogin.php">Click here login and finish your profile.</a></p>';
		$emailBody .= '<p>Keep your login details for reference:</p>';
		$emailBody .= '<p><b>Email: </b>'.$vendor->email.'</p>';
		$emailBody .= '<p><b>Password: </b>'.$vendor->password.'</p>';
		$emailBody .= '<p>You can login from the main website (<a href="http://www.stlbridalpros.com">www.stlbridalpros.com</a>) by clicking this image: <img id="loginButton" src="cid:'.$loginButton_cid.'"/> found at the bottom of the page.</p>';
		$emailBody .= '<br/><br/><p>We look forward to having you be a part of this group!</p>';
		$emailBody .= '<p>If you have problems contact: ';
		$adminCount = 0;
		$adminContactMsg = "";
		foreach($admins as $admin){
			if ($adminCount > 0)
				$adminContactMsg .= " or ";
			$adminContactMsg .= '<a href="mailto:'.$admin->email.'">'.$admin->email.'</a>';
		}
		$emailBody .= $adminContactMsg."</p>";
		$emailBody .= '<p>Do not reply to this email.  It is unmonitored.</p>';
		$html = '<html><style>#loginButton{width:30px;vertical-align:middle;}.colored{color:#aa0000;}#header{text-align:center;margin-bottom:20px;font-weight:bold;font-size:18px}#headerText{margin-left:15px;}</style><body><div id="header"><img src="cid:'.$logo_cid.'"><span id="headerText"><p>St. Louis Bridal Pros<p><p>Set Up Information</p></span></div>'.$emailBody.'</body></html>';
		$mime->setHTMLBody($html); 	
		
		
		
		$body = $mime->get();
		$hdrs = $mime->headers($hdrs);
	
		$mail =& Mail::factory('mail');
		$mailResult = $mail->send($vendor->email, $hdrs, $body);	
		/*if ($mailResult == true)
			echo json_encode(true);
		else
			echo json_encode($mailResult['message']);*/
		echo json_encode($mailResult);
	}else{ //not an admin
		echo json_encode(['message'=>"You are not authorized to send an email."]);
	}
	
?>