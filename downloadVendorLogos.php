<?
	$ssid=0;
	if (isset($_REQUEST['ssid'])){
		$ssid=$_REQUEST['ssid'];
		session_id($ssid);
		$ssidLink_continuing = "&ssid=".$ssid;
		$ssidLink_starting = "?ssid=".$ssid;
	}
	session_start();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Download St. Louis Bridal Professionals Logo(s)</title>
<link href="html5reset-1.6.1.css" rel="stylesheet" type="text/css">
<link href="all.css" rel="stylesheet" type="text/css">
<link href="downloadLinks.css" rel="stylesheet" type="text/css">
</head>

<body>
<header>
<p>Download Vendor Logos</p>
<span id="backSpan"><a href="http://www.stlbridalpros.com/vendorAdmin.php<?=$ssidLink_starting?>">Back</a></span>
</header>
<section id="downloadDivSection">
<?
	include_once('vendorsClass.php');
	$vpc = new vendorsClass('vendorList.xml');
	
	$fileCount = 0;
	foreach($vpc->vendors as $vendor){
		$firstDiv="";
		if ($fileCount == 0)
			$firstDiv = " downloadItemDiv-first";
		
		if (($vendor->logo != "") && (file_exists($vendor->logo))){
			$path_parts = pathinfo($vendor->logo);
			$srcFile = $path_parts['dirname']."/src/".$path_parts['basename'];
			$extension = $path_parts['extension'];
		?>
        
        <div class="downloadItemDiv<?=$firstDiv?>">
        	<img class="downloadItem" src="<?=$srcFile?>"/>
            <!--<p>$vendor->name</p>-->
            <p><a href="downloadGenericFile.php?filename=<?=urlencode($srcFile)?>&shortFilename=logo.<?=$extension?>"><img class="downloadArrow" src="images/downArrow_50x49.png"/><?=$vendor->name?></a></p>
        </div>
        <?
		} //end if vendor logo is valid
	}
?>
</section>
</body>
</html>