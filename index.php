<?
	session_start();
	$use_altOpen = isset($_SESSION['menuOpen'])?$_SESSION['menuOpen']:false;
		$menuIDKey = "";
	if (isset($_REQUEST['m']) && $use_altOpen){
		$menuIDKey = $_REQUEST['m'];
		session_unset();
	}
?>
<!doctype html>
<html>
<head>
<?
include_once('vendorsClass.php');
include_once('scripts/Mobile-Detect-2.8.11/Mobile_Detect.php');
$mobileDetect = new Mobile_Detect();
?>
<script>
	var logoSizes = Array();
	var showingContentFrom = false;
	var appendToLocation = "";
</script>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,  user-scalable=no"/>
<title>St. Louis Bridal Professionals</title>
<script src="scripts/jquery/jquery-1.11.2.min.js"></script>
<script src="scripts/validationClass/validator.js"></script>
<script src="scripts/jquery/vendorContactFormScripts.js"></script>
<script src="//use.edgefonts.net/iceberg;alfa-slab-one.js"></script>
<link href="html5reset-1.6.1.css" rel="stylesheet" type="text/css">
<link href="all.css" rel="stylesheet" type="text/css">
<!--<link href="desktop.css" media="screen and (min-width:900px)" rel="stylesheet" type="text/css">-->
<link href="desktop.css" media="screen and (min-width:400px)" rel="stylesheet" type="text/css">
<!--<link href="tablet.css" media="screen and (min-width:400px) and (max-width:899px)" rel="stylesheet" type="text/css">-->
<!--<link href="phone.css" media="screen and (max-width:399px)" rel="stylesheet" type="text/css">-->
<link href="phone.css" media="screen and (max-width:399px)" rel="stylesheet" type="text/css">
<link href="vendorContactForm.css" rel="stylesheet" type="text/css">
<link href="joinUsForm.css" rel="stylesheet" type="text/css">
</head>

<body>
<?
$vpc = new vendorsClass('vendorList.xml');

?>
<div id="bodyWrapper">
	<div id="overlayBackgroundDim" class="hidden">
	</div>
    <div id="overlayWrapper" class="hidden">
        <div id="overlay">
        	<img id="overlayClose" src="images/circleRedXClose_50x50.png" onClick="closeOverlay(true);"/>
            <div id="overlayContentHolder">
            	<div id="overlayContent"></div>
            </div>
        </div>
    </div>
    <header>
        <img class="header-background-image" src="images/header_800x353.jpg"/>
        <img class="header-background-image-phone" src="images/header_400x177.jpg"/>
    </header>
    
    <section class="navMenu">
        <div id="about" class="navButton navButtonFirst" onClick="showContent(this);">
            <img class="mainImage" src="images/mainPageMainImages/about2.jpg"/>
        </div>
        <div id="contactUs" class="navButton" onClick="showContent(this);">
            <img class="mainImage" src="images/mainPageMainImages/contact2.jpg"/>
        </div>
        <div id="vendors" class="navButton" onClick="showContent(this);">
            <img class="mainImage" src="images/mainPageMainImages/vendors2.jpg"/>
        </div>
        <div id="aboutTicketToSavings" class="navButton" onClick="showContent(this);">
            <img class="mainImage" src="images/mainPageMainImages/ticketToSavings2.jpg"/>
        </div>
    </section>
    
    <section class="navMenu-phone">
        <div id="about_phone" class="navButton-phone navButtonFirst-phone" onClick="showContent(this);">
            About
        </div>
        <div id="sub_about_phone" class="navIncludedText-phone navButtonSubGroup-phone subGroupHidden-phone">
            <? //include('stlbridalprosAboutText.txt');?>
        </div>
        <div id="contactUs_phone" class="navButton-phone" onClick="showContent(this);">
            Contact Us
        </div>
        <div id="sub_contactUs_phone" class="navButtonSubGroup-phone subGroupHidden-phone">
            <? //include('vendorContactForm.php'); ?>
        </div>
        <div id="vendors_phone" class="navButton-phone" onClick="showContent(this);">
            Vendors
        </div>
        <div id="sub_vendors_phone" class="navButtonSubGroup-phone subGroupHidden-phone">
        
        </div>
        <div id="aboutTicketToSavings_phone" class="navButton-phone" onClick="showContent(this);">
            <img class="navButtonTicketToSavingsImg-phone" src="images/singleTicketToSavingsPink_200x141.gif"/>Ticket to Savings
        </div>
        <div id="sub_aboutTicketToSavings_phone" class="navIncludedText-phone navButtonSubGroup-phone subGroupHidden-phone">
        </div>
        <div id="joinUs_phone" class="navButton-phone" onClick="showContent(this);">
            Join Our Team
        </div>
        <div id="sub_joinUs_phone" class="navIncludedText-phone navButtonSubGroup-phone subGroupHidden-phone">
        </div>
    </section>
    <footer>
        <?
            $startYear = 2015;
            $endYear = date("Y");
            $copyDate = $startYear;
            if (($endYear - $startYear) > 0)
                $copyDate .= "-".$endYear;
        ?>
        <p id="copyright">&copy;<?=$copyDate?> Clark Woolsey </p>
        <div id="vendorloginDiv">
            <a href="vendorLogin.php">
            <img id="vendorLoginIcon" src="images/adminLogin.gif"/>
            </a>
        </div>
        <img id="joinUs" src="images/joinUs.png" onClick="showContent(this)"/>
    </footer>
</div> <!-- end #bodyWrapper -->

<div id="displayMode" class="hidden"></div> <!--width 10px for desktop & 20px for phone -->
<div id="aboutTextContent" class="hidden">
	<span class="wrap">
	<? include('stlbridalprosAboutText.txt');?>
    </span>
</div>
<div id="contactContent" class="hidden">
	<span class="wrap">
	<? include('vendorContactForm.php');?>
    </span>
</div>
<div id="vendorListContent" class="hidden">
	<span class="wrap">
	<?php
			$vendorCount = 0;
            foreach($vpc->postableVendorsNonVenue as $vendor){
      		//foreach($vpc->vendors as $vendor){
				if ($vendor->vendorType == VENDORTYPE::NONVENUE){
						$imgSizeArr = $vendor->getLogoSize();
						?>
                        	<script>
								logoSizes.push({'x':<?=$imgSizeArr['x']?>,'y':<?=$imgSizeArr['y']?>,'ratio':<?=$imgSizeArr['ratio']?>});
							</script>

                    <div class="vendorButton">
                        <img class="vendorButtonLogo" src="<?=$vendor->logo?>"/>
                        <input type="hidden" class="landingPageID" value="<?=$vendor->id?>"/>
                    </div>
       <?        }   
            }
     ?>
     <?
	 		if (count($vpc->postableVendorsVenue) > 0){
	 ?>			
				<div class="venueDivider">
                	<p id="preferredVenueText"> Our Preferred Venues are:<p>
                </div>	 
	 
	 <?			
			}
			$vendorCount = 0;
            foreach($vpc->postableVendorsVenue as $vendor){
      		//foreach($vpc->vendors as $vendor){
				if ($vendor->vendorType == VENDORTYPE::VENUE){
						$imgSizeArr = $vendor->getLogoSize();
						?>
                        	<script>
								logoSizes.push({'x':<?=$imgSizeArr['x']?>,'y':<?=$imgSizeArr['y']?>,'ratio':<?=$imgSizeArr['ratio']?>});
							</script>

                    <div class="vendorButton">
                        <img class="vendorButtonLogo" src="<?=$vendor->logo?>"/>
                        <input type="hidden" class="landingPageID" value="<?=$vendor->id?>"/>
                    </div>
       <?       }    
            }
     ?>
     </span>
</div>
<div id="ticketToSavingsTextContent" class="hidden">
	<span class="wrap">
    <img id="ticketToSavingsBackgroundImage" src="images/ticketToSavingsBackgroundImage.gif"/>
   	<header class="ticketToSavingsHeader">
    	Ticket<br>
        <p class="ticketToSavingsSubHeader">
        	To Savings
        	<img id="tks2SavStar_L1" src="images/ticketToSavingsStar_40x38.png"/>
            <img id="tks2SavStar_R1" src="images/ticketToSavingsStar_40x38.png"/>
            <img id="tks2SavStar_L2" src="images/ticketToSavingsStar_40x38.png"/>
            <img id="tks2SavStar_R2" src="images/ticketToSavingsStar_40x38.png"/>
          	<img id="tks2SavStar_L3" src="images/ticketToSavingsStar_40x38.png"/>
            <img id="tks2SavStar_R3" src="images/ticketToSavingsStar_40x38.png"/>
        </p>
    </header>
		<div class="ticketToSavingsText"><? include('aboutTicketToSavingsText.txt');?>
            <br><div id="ticketToSavingsUnderTextDiv">
					<? include("ticketToSavingsCalculator.php");?>
                </div>
		</div>
    </span>
</div>
<div id="joinUsContent" class="hidden">
	<span class="wrap">
		<? include('joinUsForm.php');?>
    </span>
</div>

<script>
	var mode;
	$( document ).ready(function() {
		mode = getDisplayMode();
		inFieldLabels();
		initializeTicket2SavCalc();
	
		$(".vendorButton").each(function(){
			var landingPageID = $(this).find(".landingPageID").val();
			$(this).on("click",function(e){
				window.location.href = "vendorLandingPage.php?vid="+landingPageID;
			});	
		});
		
		$(window).on("resize",function(evt){	
			var prevMode = mode;
			if (getDisplayMode() != prevMode){ //mode change phone to dT or visa versa
				if (mode == "phone"){ //was dT
					//remove any overlays and restore content
					if (!$("#overlayBackgroundDim").hasClass("hidden")){
						closeOverlay(false);
					}
				}else{
					if ($(".mainNavButtonSelected-phone").length > 0){
						var selectedButton = $(".mainNavButtonSelected-phone");
						closePhoneMenu(selectedButton,false);
					}
				}
				$(getAppendToIDFromShowingContent(mode)).click();
			}else{ 
				//no mode change
			
				if (!$("#overlayBackgroundDim").hasClass("hidden"))
					setOverlayWindowHeights();
				setOverlayLogoDimensions();
				setTicketToSavingsDimensions(); 
			}
		});
		
		initialize_menuShow("<?=$menuIDKey?>");

	});

function closePhoneMenu(buttonToClose,clearShowingContentFrom){
		var previouslySelectedID = $(buttonToClose).prop("id");
		var showingContent = getShowingContentFromAppendToID(previouslySelectedID);
		$("#sub_"+previouslySelectedID).addClass("subGroupHidden-phone");
		$("#"+previouslySelectedID).removeClass("mainNavButtonSelected-phone");
		
		$("#sub_"+previouslySelectedID).find(".wrap").appendTo("#"+showingContent);
		
		if (clearShowingContentFrom)
			showingContentFrom = "";
}

function closeOverlay(clearShowingContentFrom){
	$("#overlayBackgroundDim").addClass("hidden");
	$("#overlayWrapper").addClass("hidden");
	$(appendToLocation).find(".wrap").appendTo("#"+showingContentFrom);					
	if (clearShowingContentFrom)
		showingContentFrom = "";
}

function getDisplayMode(){
	mode = "desktop";
	if ($("#displayMode").css("width") == "20px"){
		mode = "phone";	
	}
	return (mode);
}

function getAppendToIDFromShowingContent(modeToGet){
	var appendID = "";
	switch (showingContentFrom){
		case 'aboutTextContent':
			appendID = "#about_phone";
			break;
		case 'contactContent':
			appendID = "#contactUs_phone";
			break;
		case 'vendorListContent':
			appendID = "#vendors_phone";
			break;
		case 'ticketToSavingsTextContent':
			appendID = "#aboutTicketToSavings_phone";
			break;	
		case 'joinUsContent':
			appendID = "#joinUs_phone";
			break;
		default:
			appendID = "";
			break;	
	}	
	if (modeToGet == "desktop")
		appendID = appendID.replace("_phone","");
	return (appendID);
}

function getShowingContentFromAppendToID(appendToID){
	var contentFrom = "";
	switch (appendToID){
		case 'about':
		case 'about_phone':
			contentFrom = "aboutTextContent";
			break;
		case 'contactUs':
		case 'contactUs_phone':
			contentFrom = "contactContent";
			break;
		case 'vendors':
		case 'vendors_phone':
			contentFrom = "vendorListContent";
			break;
		case 'aboutTicketToSavings':
		case 'aboutTicketToSavings_phone':
			contentFrom = "ticketToSavingsTextContent";
			break;
		case 'joinUs':
		case 'joinUs_phone':
			contentFrom = "joinUsContent";	
			break;
		default:
			contentFrom = "";
			break;	
	}
	return (contentFrom);
}

function messageInOverlay(messageText){
	$("#overlayContent").html(messageText);
	$("#overlayBackgroundDim").removeClass("hidden");
	$("#overlayWrapper").removeClass("hidden");
	setOverlayWindowHeights();	
}

function showContent(divClicked){
	$("#overlayContent").empty();
	appendToLocation = "#overlayContent";
	var clickedID = $(divClicked).prop("id");
	
	if (getDisplayMode() == "phone")
		appendToLocation = "#sub_"+clickedID;	
	
	showingContentFrom = getShowingContentFromAppendToID(clickedID);	
	
	if (showingContentFrom != ""){
		$("#"+showingContentFrom).find(".wrap").appendTo(appendToLocation);
	}
	
	if (mode == "desktop"){
		$("#overlayBackgroundDim").removeClass("hidden");
		$("#overlayWrapper").removeClass("hidden");
		setOverlayWindowHeights(true);
	}else{ //phone
		$(".navButton-phone").each(function() {
			var currentID = $(this).prop("id");
			var subID = "sub_"+currentID;
			if ($(this).prop("id") == clickedID){
				if ($(this).hasClass("mainNavButtonSelected-phone"))
					closePhoneMenu(this,true);
				else{
					$("#"+subID).toggleClass("subGroupHidden-phone");
					$(this).toggleClass("mainNavButtonSelected-phone");
				}
			}else{
				closePhoneMenu(this,false);
			}
		});
	}
	setOverlayLogoDimensions();
	setTicketToSavingsDimensions();
}

function setTicketToSavingsDimensions(){
	if (mode == "desktop"){
		if (showingContentFrom == 'ticketToSavingsTextContent'){
			var parentWidth = $("#overlayContent").width();
			var parentHeight = $("#overlayContentHolder").height() + 20;
			$("#ticketToSavingsBackgroundImage").css({width:parentWidth,height:parentHeight});
			$("#overlay").addClass("ticketToSavingsHideBackground");
		}
		else{
			$("#overlay").removeClass("ticketToSavingsHideBackground");
		}
	}else{ //mode = phone
		if (showingContentFrom == 'ticketToSavingsTextContent'){
			var parentElement = $(".mainNavButtonSelected-phone").next();
			var padding = $(parentElement).css("paddingLeft").replace("px","");
			var width = Number($("#aboutTicketToSavings_phone").width()) - 2*Number(padding);
			var parentHeight = $(parentElement).height();
			//console.log("Padding: "+padding+"      Mainw: "+$("#aboutTicketToSavings_phone").width()+"     w: "+width);
			$("#ticketToSavingsBackgroundImage").css({width:width,height:parentHeight,left:padding});
		}
	
	}
}

function setOverlayLogoDimensions(){
	if (showingContentFrom == 'vendorListContent'){
		var firstVB = $(".vendorButton").first();
		var vendorBoxRatio = Number($(firstVB).width())/Number($(firstVB).height());
		var vendorBox_X = $(firstVB).width();
		var vendorBox_Y = $(firstVB).height();
		
		var vendorCount = 0;
		$(".vendorButton").each(function(){
			var logoSizeObj = logoSizes[vendorCount++];
			var vendorLogo = $(this).find(".vendorButtonLogo");
			if (logoSizeObj.ratio < vendorBoxRatio){ //need to resize based on height and width will be fine
				$(vendorLogo).css({height:35,marginTop:2,width:''});
			}else{ //resize based on width and height will fit
				var newX = vendorBox_X*.95;
				var newY = newX/logoSizeObj.ratio;
				var margTop = (vendorBox_Y-newY)/2;
				$(vendorLogo).css({height:'',marginTop:margTop,width:'95%'});
			}
		});
		if (mode=="desktop"){
			$("#overlay").addClass("vendorOverlayBackgroundColor");
		}
		else{
			$("#overlay").removeClass("vendorOverlayBackgroundColor");
		}
	}
}

function setOverlayWindowHeights(setTopPosition){
	if (setTopPosition){
		$("#overlay").css("marginTop",30+Number(window.pageYOffset));	
	}
	$("#overlayBackgroundDim").css("height","100%");
	var overlayHeight = Number($("#overlay").css("marginTop").replace("px","")) + Number($("#overlay").outerHeight()) +  Number($("#overlay").css("marginBottom").replace("px",""));
	if (overlayHeight > $("#overlayBackgroundDim").height())
		$("#overlayBackgroundDim").css("height",overlayHeight);
		
}

function initialize_menuShow(menuIDKey){
		menuIDKey = (menuIDKey)?menuIDKey:"";
		var menuID = "";
		switch(menuIDKey){
			case "about":
				menuID = "about_phone";
				break;
			case "contactUs":
				menuID = "contactUs_phone";
				break;
			case "vendors":
				menuID = "vendors_phone";
				break;
			case "TK2S":
				menuID = "aboutTicketToSavings_phone";
				break;
			defualt:
				menuID = "";
				break;
		}
		if (getDisplayMode() != "phone"){
			menuID = menuID.replace("_phone","");	
		}
		
		if (menuID != "")
			$("#"+menuID).click();	
};

</script>

</body>

</html>
