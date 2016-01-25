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
<title>Download Other Items</title>
<link href="html5reset-1.6.1.css" rel="stylesheet" type="text/css">
<link href="all.css" rel="stylesheet" type="text/css">
<link href="downloadLinks.css" rel="stylesheet" type="text/css">
</head>

<body>
<header>
<p>Download Other Items</p>
<span id="backSpan"><a href="http://www.stlbridalpros.com/vendorAdmin.php<?=$ssidLink_starting?>">Back</a></span>
</header>
<section id="downloadDivSection">
<?
	$ticketDir = 'images/stlbridalproslogos/Tickets/';
	$files = array_diff(scandir($ticketDir), array('..', '.'));
	
	$fileCount = 0;
	foreach($files as $fileName){
		$firstDiv="";
		if ($fileCount++ == 0)
			$firstDiv = " downloadItemDiv-first";
		?>
        <div class="downloadItemDiv<?=$firstDiv?>">
        	<img class="downloadItem" src="<?=$ticketDir.$fileName?>"/>
            <p><a href="downloadGenericFile.php?filename=<?=urlencode($ticketDir.$fileName)?>&shortFilename=<?=$fileName?>"><img class="downloadArrow" src="images/downArrow_50x49.png"/><?=$fileName?></a></p>
        </div>
        <?
	}
?>
</section>
</body>
</html>