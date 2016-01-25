<?
	$ssid = 0;
	if (isset($_REQUEST['ssid'])){
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
	
	if (!$isAdmin){
		header('Location: http://www.stlbridalpros.com/vendorAdmin.php'.$ssidLink_starting);
	}
	
	
	
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,  user-scalable=no"/>
<title>Sort Vendors- St. Louis Bridal Professionals</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="scripts/jquery/jquery-1.11.2.min.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<script src="scripts/serialize.js"></script>
<link href="html5reset-1.6.1.css" rel="stylesheet" type="text/css">
<link href="sortVendor.css" rel="stylesheet" type="text/css">
<link href="privateVendorPages.css" rel="stylesheet" type="text/css">


</head>
<body>
  	<header>
    	<h1>St. Louis Bridal Professionals</h1>
    	<h2>Sort Vendors</h2>
  	</header>
  	<section id="sortSection">
    	<p>Arrange vendors in order of website appearance</p>
		<div id="sortable">
        <?
			foreach($vpc->vendors as $vendor){
				?>
                <div id="<?=$vendor->id?>" class="vendorDiv ui-state-default">
                	<?=$vendor->name?>
                    <div class="sortableArrowDiv">
                    	<img class="upArrow" src="images/upArrow_50x49.png" onclick="moveUp(this)";/>
                        <img class="downArrow" src="images/downArrow_50x49.png" onclick="moveDown(this)";/>
                    </div>
                </div>
                <?	
			}
		?>
        </div>
        <div>
        	<input type="button" id="doneSorting" value="Finished" onClick="finishedSorting();"/>
        </div>
	</section>
    
<script>
	$(document).ready(function(e) {
        $( "#sortable" ).sortable();
		$( "#sortable" ).disableSelection();
    });
	
	function moveUp(clickedArrow){
		var clickedVendorDiv = getArrowsVendorDiv(clickedArrow);
		var previousDiv = $(clickedVendorDiv).prev();
		if ($(previousDiv).length != 0){
			$(clickedVendorDiv).insertBefore($(previousDiv));	
		}
	}
	
	function moveDown(clickedArrow){
		var clickedVendorDiv = getArrowsVendorDiv(clickedArrow);
		var nextDiv = $(clickedVendorDiv).next();
		if ($(nextDiv).length != 0){
			$(clickedVendorDiv).insertAfter($(nextDiv));	
		}
	}
	
	function getArrowsVendorDiv(clickedArrow){
		return ($(clickedArrow).parentsUntil(".vendorDiv").parent());
	}
	
	function finishedSorting(){
			var orderedArray = new Array();
			$(".vendorDiv").each(function(){
				orderedArray.push($(this).prop("id"));
			});
			
			$.ajax({url:'reorderVendors.php', 
				type:'POST',
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				dataType:"json",
				data:{newOrder:serialize(orderedArray),ssid:'<?=$ssid?>'}
			})
			.done(function(success){
					if (success == false)
						alert("Vendors were unable to be reordered.");
					window.location.href="vendorAdmin.php"+"<?=$ssidLink_starting?>";
				});
	
	}
</script>
</body>
</html>