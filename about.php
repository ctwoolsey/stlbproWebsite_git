<!doctype html>
<html>
<head>
<?
include_once('vendorsClass.php');
include_once('scripts/Mobile-Detect-2.8.11/Mobile_Detect.php');
$mobileDetect = new Mobile_Detect();
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,  user-scalable=no"/>
<title>About Page - St. Louis Bridal Professionals</title>
<script src="scripts/jquery/jquery-1.11.2.min.js"></script>
<link href="html5reset-1.6.1.css" rel="stylesheet" type="text/css">
<link href="all.css" rel="stylesheet" type="text/css">
<!--<link href="desktop.css" media="screen and (min-width:900px)" rel="stylesheet" type="text/css">-->
<link href="desktop.css" media="screen and (min-width:400px)" rel="stylesheet" type="text/css">
<!--<link href="tablet.css" media="screen and (min-width:400px) and (max-width:899px)" rel="stylesheet" type="text/css">-->
<!--<link href="phone.css" media="screen and (max-width:399px)" rel="stylesheet" type="text/css">-->
<link href="phone.css" media="screen and (max-width:399px)" rel="stylesheet" type="text/css">
<link href="vendorContactForm.css" rel="stylesheet" type="text/css">
</head>

<body>
<?
$vpc = new vendorsClass('vendorList.xml');

?>
<div id="bodyWrapper">
<header>
	<img class="header-background-image" src="images/header_800x353.jpg"/>
    <img class="header-background-image-phone" src="images/header_400x177.jpg"/>
</header>
<section class="navMenu">
	<a id="about_phone" class="navButton navButtonFirst" href="about.php">
        <img class="mainImage" src="images/mainPageMainImages/about2.jpg"/>
    </a>
    <a id="contactUs_phone" class="navButton" href="contactUs.php">
        <img class="mainImage" src="images/mainPageMainImages/contact2.jpg"/>
    </a>
    <a id="vendors_phone" class="navButton" href="vendors.php">
        <img class="mainImage" src="images/mainPageMainImages/vendors2.jpg"/>
    </a>
    <a id="aboutTicketToSavings" class="navButton" href="aboutTicketToSavings.php">
        <img class="mainImage" src="images/mainPageMainImages/ticketToSavings2.jpg"/>
    </a>
</section>
<section class="navMenu-phone">
	<div id="about_phone" class="navButton-phone navButtonFirst-phone">
    	About
    </div>
    <div id="sub_about_phone" class="navIncludedText-phone navButtonSubGroup-phone subGroupHidden-phone">
    	<? include('stlbridalprosAboutText.txt');?>
    </div>
    <div id="contactUs_phone" class="navButton-phone">
    	Contact Us
    </div>
    <div id="sub_contactUs_phone" class="navButtonSubGroup-phone subGroupHidden-phone">
    	<? include('vendorContactForm.php'); ?>
    </div>
    <div id="vendors_phone" class="navButton-phone">
    	Vendors
    </div>
    <div id="sub_vendors_phone" class="navButtonSubGroup-phone subGroupHidden-phone">
	<?php
        foreach($vpc->vendors as $vendor){
				$singleLineText = "";
				if ($vendor->isLogoSingleLineTextAttrbuteSet() == "yes")
					$singleLineText = " vendorButtonLogoSingleTextLine-phone";
               //for debug initially $showVendorClass = " hidden";
				if ($vendor->canVendorBePosted())
					$showVendorClass = "";
				?>
                <div class="vendorButton-phone<?=$showVendorClass?>" id="vendorButton_<?=$vendor->id?>">
					<img class="vendorButtonLogo-phone<?=$singleLineText?>" src="<?=$vendor->logo?>"/>
                    <input type="hidden" class="landingPageInput" value="<?=$vendor->landingPage?>"/>
                    <input type="hidden" class="landingPageID" value="<?=$vendor->id?>"/>
				</div><br>
				<?
        }
	?>
    </div>
    <div id="aboutTicketToSavings_phone" class="navButton-phone">
    	<img class="navButtonTicketToSavingsImg-phone" src="images/singleTicketToSavingsPink_200x141.gif"/>Ticket to Savings
    </div>
    <div id="sub_aboutTicketToSavings_phone" class="navIncludedText-phone navButtonSubGroup-phone subGroupHidden-phone">
    	<? include('aboutTicketToSavingsText.txt'); ?>
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
</footer>
</div> <!-- end #bodyWrapper -->
<script>
	$( document ).ready(function() {
    	$(".navButton-phone").click(function(e) {
			var clickedID = $(this).prop("id");
            $(".navButton-phone").each(function(index, element) {
				var currentID = $(this).prop("id");
				var subID = "sub_"+currentID;
                if ($(this).prop("id") == clickedID){
					$("#"+subID).toggleClass("subGroupHidden-phone");
					$(this).toggleClass("mainNavButtonSelected-phone");
				}else{
					$("#"+subID).addClass("subGroupHidden-phone");
					$(this).removeClass("mainNavButtonSelected-phone");
				}
            });
        });
		
		inFieldLabels();
		
		$(".vendorButton-phone").each(function(){
			//var landingPage = $(this).find(".landingPageInput").val();
			var landingPageID = $(this).find(".landingPageID").val();
			$(this).on("click",function(e){
				//window.location.href = landingPage;	
				window.location.href = "vendorLandingPage.php?vid="+landingPageID;
			});	
		});
		
		initialize_menuShow("<?=$menuIDKey?>");

	});
	
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
		if (menuID != "")
			$("#"+menuID).click();	
};

</script>

</body>

</html>
