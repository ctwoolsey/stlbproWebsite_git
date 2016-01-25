<?
	session_start();
	session_unset();
	session_destroy();
	session_start();
	$_SESSION['cookieTest'] = "yummy";
	$valid_login = false;
	$attempted_login = false;
	$userNameToFillIn = "";
	$forgotToIncludeEmail = false;
	$forgot = false;
	$emailNotFound = false;
	$loginDetailsSent = false;
	$errorSendingEmail = false;
	$rememberMeCookieWasSet = false;
	
	if (isset($_REQUEST['forgot'])){
		$forgot = true;
		if (!isset($_REQUEST['users_email'])){
			$forgotToIncludeEmail = true;
		}else{
			include_once('vendorsClass.php');
			$vpc = new vendorsClass('vendorList.xml');
			//echo "Sending: ".$_REQUEST['users_email']."<br>";
			$vendor = $vpc->findVendorWithEmail($_REQUEST['users_email']);
			if (!is_null($vendor)){
				//print_r($vendor);
				$message = "Your password is: ".$vendor->password."\n";
				//echo "email body: ".$emailBody."<br>";
				
				$to_add = $_REQUEST['users_email'];
				$subject = "stlbridalpros forgotten password";
				$fromName = "St. Louis Bridal Professionals";
				$from_add = "noreply@stlbridalpros.com"; //pw:no8rplI$
				
			
				$subject = "St. Louis Bridal Pros Password";
				//$message = "Test Message";
				
				$headers = "From: $from_add \r\n";
				$headers .= "Reply-To: $from_add \r\n";
				$headers .= "Return-Path: $from_add\r\n";
				$headers .= "X-Mailer: PHP \r\n";
				
				
				if(mail($to_add,$subject,$message,$headers)) 		
					$loginDetailsSent = true;
				else
					$errorSendingEmail = true;
					
			}else{
				$emailNotFound = true;
			}
		}
	}
		
	if (isset($_POST['users_email']) || isset($_POST['users_pass'])){
		$attempted_login = true;
	}
	
	if (isset($_REQUEST['users_email'])){
		$userNameToFillIn = $_REQUEST['users_email'];
	}else{
		 $userNameToFillIn = $_COOKIE['remember_me'];
	}
		
	if (isset($_POST['users_email']) && isset($_POST['users_pass'])){
		include_once('vendorsClass.php');
		$vpc = new vendorsClass('vendorList.xml');
		$vendor = $vpc->findVendorWithEmail($_POST['users_email']);
		if (!is_null($vendor)){
			if ($vendor->password == $_POST['users_pass']){
				$valid_login = true;	
			}
		}
		
		if ($valid_login){
			$year = time() + 31536000;
			if($_POST['rememberMe']) {
				setcookie('remember_me', $_POST['users_email'], $year);
			}
			elseif(!$_POST['rememberMe']) {
				if(isset($_COOKIE['remember_me'])) {
					$past = time() - 100;
					setcookie('remember_me', gone, $past);
				}
			}
			//session_start();
			session_unset();
			session_destroy();
			session_start();
			session_regenerate_id();
			$urlSSID = "";
			//if (isset($_POST['ssid'])){
				$new_sessionid = session_id();
				$urlSSID = "?ssid=".$new_sessionid;
			//}
			$_SESSION['username'] = $_POST['users_email'];
			//echo "session name = ".session_name();
			//echo "printing Session<br>";
			//print_r($_SESSION);
			header("Location: http://www.stlbridalpros.com/vendorAdmin.php".$urlSSID);
			exit;
		}
	}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,  user-scalable=no"/>
<title>Vendor Login Form</title>
<script src="scripts/jquery/jquery-1.11.2.min.js"></script>
<link href="html5reset-1.6.1.css" rel="stylesheet" type="text/css">
<link href="vendorLoginForm.css" rel="stylesheet" type="text/css">
<link href="privateVendorPages.css" rel="stylesheet" type="text/css">
</head>
<body>
	<header>
    	<h1>St. Louis Bridal Professionals</h1>
    	<h2>Vendor Login</h2>
    </header>
    <section>
    <?
		$invalidLoginWarning = " hidden";
    	if (($attempted_login) && ($valid_login == false))
			$invalidLoginWarning = "";
		$forgotPW_forgotEmailWarning = " hidden";
		if ($forgotToIncludeEmail)
			$forgotPW_forgotEmailWarning = "";
		$forgotPW_emailNotFoundWarning = " hidden";
		if ($emailNotFound){
			$forgotPW_emailNotFoundWarning = "";
		}
		$forgotPW_emailSent = " hidden";
		if ($loginDetailsSent){
			$forgotPW_emailSent = "";
		}
		$forgotPW_emailError = " hidden";
		if ($errorSendingEmail){
			$forgotPW_emailError = "";
		}
		
	?>
    	<p class="invalidLogin <?=$invalidLoginWarning?>">The username and/or password were incorrect.</p>
        <p class="invalidLogin <?=$forgotPW_forgotEmailWarning?>">Enter your email and then click 'forgot password'.</p>
        <p class="invalidLogin <?=$forgotPW_emailNotFoundWarning?>">The email entered was not found.</p>
        <p class="invalidLogin <?=$forgotPW_emailError?>">There was an error sending "<?=$_REQUEST['users_email']?>" the password.</p>
        <p class="passwordSent <?=$forgotPW_emailSent?>">An email containing the forgotten password has been sent to: "<?=$_REQUEST['users_email']?>"</p>
        <form method="post" action="<?=$_SERVER['PHP_SELF']?>"  >
        	<div id="formSeparator">
               <p>
                    <label for="users_email">Email</label>
                    <input type="email" name="users_email" id="users_email"  value="<?=$userNameToFillIn?>">
               </p>
               <p> 
                    <label for="users_pass">Password</label>
                    <input name="users_pass" type="password" id="users_pass"></input>
               </p>
               <p>
               		<input type="checkbox" name="rememberMe" id="rememberMe" value="1" <?=isset($_COOKIE['remember_me'])?" checked ":"";?>/>
                    <label id="rememberMeLabel" for="rememberMe">Remember Me?</label>
               </p>
               <!--<p><a class="forgotPassword" href="vendorForgotPassword.php">Forgot Password?</a></p>-->
               <p><a class="forgotPassword" href="javascript:forgotPassword('Abc');">Forgot Password?</a></p>
           </div>
               <input type="submit" value="Submit"/>
               <input type="reset" value="Reset"/>
        </form>
    </section>
<script>
function forgotPassword(){
	var users_email = document.getElementById("users_email").value;
	if (users_email != "")
		users_email = "&users_email="+users_email;

	window.location.href = ("http://www.stlbridalpros.com/vendorLogin.php?forgot=1"+users_email);	
}

$(document).ready(function(){
		if (!navigator.cookieEnabled){
			var SSID='<input type="hidden" name="ssid" value="1"/>';
			$("form").append($(SSID));
			
			$("#rememberMe").on("click",function(){
					if ($(this).prop("checked") == true){
						alert('To Use the "RememberMe" feature you need to enable cookies.');
						$(this).prop("checked",false);
					}
				
				});
		}
		
		<?
			if (($valid_login == false) && ($attempted_login == false)){ //the initial screen
				?>
				$.ajax({url:'checkSessionCookie.php', 
					type:'POST',
					contentType:'application/x-www-form-urlencoded; charset=UTF-8',
					dataType:'json',
					data:{}
				})
				.done(function(cookieWorked){
					if (cookieWorked == false){	
						$.ajax({url:'checkSessionCookie.php', 
							type:'POST',
							contentType:'application/x-www-form-urlencoded; charset=UTF-8',
							dataType:'json',
							data:{ssid:'<?=session_id();?>',sessionFailed:true}
						})
						.done(function(cookieWorkedSecond){
							if (cookieWorkedSecond == true){
								var SSID='<input type="hidden" name="ssid" value="1"/>';
								$("form").append($(SSID));
							}else if (cookieWorkedSecond == false){
								alert("Problems setting session cookies.  See Admin.");
							}
							
						}); //end second session cookie test
					}
				});		//end session cookie test
				<?
			}
		?>
	});
	
</script>
</body>
</html>