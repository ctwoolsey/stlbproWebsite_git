<?
	include_once('scripts/validationClass/validator.php');
	
	abstract class MESSAGETYPE{
		const SMS = "sms";
		const MMS = "mms";
	}
	abstract class TEXTWHOTYPE{
		const CLIENT = "client";
		const TEAM = "team";
	}
	abstract class PHONETYPE{
		const CELL = "cell";
		const LAND = "land";
	}
	abstract class YESNOTYPE{
		const YES = "yes";
		const NO = "no";
	}
	
	class VENDORTYPE{
		const NONVENUE = "Non-Venue";
		const VENUE = "Venue";
		
		private $allowedValues = [VENDORTYPE::NONVENUE,VENDORTYPE::VENUE];
		
		public function getSelectCode($nameIDField="",$class="",$vendorObj=NULL,$useDefault=true,$defaultText="Select Vendor Type",$defaultValue="-1"){
			$preselectedID = "-1";
			if (!is_null($vendorObj)){
				if ((!is_null($vendorObj->vendorType)) && ($vendorObj->vendorType != ""))	
					$preselected = $vendorObj->vendorType;
			}
			$selected = ($defaultValue==$preselected)?" selected":"";
			$defaultOption = $useDefault?'<option value="'.$defaultValue.'"'.$selected.'>'.$defaultText.'</option>':"";
			$selectCode = '<SELECT id="'.$nameIDField.'" name="'.$nameIDField.'" class="'.$class.'">'.$defaultOption;
			foreach($this->allowedValues as $vendorTypes){
					$selected = ($vendorTypes==$preselected)?" selected":"";
					$selectCode .= '<option value="'.$vendorTypes.'"'.$selected.'>'.$vendorTypes.'</option>';
			}
			$selectCode .= '</SELECT>';
			return ($selectCode);
		}
	}
	
	class VENDORMEMBERSHIPTYPE{
		const FULLMEMBER = "Full Member";
		
		private $allowedValues = [VENDORMEMBERSHIPTYPE::FULLMEMBER];
		
		public function getSelectCode($nameIDField="",$class="",$vendorObj=NULL,$useDefault=true,$defaultText="Select Vendor Type",$defaultValue="-1"){
			$preselectedID = "-1";
			if (!is_null($vendorObj)){
				if ((!is_null($vendorObj->vendorMembershipType)) && ($vendorObj->vendorMembershipType != ""))	
					$preselected = $vendorObj->vendorMembershipType;
			}
			$selected = ($defaultValue==$preselected)?" selected":"";
			$defaultOption = $useDefault?'<option value="'.$defaultValue.'"'.$selected.'>'.$defaultText.'</option>':"";
			$selectCode = '<SELECT id="'.$nameIDField.'" name="'.$nameIDField.'" class="'.$class.'">'.$defaultOption;
			foreach($this->allowedValues as $vendorMembershipType){
					$selected = ($vendorMembershipType==$preselected)?" selected":"";
					$selectCode .= '<option value="'.$vendorMembershipType.'"'.$selected.'>'.$vendorMembershipType.'</option>';
			}
			$selectCode .= '</SELECT>';
			return ($selectCode);
		}
	}


	class vendorsClass{
		public $vendors = NULL;
		public $postableVendors = NULL;
		public $unpostableVendors = NULL;
		public $postableVendorsNonVenue = NULL;
		public $postableVendorsVenue = NULL;
		public $errMessages = "";
		private $xmlVendors = NULL;
		private $xmlFileNameString = "vendorList.xml";
		private $vendorViewMode = "all"; //also "postable" or "unpostable"
		
		function __construct(){
			$a = func_get_args();
			$i = func_num_args();
			if (method_exists($this,$f='__construct'.$i)) {
				call_user_func_array(array($this,$f),$a);
			} 
		}
		
		private function __construct0(){
			$this->loadClassFromFile();	
		}
		
		private function __construct1($xmlFile){
			$this->xmlFileNameString = $xmlFile;
			$this->loadClassFromFile();	
		}
		
		public function setVendorViewMode($mode){
			switch ($mode){
				case "all":
					$this->vendorViewMode = "all";
					break;
				case "postable":
					$this->vendorViewMode = "postable";
					break;
				case "unpostable":
					$this->vendorViewMode = "unpostable";
					break;	
				default:
					$this->vendorViewMode = "all";
					break;
			}
		}
		
		private function getVendorArrayFromViewMode(){
			$vArray = NULL;
			switch ($this->vendorViewMode){
				case "all":
					$vArray = $this->vendors;
					break;
				case "postable":
					$vArray = $this->postableVendors;
					break;
				case "unpostable":
					$vArray = $this->unpostableVendors;
					break;	
			}
			return $vArray;
		}
		
		public function loadClassFromFile(){
			$this->vendors = array();
			$this->postableVendors = array();
			$this->unpostableVendors = array();
			$this->postableVendorsNonVenue = array();
			$this->postableVendorsVenue = array();
			if (file_exists($this->xmlFileNameString)) {
				$this->xmlVendors = simplexml_load_file($this->xmlFileNameString);
				foreach($this->xmlVendors->vendor as $vendor){
					$vpc = new vendorProfileClass($vendor);
					array_push($this->vendors,$vpc);
					if ($vpc->canBePosted == YESNOTYPE::YES){
						array_push($this->postableVendors,$vpc);
						if ($vpc->vendorType == VENDORTYPE::NONVENUE)
							array_push($this->postableVendorsNonVenue,$vpc);
						else if ($vpc->vendorType == VENDORTYPE::VENUE)
							array_push($this->postableVendorsVenue,$vpc);
					}
					if ($vpc->canBePosted == YESNOTYPE::NO)
						array_push($this->unpostableVendors,$vpc);
										
				}	
			}else{
				$this->$errMessages = "Failed to Load ".$this->xmlFileNameString;
			}
		}
		
		public function getSelectCode($field,$nameIDField="",$class="",$useDefault=true,$defaultText="Select Vendor",$defaultValue="-1"){
			
			$defaultOption = $useDefault?'<option selected value="'.$defaultValue.'">'.$defaultText.'</option>':"";
			$selectCode = '<SELECT id="'.$nameIDField.'" name="'.$nameIDField.'" class="'.$class.'">'.$defaultOption;
			$viewableVendors = $this->getVendorArrayFromViewMode();
			foreach($viewableVendors as $vendor){
				if (isset($vendor->$field)){
					$selectCode .= '<option value="'.$vendor->id.'">'.$vendor->$field.'</option>';
				}
			}
			$selectCode .= '</SELECT>';
			return ($selectCode);
		}
		
		public function getAdmins(){
			$adminArray = array();
			foreach($this->vendors as $vendor){
				if ($vendor->isAdmin()){
					array_push($adminArray,$vendor);	
				}
			}
			
			return $adminArray;
		}
		
		public function vendorsForWebsite($service){
			$this->setVendorViewMode("postable");
			return ($this->getVendorArrayFromViewMode());
		}
		
		public function findVendorWithService($service){
			$vendorWithService = NULL;
			$viewableVendors = $this->getVendorArrayFromViewMode();
			foreach($viewableVendors as $vendor){
				if ($vendor->hasService($service)){
					$vendorWithService = $vendor;
					break;
				}
			}
			return ($vendorWithService);
		}
		
		public function findVendorWithEmail($emailAddr){
			$vendorWithEmail = NULL;
			$viewableVendors = $this->getVendorArrayFromViewMode();
			foreach($viewableVendors as $vendor){
				if ((strtolower($vendor->email) == strtolower($emailAddr)) && ($emailAddr != "")){
					$vendorWithEmail = $vendor;
					break;
				}
			}
			return ($vendorWithEmail);
		}
		
		public function getVendorByID($id){
			$vendorFound = NULL;
			$viewableVendors = $this->getVendorArrayFromViewMode();
			foreach($viewableVendors as $vendor){
				if ($vendor->id == $id){
					$vendorFound = $vendor;
					break;	
				}
			}
			return $vendorFound;
		}
		
		public function createNewVendorID(){
			$highestNumber = 0;
			foreach($this->vendors as $vendor){
				if ($vendor->id > $highestNumber)
					$highestNumber = $vendor->id;	
			}
			$highestNumber++;
			return $highestNumber;
		}
		
		public function reorderVendors($orderedVendorIDArray){
			$newVendorArray = array();
			foreach($orderedVendorIDArray as $vendorID){
				$vendor = $this->getVendorByID($vendorID);
				array_push($newVendorArray,$vendor);	
			}
			$this->vendors = $newVendorArray;
			
			$this->generateNewXMLString();
			$this->xmlVendors->asXML($this->xmlFileNameString);
			$this->loadClassFromFile();
		}
		
		public function updateVendor($vendorToUpdate){
			for($i=0; $i<count($this->vendors); $i++){
				$originalVendor = $this->vendors[$i];
				if ($originalVendor->id == $vendorToUpdate->id){
					$this->vendors[$i] = $vendorToUpdate;
					break;	
				}
			}
			//$this->writeVendorsXMLFile();
			$this->generateNewXMLString();
			$this->xmlVendors->asXML($this->xmlFileNameString);
			$this->loadClassFromFile();
		}
		
		public function removeVendor($vendorID){
			$arrayIndex = -1;
			foreach($this->vendors as $arrayKey=>$vendor){
				if ($vendor->id == $vendorID){
					$arrayIndex = $arrayKey;
					break;
				}
			}
			if ($arrayIndex != -1){
				unset($this->vendors[$arrayIndex]);
				$this->generateNewXMLString();
				$this->xmlVendors->asXML($this->xmlFileNameString);
				$this->loadClassFromFile();
				return true;
			}else{
				return false;
			}
		}
		
		public function addVendor($newVendor){
			array_push($this->vendors,$newVendor);
			$this->generateNewXMLString();
			$this->xmlVendors->asXML($this->xmlFileNameString);
			$this->loadClassFromFile();
		}
		
		private function generateNewXMLString(){
			$xmlString = "<vendors>";
			foreach($this->vendors as $vendor){
				//if ($vendor->id == 8)
					//print_r($vendor->generateVendorXMLString());
				$xmlString .= $vendor->generateVendorXMLString(); 
			}
			$xmlString .= "</vendors>";
			$this->xmlVendors = simplexml_load_string($xmlString);
			
			return $xmlString;
		}
	}
	
	class vendorProfileClass{
		public $id = "";
		public $name = "";
		public $categoryArray = array();
		public $logo = "";  //images/vendorLogos/...
		/*private $logo_singleLineText = "no";*/
		public $phoneArray = array();
		public $email = "";
		public $website = "";
		public $address = "";
		public $facebook = "";
		public $showFacebook = false;
		public $instagram = "";
		public $showInstagram = false;
		public $twitter = "";
		public $showTwitter = false;
		public $pintrist = "";
		public $showPintrist = false;
		public $linkedin = "";
		public $showLinkedin = false;
		public $googlePlus = "";
		public $showGooglePlus = false;
		public $youtube = "";
		public $showYoutube = false;
		public $vimeo = "";
		public $showVimeo = false;
		public $headshotImage = ""; //images/vendorHeadshots/
		public $introText = "";
		public $ticketToSavings = "";
		public $ticketToSavingsAmount = "";
		public $password = "";
		public $admin = false;
		public $canBePosted = "no";
		public $reasonNoPost = "";
		
		public $vendorType = VENDORTYPE::NONVENUE;
		public $vendorMembershipType = VENDORMEMBERSHIPTYPE::FULLMEMBER;
		private $xmlVendors = NULL;
		//private $landingPageDirString = 'vendorLandingPages/';
		private $requiredForLandingPage = ['id','name','categoryArray','logo','phoneArray','email','website','introText','ticketToSavings','ticketToSavingsAmount'];
		private $socialMedia = ['facebook','twitter','instagram','pintrist','linkedin','googlePlus','youtube','vimeo'];
		
		function __construct(){
			$a = func_get_args();
			$i = func_num_args();
			if (method_exists($this,$f='__construct'.$i)) {
				call_user_func_array(array($this,$f),$a);
			} 
		}
		
		private function __construct0(){
			//creates a blank new vendor
			$this->id = "";
			$this->password = $this->generateVendorPassword(5);
			array_push($this->categoryArray,"");
			//array_push($this->phoneArray,new vendorPhoneClass());
		}
		
		private function __construct1($xmlVendor){
			if (isset($xmlVendor['id']))
				$this->id = (string)$xmlVendor['id'];
			if (isset($xmlVendor['vendorType']))
				$this->vendorType = (string)$xmlVendor['vendorType'];
			if (isset($xmlVendor['vendorMembershipType']))
				$this->vendorMembershipType = (string)$xmlVendor['vendorMembershipType'];
								
			if (property_exists($xmlVendor,"name")){
				$this->name = urldecode((string)$xmlVendor->name);
			}
			if (property_exists($xmlVendor,"logo")){
				$this->logo = (string)$xmlVendor->logo;
				/*if (isset($xmlVendor->logo['singleLineText']))
					$this->logo_singleLineText = (string)$xmlVendor->logo['singleLineText'];*/
			}
			if (property_exists($xmlVendor,"headshotImage")){
				$this->headshotImage = (string)$xmlVendor->headshotImage;
			}
			if (property_exists($xmlVendor,"introText")){
				$this->introText = urldecode((string)$xmlVendor->introText);
			}
			if (property_exists($xmlVendor,"ticketToSavings")){
				$this->ticketToSavings = urldecode((string)$xmlVendor->ticketToSavings);
				if (isset($xmlVendor->ticketToSavings['amount']))
					$this->ticketToSavingsAmount = (string)$xmlVendor->ticketToSavings['amount'];
			}
			if (property_exists($xmlVendor,"password")){
				$this->password = trim((string)$xmlVendor->password);
			}else $this->password = $this->generateVendorPassword(5);
			
			if (property_exists($xmlVendor,"admin")){
				$this->admin = ((string)$xmlVendor->admin == YESNOTYPE::YES)?true:false;
			}
			if (property_exists($xmlVendor,"canBePosted")){
				$this->canBePosted = (string)$xmlVendor->canBePosted;
			}
			if (property_exists($xmlVendor,"reasonNoPost")){
				$this->reasonNoPost = (string)$xmlVendor->reasonNoPost;
			}
			$phoneCount = 0;
			if (property_exists($xmlVendor,"contactInfo")){
				$xmlContactInfo = $xmlVendor->contactInfo;
				if (property_exists($xmlContactInfo,"email")){
					$this->email = urldecode((string)$xmlContactInfo->email);
				}
				if (property_exists($xmlContactInfo,"phones")){
					$xmlPhones = $xmlContactInfo->phones;
					if (property_exists($xmlPhones,"phone")){
						foreach($xmlPhones->phone as $phone){
							$telClass = new vendorPhoneClass($xmlPhones->phone[$phoneCount++]);
							array_push($this->phoneArray,$telClass);
						} 
					}
				}
				if (property_exists($xmlContactInfo,"website")){
					$this->website = urldecode((string)$xmlContactInfo->website);
				}
				if (property_exists($xmlContactInfo,"address")){
					$this->address = urldecode((string)$xmlContactInfo->address);
				}
				if (property_exists($xmlContactInfo,"facebook")){
					$this->facebook = (string)$xmlContactInfo->facebook;
					if (isset($xmlContactInfo->facebook['show']))
						$this->showFacebook = ((string)$xmlContactInfo->facebook['show'] == 'yes')?true:false;
				}
				if (property_exists($xmlContactInfo,"instagram")){
					$this->instagram = (string)$xmlContactInfo->instagram;
					if (isset($xmlContactInfo->instagram['show']))
						$this->showInstagram = ((string)$xmlContactInfo->instagram['show'] == 'yes')?true:false;
				}
				if (property_exists($xmlContactInfo,"twitter")){
					$this->twitter = (string)$xmlContactInfo->twitter;
					if (isset($xmlContactInfo->twitter['show']))
						$this->showTwitter = ((string)$xmlContactInfo->twitter['show'] == 'yes')?true:false;
				}
				if (property_exists($xmlContactInfo,"pintrist")){
					$this->pintrist = (string)$xmlContactInfo->pintrist;
					if (isset($xmlContactInfo->pintrist['show']))
						$this->showPintrist = ((string)$xmlContactInfo->pintrist['show'] == 'yes')?true:false;
				}
				if (property_exists($xmlContactInfo,"linkedin")){
					$this->linkedin = (string)$xmlContactInfo->linkedin;
					if (isset($xmlContactInfo->linkedin['show']))
						$this->showLinkedin = ((string)$xmlContactInfo->linkedin['show'] == 'yes')?true:false;
				}
				if (property_exists($xmlContactInfo,"googlePlus")){
					$this->googlePlus = (string)$xmlContactInfo->googlePlus;
					if (isset($xmlContactInfo->googlePlus['show']))
						$this->showGooglePlus = ((string)$xmlContactInfo->googlePlus['show'] == 'yes')?true:false;
				}
				if (property_exists($xmlContactInfo,"youtube")){
					$this->youtube = (string)$xmlContactInfo->youtube;
					if (isset($xmlContactInfo->youtube['show']))
						$this->showYoutube = ((string)$xmlContactInfo->youtube['show'] == 'yes')?true:false;
				}
				if (property_exists($xmlContactInfo,"vimeo")){
					$this->vimeo = (string)$xmlContactInfo->vimeo;
					if (isset($xmlContactInfo->vimeo['show']))
						$this->showVimeo = ((string)$xmlContactInfo->vimeo['show'] == 'yes')?true:false;
				}
				
			}
			$categoryCount = 0;
			if (property_exists($xmlVendor,"categories")){
				$xmlCategories = $xmlVendor->categories;
				if (property_exists($xmlCategories,"category")){
					foreach($xmlVendor->categories->category as $category){
						array_push($this->categoryArray,urldecode((string)$xmlCategories->category[$categoryCount++]));
					}
				} 
			}	
			
		}
		
		public function showingAnySocialMedia(){
			$showing = false;
			foreach($this->socialMedia as $mediaEntry){
				if ($this->$mediaEntry != ""){
					$showEntry = "show".ucwords($mediaEntry);
					if ($this->$showEntry){
						$showing = true;
						break;
					}
				}
			}
			return $showing;
		}
		
		private function showItem($socialMediaItem){
			$show = 'no';
			switch ($socialMediaItem){
				case 'facebook':	
					$show = $this->showFacebook;
					break;
				case 'instagram':	
					$show = $this->showInstagram;
					break;
				case 'twitter':	
					$show = $this->showTwitter;
					break;
				case 'pintrist':	
					$show = $this->showPintrist;
					break;
				case 'linkedin':	
					$show = $this->showLinkedin;
					break;
				case 'googlePlus':	
					$show = $this->showGooglePlus;
					break;
				case 'youtube':	
					$show = $this->showYoutube;
					break;
				case 'vimeo':	
					$show = $this->showVimeo;
					break;
			}
			$show = ($show == true)?"yes":"no";
			return $show;
		}
		/*public function isLogoSingleLineTextAttrbuteSet(){
			return ($this->logo_singleLineText);	
		}*/
		
		public function canVendorBePosted(){
			$reasonsForNoPost = array();
			$canBePosted = true;
			foreach($this->requiredForLandingPage as $requiredField){
				if (strpos($requiredField,"Array") !== false){
					if (count($this->$requiredField) > 0){
						switch(gettype($this->$requiredField[0])){
							case "string":
								if ($this->$requiredField[0] == ""){
									$canBePosted = false;
									array_push($reasonsForNoPost,"Services Need To Be Listed"); 
								}
								break;
							case "object":
								$obj = $this->$requiredField[0];
								if (!$obj->isValid()){
									$canBePosted = false;
									array_push($reasonsForNoPost,"Phone Number not Valid");
								}
								break;	
						}
					}
				} // end if special array field
				if ($this->$requiredField == ""){
					array_push($reasonsForNoPost,"Invalid ".$requiredField);
					$canBePosted = false;
				}
			}
			$this->canBePosted = $canBePosted?"yes":"no";
			$this->reasonNoPost = implode(";",$reasonsForNoPost);
			return ($canBePosted);
		}
		
		public function hasService($service){
			$hasService = false;
			if (in_array($service,$this->categoryArray))
				$hasService = true;			
				
			return ($hasService);
		}
		
		private function toYesNO($field){
			$yesOrNo = YESNOTYPE::NO;
			if ($field === true)
				$yesOrNo = YESNOTYPE::YES;
				
			return $yesOrNo;
		}
		
		public function isAdmin(){		
			$isAdmin = $this->admin;	
			return ($isAdmin);
		}
		
		public function setAdminStatus($adminStatus){
			$this->admin = ($adminStatus == YESNOTYPE::YES)?true:false;	
		}
		
		public function textToNumber($textToWho){
			$textToNumber = false;
			foreach($this->phoneArray as $phone){
				if ($phone->allowTexting($textToWho) !== false){
					$textToNumber = $phone->getTextAddr();
					break;	
				}
			}
			return $textToNumber;	
		}
		
		private function generateUniqueID(){
			$vc = new vendorsClass('vendorList.xml');
			$this->id = $vc->createNewVendorID();
		}
		
		private function updateFilenamesAccordingToID(){
			$this->logo = $this->getAndSetUpdatedFileName($this->logo);
			$this->headshotImage = $this->getAndSetUpdatedFileName($this->headshotImage);
		}
		
		private function getAndSetUpdatedFileName($fileWithPath){
			//uploaded files will have some name from user.  So that files from different vendors don't conflict, name them according to thier ID value;
			$returnVal = $fileWithPath;
			if (($fileWithPath != "") && (file_exists($fileWithPath))){
				$pI = pathinfo($fileWithPath);
				$newFileName = $pI['dirname']."/".$this->id.".".$pI['extension'];
				if ($pI['filename'] != $this->id){
					if (rename($fileWithPath,$newFileName))
						$returnVal = $newFileName;
				}
			}else
				$returnVal = "";

			return $returnVal;
		}
		
		private function generateVendorPassword($length){
			$length = ($length)?$length:"";
			$length=(is_numeric($length))?(int)$length:6; 
			if ($length>20) $length=20;// capped at 20 chars max length 
			if ($length<4) $length=4;// capped at 4 chars min length 
		
			// characters that will be used 
			$str_upper="ABCDEFGHJKLMNPQRSTUVWXYZ"; 
			$str_lower.="abcdefghjkmnpqrstuvwxyz"; 
			$str_digit.="23456789"; 
			//$str_special.="~!@#$%^&*()_+[]\;',./~{}|:\"<>?"; 
				
			$use=$str_upper.$str_lower.$str_digit;//.$str_special; 
			$use_length=strlen($use)-1; 

			$password=''; 
	
			for($i=strlen($password); $i<$length; $i++){ 
				$password.=$use[rand(0,$use_length)]; 
			} 
	
			// shuffle the char order then print (with any special chars html safe) 
			$password = trim(htmlentities(str_shuffle($password)));//."\n"; 
			return ($password);
		} 
		
		public function save(){
			//echo "saving\n";
			$newVendor = false;
			if ($this->id == ""){
				$this->generateUniqueID();
				$newVendor = true;
			}
			//now rename any associatedFiles
			//$this->updateFilenamesAccordingToID();
			
			$xmlVendors = new vendorsClass('vendorList.xml');
			if ($newVendor)
				$xmlVendors->addVendor($this);
			else
			{
				//echo "Updating\n";
				$xmlVendors->updateVendor($this);
			}
			
			
		}
		
		private function getImageSize($imageToGet){
			$sizeArray = array(x=>0,y=>0,ratio=>0);
			if (file_exists($imageToGet)){
				$imgSize = getimagesize($imageToGet);
				if ($imgSize !== false){
					$sizeArray['x'] = $imgSize[0];
					$sizeArray['y'] = $imgSize[1];
					$sizeArray['ratio'] = $imgSize[0]/$imgSize[1];	
				}
			}
			return ($sizeArray);
		}
		public function getLogoSize(){
			return ($this->getImageSize($this->logo));
		}
		public function getHeadshotImageSize(){
			return ($this->getImageSize($this->headshotImage));
		}
		
		/*private function logo_singleLineText(){			
			$logoSingleLineAttrString = "";
			if (($this->logo != "") && (file_exists($this->logo)))
			{
				$image_info = getimagesize($this->logo); 
				$image_type = $image_info[2]; 
				$srcImage = NULL;
			
				if( $image_type == IMAGETYPE_JPEG ) { 
					$srcImage = imagecreatefromjpeg($this->logo); 
				} elseif( $image_type == IMAGETYPE_GIF ) {   
					$srcImage = imagecreatefromgif($this->logo); 
				} elseif( $image_type == IMAGETYPE_PNG ) {   
					$srcImage = imagecreatefrompng($this->logo); 
				}
			
				$orgWidth = imagesx($srcImage);
				$orgHeight = imagesy($srcImage);
			
				$singleLineTextResult = "no";
				if ($orgWidth/$orgHeight > 6)
					$singleLineTextResult = "yes";
					
				$logoSingleLineAttrString = ($singleLineTextResult == 'yes')?' singleLineText="yes"':"";
			}
			
			return ($logoSingleLineAttrString);
		}*/
		
		public function generateVendorXMLString(){
			$xmlStr = '<vendor id="'.$this->id.'" vendorType="'.$this->vendorType.'" vendorMembershipType="'.$this->vendorMembershipType.'">';
			$xmlStr .= '<name>'.urlencode($this->name).'</name>';
			$xmlStr .= '<password>'.trim($this->password).'</password>';
			$xmlStr .= '<categories>';
			foreach($this->categoryArray as $category){
				$xmlStr .= '<category>'.urlencode($category).'</category>';	
			}
			$xmlStr .= '</categories>';
			/*$xmlStr .= '<logo'.$this->logo_singleLineText().'>'.$this->logo.'</logo>';*/
			$xmlStr .= '<logo>'.$this->logo.'</logo>';
			$xmlStr .= '<contactInfo>';
			if (count($this->phoneArray) == 0)
				array_push($this->phoneArray,new vendorPhoneClass());
			$xmlStr .= '<phones>';
			foreach($this->phoneArray as $phone){
				$xmlStr .= $phone->generateXMLString();				
			}
			$xmlStr .= '</phones>';
			$xmlStr .= '<email>'.$this->email.'</email>';
			$xmlStr .= '<website>'.urlencode($this->website).'</website>';
			$xmlStr .= '<address>'.urlencode($this->address).'</address>';
			$xmlStr .= '<facebook show="'.$this->showItem("facebook").'">'.$this->facebook.'</facebook>';
			$xmlStr .= '<instagram show="'.$this->showItem("instagram").'">'.$this->instagram.'</instagram>';
			$xmlStr .= '<twitter show="'.$this->showItem("twitter").'">'.$this->twitter.'</twitter>';
			$xmlStr .= '<pintrist show="'.$this->showItem("pintrist").'">'.$this->pintrist.'</pintrist>';
			$xmlStr .= '<linkedin show="'.$this->showItem("linkedin").'">'.$this->linkedin.'</linkedin>';
			$xmlStr .= '<googlePlus show="'.$this->showItem("googlePlus").'">'.$this->googlePlus.'</googlePlus>';
			$xmlStr .= '<youtube show="'.$this->showItem("youtube").'">'.$this->youtube.'</youtube>';
			$xmlStr .= '<vimeo show="'.$this->showItem("vimeo").'">'.$this->vimeo.'</vimeo>';
			$xmlStr .= '</contactInfo>';
			$xmlStr .= '<headshotImage>'.$this->headshotImage.'</headshotImage>';
			$xmlStr .= '<introText>'.($this->introText).'</introText>';
			$xmlStr .= '<ticketToSavings amount="'.$this->ticketToSavingsAmount.'">'.$this->ticketToSavings.'</ticketToSavings>';
			$this->canVendorBePosted();
			$xmlStr .= '<canBePosted>'.$this->canBePosted.'</canBePosted>';
			$xmlStr .= '<reasonNoPost>'.$this->reasonNoPost.'</reasonNoPost>';
			$xmlStr .= '<admin>'.$this->toYesNO($this->admin).'</admin>';
			//eventually when I figure thisout	<galleries></galleries>
			$xmlStr .= '</vendor>';
			
			return $xmlStr;
		}
	

	}//end vendorProfileClass
	
	class vendorPhoneClass{
		public $type;
		public $allowClientsToText;
		public $allowTeamToText;
		public $textTypeAllowed;
		public $number;
		public $carrier;
		
		function __construct(){
			$a = func_get_args();
			$i = func_num_args();
			if (method_exists($this,$f='__construct'.$i)) {
				call_user_func_array(array($this,$f),$a);
			} 
		}
		
		private function initializeBlank(){
			$this->type = PHONETYPE::CELL;
			$this->allowClientsToText = false;
			$this->allowTeamToText = false;
			$this->textTypeAllowed = MESSAGETYPE::MMS;
			$this->number = "";
			$this->carrier = NULL;
		}
		
		private function __construct0(){
			//creates a blank new phone Class
			$this->initializeBlank();
		}
		
		private function __construct1($simpleXML_phoneXML){
			$this->type = PHONETYPE::CELL;
			if (isset($simpleXML_phoneXML["type"]))
				$this->type = (string)$simpleXML_phoneXML["type"];
			$this->allowClientsToText = false;
			if (isset($simpleXML_phoneXML["allowClientsToText"])){
				$this->allowClientsToText = ((string)$simpleXML_phoneXML["allowClientsToText"] == YESNOTYPE::YES)?true:false;
			}
			$this->allowTeamToText = false;
			if (isset($simpleXML_phoneXML["allowTeamToText"]))
				$this->allowTeamToText = ((string)$simpleXML_phoneXML["allowTeamToText"] == YESNOTYPE::YES)?true:false;;
			$this->textTypeAllowed = MESSAGETYPE::MMS;
			if (isset($simpleXML_phoneXML["textTypeAllowed"]))
				$this->textTypeAllowed = (string)$simpleXML_phoneXML["textTypeAllowed"];
			if (isset($simpleXML_phoneXML["carrierID"])){
				$carrierID = (string)$simpleXML_phoneXML["carrierID"];
				if (is_numeric(trim($carrierID))){
					$aC = new allCarriersClass();
					$this->carrier = $aC->findCarrierByID($carrierID);
				}
			}
			
			$this->number = (string)$simpleXML_phoneXML;	
		}
		
		private function __construct6($number,$type,$clientsText,$teamText,$textTypeAllowed,$carrier){
			$this->type = $type;
			$this->allowClientsToText = (($clientsText == YESNOTYPE::YES) || ($clientsText === true))?true:false;
			$this->allowTeamToText = (($teamText == YESNOTYPE::YES) || ($teamText === true))?true:false;
			$this->textTypeAllowed = $textTypeAllowed;
			$this->number = $number;
			$this->carrier = $carrier;
		}
		
		private function normalizeNumber(){
			$normalizedNumber = "";
			$normalizedNumber = preg_replace('/[^0-9]/','',$this->number);
			return $normalizedNumber;	
		}
	
		public function allowAnyTexting(){
			$allowed = $this->allowTexting(TEXTWHOTYPE::CLIENT);
			if ($allowed == false)
				$allowed = $this->allowTexting(TEXTWHOTYPE::TEAM);
			return $allowed;
		}
		
		public function allowTexting($textingType=TEXTWHOTYPE::CLIENT,$convertToYesNo=false){
			$textingAllowed = false;
			
			if (($this->type == PHONETYPE::CELL) && ($this->isValid())){
				$checkField = "allowClientsToText";
				if($textingType == TEXTWHOTYPE::TEAM)
					$checkField = "allowTeamToText";
				
				$textingAllowed = $this->$checkField;
			}
			
			if ($convertToYesNo === true)
				$textingAllowed = $this->toYesNO($textingAllowed);

			return $textingAllowed;
		}
		
		public function getTextAddr(){//($msgType = MESSAGETYPE::MMS){
			$textAddr = false;
			if ($this->isValid()){
				if (($this->type == PHONETYPE::CELL) && (!is_null($this->carrier)))
					$textAddr = $this->carrier->getTextNumber($this->normalizeNumber(),$this->textTypeAllowed);
			}
			
			if ($textAddr == "")
				$textAddr = false;
			return $textAddr;
		}
		
		public function isValid(){
			$vC = new validator();
			return ($vC->USA_PHONE($this->number));
		}
		
		private function toYesNO($field){
			$yesOrNo = YESNOTYPE::NO;
			if ($field === true)
				$yesOrNo = YESNOTYPE::YES;
				
			return $yesOrNo;
		}
		
		public function generateXMLString(){
			$carrierID = (is_null($this->carrier))?"":$this->carrier->id;
			$xmlStr = '<phone type="'.$this->type.'" allowClientsToText="'.$this->allowTexting(TEXTWHOTYPE::CLIENT,true).'" allowTeamToText="'.$this->allowTexting(TEXTWHOTYPE::TEAM,true).'" textTypeAllowed="'.$this->textTypeAllowed.'" carrierID="'.$carrierID.'">'.$this->number.'</phone>';
				return $xmlStr;	
		}
	}
	
	class allCarriersClass{
		public $carriers = array();
		
		private $emailToTextXMLString = "emailToTextInfo.xml";
		private $xmlCarriers = NULL;
		
		
		function __construct(){
			$this->loadClassFromFile();
		}
		
		public function loadClassFromFile(){
			$this->carriers = array();
			if (file_exists($this->emailToTextXMLString)) {
				$this->xmlCarriers = simplexml_load_file($this->emailToTextXMLString);
				foreach($this->xmlCarriers->carrier as $carrier){
					$emailToTextCarrier = new carrierClass($carrier);
					array_push($this->carriers,$emailToTextCarrier);				
				}	
			}else{
				$this->$errMessages = "Failed to Load ".$this->emailToTextXMLString;
			}
		}
		
		public function findCarrier($carrierToFind){
			$carrierFound = NULL;
			foreach($this->carriers as $carrier){
				if ($carrier->carrierName == $carrierToFind){
					$carrierFound = $carrier;
					break;	
				}
			}
			return ($carrierFound);
		}
		
		public function findCarrierByID($carrierIDToFind){
			$carrierFound = NULL;
			foreach($this->carriers as $carrier){
				if ($carrier->id == $carrierIDToFind){
					$carrierFound = $carrier;
					break;	
				}
			}
			return ($carrierFound);
		}
		
		public function getSelectCode($nameIDField="",$class="",$phoneObj=NULL,$useDefault=true,$defaultText="Select Carrier",$defaultValue="-1"){
			$preselectedID = "-1";
			if (!is_null($phoneObj)){
				if (!is_null($phoneObj->carrier))	
					$preselectedID = $phoneObj->carrier->id;
			}
			$selected = ($defaultValue==$preselectedID)?" selected":"";
			$defaultOption = $useDefault?'<option value="'.$defaultValue.'"'.$selected.'>'.$defaultText.'</option>':"";
			$selectCode = '<SELECT id="'.$nameIDField.'" name="'.$nameIDField.'" class="'.$class.'">'.$defaultOption;
			foreach($this->carriers as $carrier){
				if (isset($carrier->carrierName)){
					$selected = ($carrier->id==$preselectedID)?" selected":"";
					$selectCode .= '<option value="'.$carrier->id.'"'.$selected.'>'.$carrier->carrierName.'</option>';
				}
			}
			$selectCode .= '</SELECT>';
			return ($selectCode);
		}
		
		public function createNewCarrierID(){
			$highestNumber = 0;
			foreach($this->carriers as $carrier){
				if ($carrier->id > $highestNumber)
					$highestNumber = $carrier->id;	
			}
			$highestNumber++;
			return $highestNumber;
		}
		
		public function updateCarrier($carrierToUpdate){
			for($i=0; $i<count($this->carriers); $i++){
				$originalCarrier = $this->carriers[$i];
				if ($originalCarrier->id == $carrierToUpdate->id){
					$this->carriers[$i] = $carrierToUpdate;
					break;	
				}
			}
			
			$this->generateNewXMLString();
			$this->xmlCarriers->asXML($this->emailToTextXMLString);
			$this->loadClassFromFile();
		}
		
		public function addCarrier($newCarrier){
			array_push($this->carriers,$newCarrier);
			$this->generateNewXMLString();
			$this->xmlCarriers->asXML($this->emailToTextXMLString);
			$this->loadClassFromFile();
		}
		
		public function generateXMLString(){
			$xmlStr = '<emailToTextInfo>';
			foreach($this->carriers as $carrier){
				$xmlStr .= $carrier->generateXMLString();	
			}
			$xmlStr .= '</emailToTextInfo>';
			return $xmlStr;	
		}
	}
	
	class carrierClass{
		public $smsEmailExt = "";
		public $mmsEmailExt = "";
		public $carrierName = "";
		public $id = "";
		
		function __construct(){
			$a = func_get_args();
			$i = func_num_args();
			if (method_exists($this,$f='__construct'.$i)) {
				call_user_func_array(array($this,$f),$a);
			} 
		}
		
		private function __construct0(){
			//create empty Carrier	
		}
		
		private function __construct1($simpleXML_carrierXML){
			$this->id = "";
			if (isset($simpleXML_carrierXML["id"]))
				$this->id = (string)$simpleXML_carrierXML["id"];
			$this->smsEmailExt = "";
			if (isset($simpleXML_carrierXML["smsExt"]))
				$this->smsEmailExt = (string)$simpleXML_carrierXML["smsExt"];
			$this->mmsEmailExt = "";
			if (isset($simpleXML_carrierXML["mmsExt"]))
				$this->mmsEmailExt = (string)$simpleXML_carrierXML["mmsExt"];
			$this->carrierName = (string)$simpleXML_carrierXML;	
		}
		
		private function __construct3($smsExt,$mmsExt,$name){
			$this->smsEmailExt = $smsExt;
			$this->mmsEmailExt = $mmsExt;
			$this->carrierName = $name;
		}
		
		public function getTextNumber($normalizedNumber,$messagingType=MESSAGETYPE::MMS){
			$textNumber = false;
			$nN = $normalizedNumber;
			if (is_numeric(trim($nN)) && preg_match('/^\d{10}$/', $nN)){
				if (($messagingType == MESSAGETYPE::SMS) && ($this->smsEmailExt != ""))
					$textNumber = $nN.$this->smsEmailExt;
				if (($messagingType == MESSAGETYPE::MMS) && ($this->mmsEmailExt != ""))
					$textNumber = $nN.$this->mmsEmailExt;
			}
			return ($textNumber);
		}
		
		private function generateUniqueID(){
			$carriers = new allCarriersClass();
			$this->id = $carriers->createNewCarrierID();
		}
		
		public function save(){
			//echo "saving Carrier\n";
			$newCarrier = false;
			if ($this->id == ""){
				$this->generateUniqueID();
				$newCarrier = true;
			}
			
			$xmlCarriers = new allCarriersClass();
			if ($newCarrier)
				$xmlCarriers->addCarrier($this);
			else
			{
				//echo "Updating\n";
				$xmlCarriers->updateCarrier($this);
			}
		}
		
		public function generateXMLString(){
			$xmlStr = '<carrier id="'.$this->id.'" smsExt="'.$this->smsEmailExt.'" mmsExt="'.$this->mmsEmailExt.'">'.$this->carrier.'</carrier>';
			return $xmlStr;	
		}
	}

?>