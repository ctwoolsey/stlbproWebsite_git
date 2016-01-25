<?
	$secondCheck = isset($_REQUEST['sessionFailed']);
	if (!$secondCheck){
		session_start();
		if (isset($_SESSION['cookieTest']) && ($_SESSION['cookieTest'] == "yummy")){
			echo json_encode(true);
		}
		else{
			echo json_encode(false);
		}
	}else{ //this is the second test to test if can start a session without a cookie
		$ssid=0;
		if (isset($_REQUEST['ssid'])){
			$ssid=$_REQUEST['ssid'];
			session_id($ssid);
			session_start();
		}
		session_start();
		//echo json_encode ("newtry");
		if (isset($_SESSION['cookieTest']) && ($_SESSION['cookieTest'] == "yummy")){
			echo json_encode(true);
		}
		else{
			echo json_encode(false);
		}
	}
	
?>