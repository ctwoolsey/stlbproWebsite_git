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
		public $businessName = "";
		public $email = "";
		public $phone = "";
		public $servicesProvided = "";
		public $howHeard = "";
		public $whyInterested = "";
		public $goodFit = "";
		public $comments = "";
			
		public $emailsSentTo = "";
		public $textMessagesSentTo = "";
		
		//private $vendorClass = NULL;
		
		function __construct($n,$bn,$e,$p,$sp,$hH,$wI,$gF,$cmt){
			$vClass = new vendorsClass();
			
			$this->name = $n;
			$this->businessName = $bn;
			$this->email = $e;
			$this->phone = $p;
			$this->servicesProvided = $sp;
			$this->howHeard = $hH;
			$this->whyInterested = $wI;
			$this->goodFit = $gF;
			$this->comments = $cmt;
			
			$this->contactVendors($vClass);
		}
		
		private function contactVendors($vClass){
			foreach($vClass->vendors as $vendor){
				if ($vendor->isAdmin() === true){
					$this->sendTextToVendorIfNeeded($vendor);	
					$this->sendEmailToVendorIfNeeded($vendor);	
				}
			}
			
		}
		
		private function sendTextToVendorIfNeeded($vendor){
			$textEmailAddr = $vendor->textToNumber(TEXTWHOTYPE::TEAM,MESSAGETYPE::MMS);
			if ($textEmailAddr !== false){
				$textMessage = $this->messageToVendor($vendor,DESTINATIONTYPE::MMSTEXT);
				$this->sendMimeEmail($textEmailAddr,$textMessage,DESTINATIONTYPE::MMSTEXT);
			}else{
				$textEmailAddr = $vendor->textToNumber(TEXTWHOTYPE::TEAM,MESSAGETYPE::SMS);
				if ($textEmailAddr !== false){
					$textMessage = $this->messageToVendor($vendor,DESTINATIONTYPE::SMSTEXT);
					$this->sendMimeEmail($textEmailAddr,$textMessage,DESTINATIONTYPE::SMSTEXT);
				}
			}
		}
		
		private function messageToVendor($vendor,$destinationType){
			$newLine = "<br>";
			$phoneDialer = $this->phone;
			$contactName = $this->name;
			$email = $this->email;
			$nameLbl = "Name: ";
			$bizLbl = "Business Name: ";
			$emailLbl = "Email: ";
			$phoneLbl = "Phone: ";
			$servicesLbl = "Services Provided: ";			
			$howHeardLbl = "How Heard: ";
			$whyIntLbl = "Why Interested: ";
			$goodFitLbl = "Why a Good Fit: ";
			$commentLbl = "Additional Comments: ";
			
			
			if ($destinationType == DESTINATIONTYPE::MMSTEXT)
				$newLine = "\r\n";
			if ($destinationType == DESTINATIONTYPE::EMAIL){
				$newLine = "<br>";
				$contactName = '<span class="colored">'.$this->name.'</span>';
				$email = '<a href="mailto:'.$this->email.'">'.$this->email.'</a>';
				$phoneDialer = '<a href="tel://"'.$this->phone.'>'.$this->phone.'</a>';
				$nameLbl = "<b>".$nameLbl."</b>";
				$bizLbl = "<b>".$bizLbl."</b>";
				$emailLbl = "<b>".$emailLbl."</b>";
				$phoneLbl = "<b>".$phoneLbl."</b>";
				$servicesLbl = "<b>".$servicesLbl."</b>";
				$howHeardLbl = "<b>".$howHeardLbl."</b>";
				$whyIntLbl = "<b>".$whyIntLbl."</b>";
				$goodFitLbl = "<b>".$goodFitLbl."</b>";
				$commentLbl = "<b>".$commentLbl."</b>";
			}
			if ($destinationType == DESTINATIONTYPE::SMSTEXT)
				$newLine = "<br>";
		
			$message = $contactName." has indicated interest in joining.".$newLine;
			$message .= $newline."Contact Information".$newLine;
			$message .= $nameLbl.$this->name.$newLine;
			$message .= $bizLbl.$this->businessName.$newLine;
			$message .= $phoneLbl.$phoneDialer.$newLine;
			$message .= $emailLbl.$email.$newLine;
			$message .= $servicesLbl.$this->servicesProvided.$newLine;
			$message .= $howHeardLbl.$this->howHeard.$newLine;
			$message .= $whyIntLbl.$this->whyInterested.$newLine;
			$message .= $goodFitLbl.$this->goodFit.$newLine;
			$message .= $commentLbl.$this->comments.$newLine;
			
			return $message;
		}
		private function sendMimeEmail($toAddr,$emailBody,$destinationType){
			$crlf = "\r\n";
			$hdrs = array( 
					'From' => 'noreply@stlbridalpros.com', 
					'Subject' => 'Join Request - stlbridalpros' 
					); 
			
			$mime = new Mail_mime($crlf); 
			
			if (($destinationType == DESTINATIONTYPE::MMSTEXT) || ($destinationType == DESTINATIONTYPE::SMSTEXT))
				$mime->setTXTBody($emailBody);
			if ($destinationType == DESTINATIONTYPE::EMAIL){
				$mime->addHTMLImage("images/stlbridalprosLogo_200x180.jpg", "image/jpeg");
				$cid=$mime->_html_images[count($mime->_html_images)-1]['cid'];
				$html = '<html><style>.colored{color:#aa0000;}#header{text-align:center;margin-bottom:20px;font-weight:bold;font-size:18px}#headerText{margin-left:15px;}</style><body><div id="header"><img src="cid:'.$cid.'"><span id="headerText"><p>St. Louis Bridal Pros<p><p>Membership Request</p></span></div>'.$emailBody.'</body></html>';
				$mime->setHTMLBody($html); 	
			}
			
			
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);
		
			$mail =& Mail::factory('mail');
			$mail->send($toAddr, $hdrs, $body);	
		}
		
		private function sendEmailToVendorIfNeeded($vendor){
			if ($vendor->email != ""){
				$emailBody = $this->messageToVendor($vendor,DESTINATIONTYPE::EMAIL);
				
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
	$businessName = $formData["businessName"];
	
	$servicesProvided = $formData["servicesProvided"];
	$howHeard = $formData["howHeard"];
	$whyInterested = $formData["whyInterested"];
	$goodFit = $formData["whyGoodFit"];
	$comments = $formData["commentsQuestions"];
	
	$requiredFields = $formData["requiredFields"];
	$validation = new validator();
	$valid = true;
	//first check requiredFields
	foreach($requiredFields as $requiredField){
		$valid = $validation->NOT_BLANK($formData[$requiredField]);
		if (!$valid)
			break;
	}
	
	//echo "no blanks\n";
	
	//check special fields
	if ($valid){
		//echo "checking phone\n";
		$valid = $validation->USA_PHONE($phone);	
	}
	if ($valid){
		//echo "checking email\n";
		$valid = $validation->EMAIL($email);	
	}
	if ($valid){
		//echo "checking spam\n";
		$valid = $validation->SPAMCHECK($formData["spamCheck"]);
		//if ($valid == false)
			//echo "Spamcheck = '".$formData['spamCheck']."'\n";
	}
	
	if ($valid){
		
		$contactEntry = new contactEntryClass($name,$businessName,$email,$phone,$servicesProvided,$howHeard,$whyInterested,$goodFit,$comments);
		
		
		//if successfully emailed etc
		echo json_encode(true);
	}else{
		echo json_encode(false);
	}

?>