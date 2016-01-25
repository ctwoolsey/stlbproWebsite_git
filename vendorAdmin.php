<?
	$ssid=0;
	if (isset($_REQUEST['ssid'])){
		$ssid=$_REQUEST['ssid'];
		session_id($ssid);
		$ssidLink_continuing = "&ssid=".$ssid;
		$ssidLink_starting = "?ssid=".$ssid;
	}
	session_start();
	
	/*echo "Session info: <br>";
	print_r($_SESSION);
	echo "<br><br>";*/
	include_once('vendorsClass.php');
	$vpc = new vendorsClass('vendorList.xml');
	$username = "";
	$isAdmin = false;
	$vendor = NULL;
	$valid_login = false;
	
	if (isset($_SESSION['username'])){
		$valid_login=true;
		$username = $_SESSION['username'];
		$vendor = $vpc->findVendorWithEmail($username);
		if (!is_null($vendor))
			$isAdmin = $vendor->isAdmin();
	}
	//else echo "Warning Session Didn't Set<br>";
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,  user-scalable=no"/>
<title>Vendor Administration</title>
<script src="scripts/jquery/jquery-1.11.2.min.js"></script>
<link href="html5reset-1.6.1.css" rel="stylesheet" type="text/css">
<link href="vendorAdmin.css" rel="stylesheet" type="text/css">
<link href="privateVendorPages.css" rel="stylesheet" type="text/css">
</head>
<body>

	<header>
    	<h1>St. Louis Bridal Professionals</h1>
        <p id="homeLink"><a href="http://www.stlbridalpros.com">Home</a></p>
        <br>
        <p class="activeStatus">
            <?
			if ($valid_login){
                $postingStatusOnImg = "images/off_101x119.png";
                if ($vendor->canVendorBePosted()){
                    $postingStatusOnImg = "images/on_101x119.png";	
                }
            ?>
            Your Web Status is: <img src="<?=$postingStatusOnImg?>"/>
            <?
			}else{ //this case of not logging in is taken care of at the bottom of the page
			?>
            <!--Something didn't load right, try refresing or logging in again. If the problem persists contact the site administrator.-->
        	<?
			}
			?>
        </p>
    </header>
    <?
		if ($valid_login && $isAdmin){
	?>
    <section class="adminOnlyFunctions">
    	<div class="functionWrapper">
        <p class="sectionHeaderText">Administrative Operations</p>
    	<p class="functionHeader">Create New User</p>
    	<p class="functionBounds hidden">
        	<span>
            <label class="functionLabel" for="createNewVendor">Enter Business Email:</label>
            <input type="text" class="dataInput" id="createNewVendor" name="createNewVendor" value=""/>
            </span>
            <span>
            <label class="functionLabel spaceAbove" for="createNewVendorBizName">Enter Business Name:</label>
            <input type="text" class="dataInput" id="createNewVendorBizName" name="createNewVendorBizName" value=""/>
            </span>
            <span>
            <label class="functionLabel spaceAbove" for="vendorType">Vendor Type:</label>
            <?
            $vtClass = new VENDORTYPE();
			$vtSelect = $vtClass->getSelectCode("vendorType","vendorTypeClass",$usingVendor,false);
			echo $vtSelect;
			?>
            </span>
            <span>
            <label class="functionLabel spaceAbove" for="vendorType">Vendor Type:</label>
            <?
            $vtMembershipClass = new VENDORMEMBERSHIPTYPE();
			$vtMembershipSelect = $vtMembershipClass->getSelectCode("vendorMembershipType","vendorMembershipTypeClass",$usingVendor,false);
			echo $vtMembershipSelect;
			?>
            </span>
            <br>
            <input class="spaceAboveButton" type="button" id="createNewVendorButton" value="Create New Vendor"/>
        </p>
        <p class="functionHeader">Edit User</p>
        <p class="functionBounds hidden">
        	<label class="functionLabel" for="editVendorSelect">Select Vendor To Edit:</label>
        	<?=$vpc->getSelectCode('name','editVendorSelect','dataInput',true,"");?>
            <br>
            <input  class="spaceAboveButton" type="button" id="editVendorButton" value="Edit Vendor"/>
        </p>
        <p class="functionHeader">Remove User</p>
        <p class="functionBounds hidden">
        	<label class="functionLabel" for="editVendorSelect">Select Vendor To Remove:</label>
        	<?=$vpc->getSelectCode('name','removeVendorSelect','dataInput',true,"");?>
            <br>
            <input class="spaceAboveButton" type="button" id="removeVendorButton" value="Remove Vendor"/>
        </p> 
        <p class="functionHeader">Set User Access</p>
        <p class="functionBounds hidden">
        	<label class="functionLabel" for="setAdminStatusSelect">Select Vendor:</label>
        	<?=$vpc->getSelectCode('name','setAdminStatusSelect','dataInput',true,"");?>
            <br>
            <span id="adminChoiceBlock" class="spaceAbove hidden">
                <label class="functionLabel" for="adminChoice">Select Access Level:</label>
                <SELECT class="adminChoiceClass" name="adminChoice" id="adminChoice">
                    <option value="yes">admin</option>
                    <option value="no">user</option>
                </SELECT>
                <br>
            	<input id="updateAccessLevelBtn" class="spaceAboveButton" type="button" value="Update Access Level"/>
            </span>
        </p>
        <p class="functionHeader">Set User Type</p>
        <p class="functionBounds hidden">
        	<label class="functionLabel" for="setVendorTypeSelect">Select Vendor:</label>
        	<?=$vpc->getSelectCode('name','setVendorTypeSelect','dataInput',true,"");?>
            <br>
            <span class="changeVendorTypeBlock" class="spaceAbove hidden">
                <label class="functionLabel spaceAbove" for="vendorChangeTypeSelect">Type:</label>
				<?
                $vtChangeClass = new VENDORTYPE();
                $vtChangeSelect = $vtChangeClass->getSelectCode("vendorChangeTypeSelect","vendorChangeTypeClass",$usingVendor,true,"","");
                echo $vtChangeSelect;
                ?>
            </span>
            <br>
            <span class="changeVendorTypeBlock" class="spaceAbove hidden">
                <label class="functionLabel spaceAbove" for="vendorChangeMembershipTypeSelect">Membership Type:</label>
				<?
                $vtChangeMembershipClass = new VENDORMEMBERSHIPTYPE();
                $vtChangeMembershipSelect = $vtChangeMembershipClass->getSelectCode("vendorChangeMembershipTypeSelect","vendorChangeMembershipTypeClass",$usingVendor,true,"","");
                echo $vtChangeMembershipSelect;
                ?>
            </span>
            <br>
            <span class="changeVendorTypeBlock" class="spaceAbove hidden">
            	<input id="updateVendorTypeLevelBtn" class="spaceAboveButton" type="button" value="Update Vendor Type Fields"/>
            </span>
        </p>
        <p class="functionHeader">Set Users Web Display Order</p>
        <p class="functionBounds hidden"><a href="http://www.stlbridalpros.com/sortVendors.php<?=$ssidLink_starting?>">arrange order of vendors</a></p> 
        <p class="functionHeader">Modify Website 'About' Text</p>
        <p class="functionBounds hidden"><input id="editGroupAboutText" type="button" value="Edit Group's About Text" onClick="showEditText(this,'stlbridalprosAboutText.txt');"/><input class="hidden" id="saveGroupAboutText" type="button" value="Save About Text" onclick="saveEditText(this,'stlbridalprosAboutText.txt');"/><input class="cancelEditTextButton hidden" type="button" value="Cancel" onClick="cancelEditText(this);"/><textarea id="aboutTextArea" contentEditable="true" class="hidden textGoesHere"></textarea></p>
        <p class="functionHeader">Modify Website 'Ticket To Savings' Text</p>
        <p class="functionBounds hidden"><input id="editGroupTicketToSavingsText" type="button" value="Edit Group's Ticket To Savings Text" onClick="showEditText(this,'aboutTicketToSavingsText.txt');"/><input class="hidden" id="saveGroupTicketToSavingsText" type="button" value="Save Ticket To Savings Text" onclick="saveEditText(this,'aboutTicketToSavingsText.txt');"/><input class="cancelEditTextButton hidden" type="button" value="Cancel" onClick="cancelEditText(this);"/><textarea id="tkts2SavingsTextArea" contentEditable="true" class="hidden textGoesHere"></textarea></p>
     	<p class="functionHeader">Re-Send Welcome Email</p>
        <p class="functionBounds hidden">
        	<label class="functionLabel" for="sendWelcomeEmail">Select Vendor To Email:</label>
        	<?=$vpc->getSelectCode('name','welcomeEmailSelect','dataInput',true,"");?>
            <br>
            <input class="spaceAboveButton" type="button" value="Send Email" onClick="sendWelcomeEmail();"/>
        </p>
        </div>           
    
    </section>
    <?
		} //end if Admin
	?>
    
    <section class="userFunctions">
    <?
		if ($valid_login && (!is_null($vendor))){
	?>
    	<div class="functionWrapper">
            <p class="sectionHeaderText">User Operations</p>
            <p class="functionHeader">Edit User Profile</p>
                <p class="functionBounds hidden">
                    <input  class="" type="button" id="editUserButton" value="Click To Edit Profile"/>
                </p>
            <p class="functionHeader">Useful Downloads</p>
                <p class="functionBounds hidden">
                    <a href="http://www.stlbridalpros.com/downloadSTLGroupLogos.php<?=$ssidLink_starting?>">Download Arch Logo</a>
                    <br>
                    <a href="http://www.stlbridalpros.com/downloadSTLOtherItems.php<?=$ssidLink_starting?>">Download Other Items</a>
                    <br>
                    <a href="http://www.stlbridalpros.com/downloadVendorLogos.php<?=$ssidLink_starting?>">Download Eachother's Logos</a>
                </p>
            <p class="functionHeader">Change Password</p>
	            <p class="functionBounds hidden">
                	<span class="changePasswordWarningText warningColor hidden" id="oldPasswordIncorrect">The old password was not correct<br></span>
                    <span class="changePasswordWarningText warningColor hidden" id="passwordsDoNotMatch">The new passwords do not match<br></span>
                    <span class="changePasswordWarningText warningColor hidden" id="blankNewPassword">The new password cannot be blank<br></span>
                    <span class="changePasswordWarningText warningColor hidden" id="passwordLengthProblem">The new password must be between 4-20 letters or numbers<br></span>
                    
    	    		<span>
                        <label class="functionLabel spaceAbove" for="oldPassword">Old Password:</label>
                        <input type="password" class="dataInput" id="oldPassword" name="oldPassword" value=""/>
                    </span>
                    <span>
                        <label class="functionLabel spaceAbove" for="newPassword">New Password:</label>
                        <input type="password" class="dataInput" id="newPassword" name="newPassword" value=""/>
                    </span>
                    <span>
                        <label class="functionLabel spaceAbove" for="newPasswordVerify">Re-Type New Password:</label>
                        <input type="password" class="dataInput" id="newPasswordVerify" name="newPasswordVerify" value=""/>
                    </span>
                	<input  class="spaceAboveButton" type="button" id="changePasswordButton" value="Change Password"/>
        		</p>
            <?
				if (!$vendor->canVendorBePosted()){
			?>
                    <p class="functionHeader">Why Is My Web Status Off?</p>
                    <p class="functionBounds hidden">
                    	<input id="statusReasonsBtn" type="button" value="Show/Refresh Reasons"/>
                        <br>
                        <span id="statusReasons">
                        </span>
                    </p> 
   			<?
				}//end if vendor can't be posted
			?>
            <br><br><p><b>Eventually coming functions</b><p> 
            <p>download minutes</p>
            <p>team calendar</p>
            <p>preview landing page</p>
            <p>switch status on off</p>
        </div>
        <?
		}else{ //end check if Vendor was null
			echo '<span class="warningColor">Unable to login, try logging in again at: <a href="http://www.stlbridalpros.com/vendorLogin.php">www.stlbridalpros.com/vendorlogin.php</a></span>';
		}
	?>
    </section>
    
<script>
	
	function sendWelcomeEmail(id,email){
		var reloadNeeded = (id)?true:false;
		var vendorID = (id)?id:$("#welcomeEmailSelect").val();
		var mailSentTo = (email)?email:$("#welcomeEmailSelect option:selected" ).text();
		if ($(vendorID) != "-1"){
			$.ajax({url:'sendWelcomeEmail.php', 
				type:'POST',
				dataType:"json",
				data:{vendorID:vendorID,ssid:'<?=$ssid?>'}	
			})
			.done(function(success){
				if (success == true){
					$("#welcomeEmailSelect").val("-1");
					alert("Welcome email sent to: "+mailSentTo);
					if (reloadNeeded){
						location.reload(true);
					}
				}else{
					alert("Error Sending Welcome Email.\nMessage: "+success.message);	
				}
			});
		}
	}
	
	function cancelEditText(buttonClicked){
		var textArea = $(buttonClicked).parent().find("textarea");
		$(buttonClicked).parent().find("input").toggleClass("hidden");
		$(textArea).addClass("hidden");
	}
	
	function showEditText(buttonClicked,fileToRead){
		$(buttonClicked).parent().find(".hidden").removeClass("hidden");
		$(buttonClicked).addClass("hidden");
		$.ajax({url:'getFileContents.php', 
				type:'POST',
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				dataType:"html",
				data:{fileToGet:fileToRead,ssid:'<?=$ssid?>'}
			})
			.done(function(contents){
				//var contents = decodeURI(contents);
				$(buttonClicked).parent().find(".textGoesHere").val(contents);
			});
	};
	
	function saveEditText(buttonClicked,fileToSave){
		var textArea = $(buttonClicked).parent().find("textarea");
		var textToSave = $(textArea).val();
		$(buttonClicked).parent().find(".hidden").removeClass("hidden");
		$(textArea).addClass("hidden");
		$(buttonClicked).addClass("hidden");
		$.ajax({url:'saveFileWithContents.php', 
				type:'POST',
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				dataType:"json",
				data:{fileToWrite:fileToSave,contents:textToSave,ssid:'<?=$ssid?>'}
			})
			.done(function(success){
					if (success){
						alert("Saved");
					}else{
						alert("Problems Saving");
					}
				});
	};
	
	function initializeEvents(){
		$(".functionHeader").on("click",function(){
			var clickedOnFH = $(this);
			$(".functionHeader").each(function(){
				if ($(this).get(0) == $(clickedOnFH).get(0)){
					$(this).next().toggleClass("hidden");	
				}else{
					$(this).next().addClass("hidden");	
				}
			});	
		});
		
		$("#statusReasonsBtn").on("click",function(){
			$.ajax({url:'getNoPostingReasons.php', 
				type:'POST',
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				dataType:'json',
				data:{vendorID:'<?=$vendo->id?>',
					  ssid:'<?=$ssid?>'}
			})
			.done(function(completed){
				if (completed.success == true){
					$htmlString = '<ul>';
					for(var reasonCount in completed.reasons){
						$htmlString += '<li>'+completed.reasons[reasonCount]+'</li>';
					}
					$htmlString += '</ul>';
					
					$("#statusReasons").html($htmlString);
				}else{
					$("#statusReasons").html("unable to retrieve reasons");
				}
			});
		
		});
		
		$("#removeVendorButton").on("click",function(){
			$.ajax({url:'removeVendor.php', 
				type:'POST',
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				dataType:'json',
				data:{vendorID:$("#removeVendorSelect").val(),
					  ssid:'<?=$ssid?>'}
			})
			.done(function(removed){
				$("#removeVendorSelect").val("-1");
				if (removed == true){
					alert("Vendor Was Removed.");
				}else{
					alert("Vendor Was Not Removed.");
				}
				location.reload(true);
			});
		});
			
		$("#createNewVendorButton").on("click",function(){
			$.ajax({url:'createNewVendor.php', 
				type:'POST',
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				dataType:'json',
				data:{email:$("#createNewVendor").val(),bizName:$("#createNewVendorBizName").val(),
					  vendorType:$("#vendorType").val(),vendorMembershipType:$("#vendorMembershipType").val(),
					  ssid:'<?=$ssid?>'}
			})
			.done(function(created){
				if (created == false){
					alert("New Vendor Not Created");
				}else{
					$("#createNewVendor").val("");
					$("#createNewVendorBizName").val("");
					$("#vendorType").val('<?=VENDORTYPE::NONVENUE?>');
					$("#vendorMembershipType").val('<?=VENDORMEMBERSHIPTYPE::FULLMEMBER?>');
					//do code to send an email
					sendWelcomeEmail(created.id,created.email);
				}
				
			});	
			
		});
		$("#setVendorTypeSelect").on("change",function(){
			//var setVendorTypeSelect = $("#setVendorTypeSelect");
			var changeVendorTypeSelect = $("#vendorChangeTypeSelect");
			var changeVendorMembershipTypeSelect = $("#changeVendorMembershipTypeSelect");
			var changeVendorTypeBlock = $(".changeVendorTypeBlock");
			
			var vendorID = $(this).val();
			if (vendorID != "-1"){
				$.ajax({url:'getVendorTypes.php', 
					type:'POST',
					contentType:'application/x-www-form-urlencoded; charset=UTF-8',
					dataType:'json',
					data:{id:vendorID}
				})
				.done(function(vendorTypes){
					$(changeVendorTypeSelect).val(vendorTypes.vendorType);
					$(changeVendorMembershipTypeSelect).val(vendorTypes.vendorMembershipType);
					
					$(changeVendorTypeBlock).removeClass("hidden");
				});		
			}else//vendor is blank
				$(changeVendorTypeBlock).addClass("hidden");
			
		});
		
		$("#updateVendorTypeLevelBtn").on("click",function(){
			var vendorID = $("#setVendorTypeSelect").val();
			var vendorType = $("#vendorChangeTypeSelect").val();
			var vendorMembershipType = $("#vendorChangeMembershipTypeSelect").val();
			
			$.ajax({url:'updateVendorTypes.php', 
					type:'POST',
					contentType:'application/x-www-form-urlencoded; charset=UTF-8',
					dataType:'json',
					data:{vendorID:vendorID,vendorType:vendorType,vendorMembershipType:vendorMembershipType,ssid:'<?=$ssid?>'}
				})
				.done(function(completed){
					$("#setVendorTypeSelect").val("-1");
					$(".changeVendorTypeBlock").addClass("hidden");
					if (completed){
						alert("Vendor Types updated.");
					}else{
						alert("Unable to update vendor types.");
					}
				});		
			
		});
		
		$("#setAdminStatusSelect").on("change",function(){
			var adminSelect = $("#adminChoice");
			var adminChoiceBlock = $("#adminChoiceBlock");
			
			var vendorID = $(this).val();
			if (vendorID != "-1"){
				$.ajax({url:'isVendorAnAdmin.php', 
					type:'POST',
					contentType:'application/x-www-form-urlencoded; charset=UTF-8',
					dataType:'json',
					data:{id:vendorID}
				})
				.done(function(isAdmin){
					if (isAdmin == true)
						$(adminSelect).val("yes");
					else
						$(adminSelect).val("no");
					
					$(adminChoiceBlock).removeClass("hidden");
				});		
			}else//vendor is blank
				$(adminChoiceBlock).addClass("hidden");
			
		});
		
		$("#updateAccessLevelBtn").on("click",function(){
			var vendorID = $("#setAdminStatusSelect").val();
			var adminStatus = $("#adminChoice").val();
			
			$.ajax({url:'updateAdminStatus.php', 
					type:'POST',
					contentType:'application/x-www-form-urlencoded; charset=UTF-8',
					dataType:'json',
					data:{id:vendorID,adminStatus:adminStatus,ssid:'<?=$ssid?>'}
				})
				.done(function(completed){
					$("#setAdminStatusSelect").val("-1");
					$("#adminChoiceBlock").addClass("hidden");
					if (completed){
						alert("Admin status updated.");
					}else{
						alert("Unable to update admin status.");
					}
				});		
			
		});
		$("#changePasswordButton").on("click",function(){
			var oldPassword = $("#oldPassword").val();
			var newPassword = $("#newPassword").val();
			var newPasswordVerify = $("#newPasswordVerify").val();
			var changePasswordValid = true;
			
			var savedPassword = '';

			$.ajax({url:'getPassword.php', 
				type:'POST',
				async:false,	
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				dataType:"html",
				data:{ssid:'<?=$ssid?>'}
			})
			.done(function(contents){
				//var contents = decodeURI(contents);
				savedPassword = contents;
			});
	
			if (oldPassword == savedPassword){
				if (newPassword == newPasswordVerify){
					if (newPassword == ""){
						showChangePasswordError("blankNewPassword");
						changePasswordValid = false;
					}
					var passwordPattern = /[a-zA-Z0-9]{4,20}/;
					if (!passwordPattern.test(newPassword)){
						showChangePasswordError("passwordLengthProblem");
						changePasswordValid = false;
					}
				}else{
					showChangePasswordError("passwordsDoNotMatch");
					changePasswordValid = false;
				}
			}else{
				showChangePasswordError("oldPasswordIncorrect");	
				changePasswordValid = false;
			}
			
			if (changePasswordValid){
				$.ajax({url:'updatePassword.php', 
					type:'POST',
					contentType:'application/x-www-form-urlencoded; charset=UTF-8',
					dataType:'json',
					data:{password:newPassword,ssid:'<?=$ssid?>'}
				})
				.done(function(completed){
					showChangePasswordError();
					$("#oldPassword").val('');
					$("#newPassword").val('');
					$("#newPasswordVerify").val('');
					if (completed){
						alert("Password updated.");
					}else{
						alert("Unable to update password.");
					}
				});		
			}
		});
		
		$("#editVendorButton").on("click",function(){
			window.location.href = "http://www.stlbridalpros.com/editVendor.php?user_id="+$("#editVendorSelect").val()+"<?=$ssidLink_continuing?>";
		});
		$("#editUserButton").on("click",function(){
			window.location.href = "http://www.stlbridalpros.com/editVendor.php?user_id="+<?=$vendor->id?>+"<?=$ssidLink_continuing?>";
		});
	}
	
	function showChangePasswordError(errorToShow){
		clearAllPasswordErrors();
		if ((errorToShow != "") && ($("#"+errorToShow).length > 0))
			$("#"+errorToShow).removeClass("hidden");
	}
	
	function clearAllPasswordErrors(){
		$("#oldPasswordIncorrect").addClass("hidden");
		$("#passwordsDoNotMatch").addClass("hidden");
		$("#blankNewPassword").addClass("hidden");	
		$("#passwordLengthProblem").addClass("hidden");
	}
	
	$( document ).ready(function() {
		initializeEvents();
		<?
		if ($isAdmin){
		?>
			$("#vendorType").val('<?=VENDORTYPE::NONVENUE?>');
			$("#vendorMembershipType").val('<?=VENDORMEMBERSHIPTYPE::FULLMEMBER?>');
		<?
		}
		?>
		
	});
</script>
</body>
</html>