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
<p>Download The Group Logo</p>
<span id="backSpan"><a href="http://www.stlbridalpros.com/vendorAdmin.php<?=$ssidLink_starting?>">Back</a></span>
</header>
<section id="downloadDivSection">
<?
	$blackDir = 'images/stlbridalproslogos/archLogo/Black Lines/';
	$whiteDir = 'images/stlbridalproslogos/archLogo/White Lines/';
	$files = array_diff(scandir($blackDir), array('..', '.'));
	
	$fileCount = 0;
	foreach($files as $fileName){
		$whiteFileName = str_replace("black","white",$fileName);
		$firstDiv="";
		if ($fileCount++ == 0)
			$firstDiv = " downloadItemDiv-first";
		?>
        <div class="downloadItemDiv<?=$firstDiv?>">
        	<img class="downloadItem" src="<?=$blackDir.$fileName?>"/>
            <p><a href="downloadGenericFile.php?filename=<?=urlencode($blackDir.$fileName)?>&shortFilename=<?=$fileName?>"><img class="downloadArrow" src="images/downArrow_50x49.png"/>Black</a></p>
            <p><a href="downloadGenericFile.php?filename=<?=urlencode($whiteDir.$whiteFileName)?>&shortFilename=<?=$whiteFileName?>"><img class="downloadArrow" src="images/downArrow_50x49.png"/>White</a></p>
        </div>
        <?
	}
?>
</section>
</body>
</html>