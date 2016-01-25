<?
	session_start();
	$_SESSION['menuOpen'] = true;
	
?>
<!doctype html>
<html>
<head>
<? include_once('vendorsClass.php'); 
include_once('scripts/Mobile-Detect-2.8.11/Mobile_Detect.php');
$mobileDetect = new Mobile_Detect();
$vendorID=$_REQUEST['vid'];

?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,  user-scalable=no"/>
<title>St. Louis Bridal Professionals - Vendors</title>
<script src="scripts/jquery/jquery-1.11.2.min.js"></script>
<!--<script src="scripts/jquery/vendorContactFormScripts.js"></script>-->
<link href="html5reset-1.6.1.css" rel="stylesheet" type="text/css">
<link href="all.css" rel="stylesheet" type="text/css">
<!--<link href="desktop.css" media="screen and (min-width:900px)" rel="stylesheet" type="text/css">
<link href="tablet.css" media="screen and (min-width:400px) and (max-width:899px)" rel="stylesheet" type="text/css">
<link href="phone.css" media="screen and (max-width:399px)" rel="stylesheet" type="text/css"> -->
<link href="vendorLandingPage.css" rel="stylesheet" type="text/css">
<link href="vendorLandingPage_NavMenu.css" rel="stylesheet" type="text/css">
</head>

<body>
<?
$vpc = new vendorsClass('vendorList.xml');
$vendor = $vpc->getVendorByID($vendorID);
?>

<header>
	<div id="header_innerWrap">
        <div id="groupLogo">
            <a href="http://www.stlbridalpros.com?m=vendors"><img id="groupLogoImg" src="images/logo_black_523_450.gif"/></a>
        </div>
        <div id="vendorLogoHolder">
            <img id="vendorHeaderImage" src="<?=$vendor->logo?>"/>
        </div>
    </div>
    
</header>
<section id="midSection">

	<section class="introText">
            <p><?=$vendor->introText?></p>
    </section>
    <br/><section class="ticketForSavings">
    <?
		if ($vendor->ticketToSavings != ""){
	?>
        <p><img id="ticketToSavingsImage_Front" src="images/singleNonRotatedTicket_100x41.png""/></p><h1>My Ticket To Savings</h1><p><img id="ticketToSavingsImage_Back" src="images/singleNonRotatedTicket_100x41.png"/></p>
        <br/><p><b><i><?=$vendor->ticketToSavings?></i></b></p>
        <?
			if (($vendor->ticketToSavingsAmount != 0) && ($vendor->ticketToSavingsAmount != "")) {
		?>
        		<br/><p class="savings">This represents a savings up to $<?=$vendor->ticketToSavingsAmount?>!</p>
			<?
            }
			?>
        <br/><p class="ticketToSavingsDetails"><i>Sign up with me and at least two other vendors listed on this website to receive the ticket to savings.<span class="footnote">&#1645;</span></i></p>
        <?
		} //end if ticketToSavings != ""
			?>
    </section>
    <section class="contactInformation">
        <?
		//	if ($vendor->headshotImage != ""){
		//		echo '<img id="headshotImage" src="'.$vendor->headshotImage.'"/><br/><br/>';	
		//	}
		?>
		<?
            if ($vendor->headshotImage != ""){
                echo '<img id="headshotImage" src="'.$vendor->headshotImage.'"/>';	
            }
        ?>
        <section class="contactInfoOnly">
        <p><h1>Contact Information</h1></p>
        <?
        if ($vendor->website != ""){
			?>
			<p class="contactInfoData"><b>website: </b><a href="http://<?=$vendor->website?>" target="new"><?=$vendor->website?></a></p>
			<?
		}
        	foreach($vendor->phoneArray as $phone){
				if ($phone->number != ""){
					?><p class="contactInfoData"><b>phone<?=($phone->type == PHONETYPE::CELL)?" (cell)":""	?>: </b><a href="tel://<?=$phone->number?>"><?=$phone->number?></a></p><?
					if ($phone->allowTexting(TEXTWHOTYPE::CLIENT)){
						if ($mobileDetect->isMobile()){
							?><p class="contactInfoData"><b>phone (text): </b><a href="sms:/<?=$phone->number?>">click to text me</a></p><?	
						}
					}
				} //end if phone->number != ""
			}
			if ($vendor->email != ""){
			?>
            <p class="contactInfoData"><b>email: </b><a href="mailto:<?=$vendor->email?>"><?=$vendor->email?></a></p>
            <?
            }
			if (($vendor->address != "") && ($mobileDetect->isMobile())){
            ?><p class="contactInfoData"><b>address: </b><a href="maps://<?=$vendor->address?>"><?=$vendor->address?></a></p>
            <?
            }else{
				if ($vendor->address != ""){
			?><p class="contactInfoData"><b>address: </b><a href="http://www.bing.com/maps/default.aspx?encType=1&where1=<?=$vendor->address?>" target="new"><?=$vendor->address?></a></p>
			<?
				}
			}
			?>
            </section> <!--end ContactInfoOnly -->
           <?
		   	if ($vendor->showingAnySocialMedia()){
		   ?>
            <br/><p id="socialMedia">
            <?
			if (($vendor->facebook != "") && ($vendor->showFacebook)){
			?><span><a href="<?=$vendor->facebook?>" target="new"><img class="socialMediaIcon" src="images/socialMedia/facebook.gif"/></a></span>
            <?
			}
			if (($vendor->instagram != "") && ($vendor->showInstagram)){
			?><span><a href="<?=$vendor->instagram?>" target="new"><img class="socialMediaIcon" src="images/socialMedia/instagram.gif"/></a></span>
            <?
			}
			if (($vendor->twitter != "") && ($vendor->showTwitter)){
			?><span><a href="<?=$vendor->twitter?>" target="new"><img class="socialMediaIcon" src="images/socialMedia/twitter.gif"/></a></span>
            <?
			}
			if (($vendor->pintrist != "") && ($vendor->showPintrist)){
			?><span><a href="<?=$vendor->pintrist?>" target="new"><img class="socialMediaIcon" src="images/socialMedia/pintrist.gif"/></a></span>
            <?
			}
			if (($vendor->linkedin != "") && ($vendor->showLinkedin)){
			?><span><a href="<?=$vendor->linkedin?>" target="new"><img class="socialMediaIcon" src="images/socialMedia/linkedin.gif"/></a></span>
            <?
			}
			if (($vendor->googlePlus != "") && ($vendor->showGooglePlus)){
			?><span><a href="<?=$vendor->googlePlus?>" target="new"><img class="socialMediaIcon" src="images/socialMedia/googlePlus.gif"/></a></span>
            <?
			}
			if (($vendor->youtube != "") && ($vendor->showYoutube)){
			?><span><a href="<?=$vendor->youtube?>" target="new"><img class="socialMediaIcon" src="images/socialMedia/youTube.gif"/></a></span>
            <?
			}
			if (($vendor->vimeo != "") && ($vendor->showVimeo)){
			?><span><a href="<?=$vendor->vimeo?>" target="new"><img class="socialMediaIcon" src="images/socialMedia/vimeo.gif"/></a></span>
            <?
			}
			?>
			</p> <!--end socialMedia id -->
        <?
			}
			?>
        
    </section>

</section>

<footer>
	<?
	if ($vendor->ticketToSavings != ""){
	?>
		<p class="ticketToSavingsFootnote"><i><span class="footnote">&#1645;</span> Contact vendor for any restrictions.  Offer subject to modification or cancellation prior to a signed agreement being executed.</i></p>
	<?
    }
    ?>
</footer>

<script>
$(document).ready(function($){

	/* prepend menu icon */
	//$('#nav-wrap').prepend('<div id="menu-icon">Menu</div>');
	$('#nav-wrap').prepend('<div id="menu-icon"></div>');
	
	/* toggle nav */
	$("#menu-icon").on("click", function(){
		$("#nav").slideToggle();
		$(this).toggleClass("active");
	});
	
	<?
	if ($vendor->headshotImage != ""){
		$imgSizeArr = $vendor->getHeadshotImageSize();
		?>
			var headshotImageObj = {'x':<?=$imgSizeArr['x']?>,'y':<?=$imgSizeArr['y']?>,'ratio':<?=$imgSizeArr['ratio']?>};
			if (headshotImageObj.x > headshotImageObj.y){
				$("#headshotImage").css({maxWidth:'20%'});	
			}else{
				$("#headshotImage").css({maxHeight:'100px'});	
			}
		<?
	}
	?>

});

</script>

</body>
</html>