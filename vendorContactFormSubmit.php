<?
	include_once('scripts/validationClass/validator.php');
	include_once('vendorsClass.php');
	include_once('Mail.php');
	require_once('/home/dvaqpvvw/php/Mail/mime.php');
	
	abstract class DESTINATIONTYPE{
		const MMSTEXT = "mms";
		const SMSTEXT = "sms";
		const EMAIL = "email";
	}

	class contactEntryClass {
		public $name = "";
		public $email = "";
		public $phone = "";
		public $weddingDate = "";
		public $servicesNeeded = "";
		public $comments = "";
		public $otherServicesNeeded = "";
		public $emailsSentTo = "";
		public $textMessagesSentTo = "";
		
		//private $vendorClass = NULL;
		
		function __construct($n,$e,$p,$wd,$snA,$cmt,$othSvc){
			$vClass = new vendorsClass('vendorList.xml');
			//$this->vendorClass = $vClass;
			$this->name = $n;
			$this->email = $e;
			$this->phone = $p;
			$this->weddingDate = $wd;
			$this->comments = $cmt;
			$this->otherServicesNeeded = $othSvc;	
			$this->getServicesNeeded($snA);
			$this->contactVendors($vClass);
			
		}
		
		private function getServicesNeeded($servicesNeededArray){
			$this->servicesNeeded  = "";
			foreach($servicesNeededArray as $serviceNeeded){
				$this->servicesNeeded .= $serviceNeeded.",";
			}
			
			$this->servicesNeeded = trim($this->servicesNeeded,","); //remove last ','
			
		}
		
		private function getVendorsNeedingContact($vClass){
			$vendorsNeededArrayServiceKey = array();
			$vendorsAndServicesNeeded = array();
			$serviceNeededArray = split(",",$this->servicesNeeded);
			foreach($serviceNeededArray as $serviceNeeded){
				//$vendor = $this->vendorClass->findVendorWithService($serviceNeeded);
				$vendor = $vClass->findVendorWithService($serviceNeeded);
				if (!is_null($vendor)){
					if (!array_key_exists($serviceNeeded,$vendorsNeededArrayServiceKey))
						$vendorsNeededArrayServiceKey[$serviceNeeded] = (string)$vendor->id;
				}
			}
			
			foreach($vendorsNeededArrayServiceKey as $service => $vendorID){
				if (!array_key_exists($vendorID,$vendorsAndServicesNeeded))
					$vendorsAndServicesNeeded[$vendorID] = "";
				if ($vendorsAndServicesNeeded[$vendorID] != "")
					$separator = ", ";
				$vendorsAndServicesNeeded[$vendorID] .= $separator.$service;
			}
			return $vendorsAndServicesNeeded;
		}
		
		private function contactVendors($vClass){
			$vendorsNeeded = $this->getVendorsNeedingContact($vClass);
			foreach($vendorsNeeded as $vendorID => $servicesNeeded){
				//$vendor = $this->vendorClass->getVendorByID($vendorID);
				$vendor = $vClass->getVendorByID($vendorID);
				if (!is_null($vendor)){
					$this->sendTextToVendorIfNeeded($vendor,$servicesNeeded);	
					$this->sendEmailToVendorIfNeeded($vendor,$servicesNeeded);	
				}
			}
			
		}
		
		private function sendTextToVendorIfNeeded($vendor,$servicesNeeded){
			$success = false;
			$textEmailAddr = $vendor->textToNumber(TEXTWHOTYPE::TEAM,MESSAGETYPE::MMS);
			if ($textEmailAddr !== false){
				$textMessage = $this->messageToVendor($vendor,$servicesNeeded,DESTINATIONTYPE::MMSTEXT);
				$this->sendMimeEmail($textEmailAddr,$textMessage,DESTINATIONTYPE::MMSTEXT);
				$success = true;
			}else{
				$textEmailAddr = $vendor->textToNumber(TEXTWHOTYPE::TEAM,MESSAGETYPE::SMS);
				if ($textEmailAddr !== false){
					$textMessage = $this->messageToVendor($vendor,$servicesNeeded,DESTINATIONTYPE::SMSTEXT);
					$this->sendMimeEmail($textEmailAddr,$textMessage,DESTINATIONTYPE::SMSTEXT);
					$success = true;
				}
			}
			
			if ($success){
				//log that a text message was sent
				$separator = ";";
				if ($this->textMessagesSentTo == "")
					$separator = "";
				$this->textMessagesSentTo .= $separator.$vendor->name."(".$numberToTextTo.")";
			}
		}
		
		private function messageToVendor($vendor,$servicesNeeded,$destinationType){
			$newLine = "<br>";
			$phoneDialer = $this->phone;
			$contactName = $this->name;
			$email = $this->email;
			$servNeedLbl = "Services Needed: ";
			$othSvcNeededLbl = "Other Services: ";
			$nameLbl = "Name: ";
			$emailLbl = "Email: ";
			$phoneLbl = "Phone: ";
			$wedDateLbl = "Wedding Date: ";
			$commentLbl = "Comments: ";
			$callToAction = " Please contact them right away.";
			
			if ($destinationType == DESTINATIONTYPE::MMSTEXT)
				$newLine = "\r\n";
			if ($destinationType == DESTINATIONTYPE::EMAIL){
				$newLine = "<br>";
				$contactName = '<span class="colored">'.$this->name.'</span>';
				$email = '<a href="mailto:'.$this->email.'">'.$this->email.'</a>';
				$phoneDialer = '<a href="tel://"'.$this->phone.'>'.$this->phone.'</a>';
				$servNeedLbl = "<b>".$servNeedLbl."</b>";
				$nameLbl = "<b>".$nameLbl."</b>";
				$emailLbl = "<b>".$emailLbl."</b>";
				$phoneLbl = "<b>".$phoneLbl."</b>";
				$wedDateLbl = "<b>".$wedDateLbl."</b>";
				$othSvcNeededLbl = "<b>".$othSvcNeededLbl."</b>";
				$commentLbl = "<b>".$commentLbl."</b>";
				$callToAction = "<i>".$callToAction."</i>";
			}
			if ($destinationType == DESTINATIONTYPE::SMSTEXT)
				$newLine = "<br>";
		
			$message = $contactName." has requested contact.".$callToAction.$newLine;
			$message .= $servNeedLbl.$servicesNeeded.$newLine;
			$message .= $newline."Contact Information".$newLine;
			$message .= $nameLbl.$this->name.$newLine;
			$message .= $phoneLbl.$phoneDialer.$newLine;
			$message .= $emailLbl.$email.$newLine;
			$message .= $wedDateLbl.$this->weddingDate.$newLine;
			$message .= $othSvcNeededLbl.$this->otherServicesNeeded.$newLine;
			$message .= $commentLbl.$this->comments.$newLine;
			
			return $message;
		}
		
		/*private function sendEmail($toAddr,$emailBody){
			
			$subject= "stlbridalpros Hot Lead";
			$fromName = "St. Louis Bridal Professionals";
			$fromEmail = "noreply@stlbridalpros.com"; //pw:no8rplI$
			
			// To send HTML mail, the Content-type header must be set
			$headers  = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
			
			// Additional headers
			$headers .= "From: $fromName <$fromEmail>"."\r\n";
			
			//Mail function
			mail($toAddr, $subject, $emailBody, $headers);
		}*/
		
		private function sendMimeEmail($toAddr,$emailBody,$destinationType){
			$crlf = "\r\n";
			$hdrs = array( 
					'From' => 'noreply@stlbridalpros.com', 
					'Subject' => 'Hot Lead - stlbridalpros' 
					); 
			
			$mime = new Mail_mime($crlf); 
			
			if (($destinationType == DESTINATIONTYPE::MMSTEXT) || ($destinationType == DESTINATIONTYPE::SMSTEXT))
				$mime->setTXTBody($emailBody);
			if ($destinationType == DESTINATIONTYPE::EMAIL){
				$mime->addHTMLImage("images/stlbridalprosLogo_200x180.jpg", "image/jpeg");
				$cid=$mime->_html_images[count($mime->_html_images)-1]['cid'];
				$html = '<html><style>.colored{color:#aa0000;}#header{text-align:center;margin-bottom:20px;font-weight:bold;font-size:18px}#headerText{margin-left:15px;}</style><body><div id="header"><img src="cid:'.$cid.'"><span id="headerText"><p>St. Louis Bridal Pros<p><p>Hot Lead</p></span></div>'.$emailBody.'</body></html>';
				$mime->setHTMLBody($html); 	
			}
			
			
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);
		
			$mail =& Mail::factory('mail');
			$mail->send($toAddr, $hdrs, $body);	
		}
		
		private function sendEmailToVendorIfNeeded($vendor,$servicesNeeded){
			if ($vendor->email != ""){
				$emailBody = $this->messageToVendor($vendor,$servicesNeeded,DESTINATIONTYPE::EMAIL);
				
				$to = $vendor->email;
				
				//$this->sendEmail($to,$emailBody);
				$this->sendMimeEmail($to,$emailBody,DESTINATIONTYPE::EMAIL);
				
				//log that an email message was sent
				$separator = ";";
				if ($this->emailsSentTo == "")
					$separator = "";
				$this->emailsSentTo .= $separator.$vendor->name."(".$vendor->email.")";
			}
		}
		
		
	} //end Class
	
	
	//echo "here<br>";
	$formData = $_REQUEST["formData"];
	$name = $formData["name"];
	$phone = $formData["phone"];
	$email = $formData["email"];
	$wedDate = $formData["weddingDate"];
	$servicesNeeded = $formData["servicesNeeded"];
	$otherServicesNeeded = $formData["otherServicesNeeded"];
	$comments = $formData["comments"];
	
	$requiredFields = $formData["requiredFields"];
	$validation = new validator();
	$valid = true;
	//first check requiredFields
	foreach($requiredFields as $requiredField){
		$valid = $validation->NOT_BLANK($formData[$requiredField]);
		if (!$valid)
			break;
	}
	
	//check special fields
	if ($valid){
		$valid = $validation->USA_PHONE($phone);	
	}
	if ($valid){
		$valid = $validation->EMAIL($email);	
	}
	if ($valid){
		$valid = $validation->SPAMCHECK($formData["spamCheck"]);
	}
	
	if ($valid){
	
		$contactEntry = new contactEntryClass($name,$email,$phone,$wedDate,$servicesNeeded,$comments,$otherServicesNeeded);
		
		
		try {
		  # MySQL with PDO_MYSQL
		  $conn = new PDO("mysql:host=localhost;dbname=dvaqpvvw_inquiryDB", "dvaqpvvw", "1rtY4aF35i");
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		$sql = "INSERT INTO contacted (name, phone, email, weddingDate, servicesNeeded, emailsSentTo, otherServicesNeeded, comments,textMessagesSentTo) value (:name, :phone, :email, :weddingDate, :servicesNeeded, :emailsSentTo, :otherServicesNeeded, :comments, :textMessagesSentTo)";
		
		/*echo $sql."\n";
		print_r($contactEntry);
		echo "\nFormData\n";
		
		print_r($formData);
		echo "\n";*/
		$q = $conn->prepare($sql);
		$q->execute((array)$contactEntry);
		
		
		
		
		
		//if successfully emailed etc
		echo json_encode(true);
	}else{
		echo json_encode(false);
	}
	

?>