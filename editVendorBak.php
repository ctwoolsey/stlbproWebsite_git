<?
	session_start();
	include_once('vendorsClass.php');
	include_once('scripts/Mobile-Detect-2.8.11/Mobile_Detect.php');
	$mobileDetect = new Mobile_Detect();
	$vpc = new vendorsClass('vendorList.xml');
	$username = "";
	$isAdmin = false;
	$vendor = NULL;
	$currentUser_id = "";
	$vendor_id = $_REQUEST['user_id'];
	$validToEdit = false;
	$mailToLink = "";
	$mailToLinkSubject = "";
	$mailToLinkBody = "Instructions:%0AAttach the image file and send the email.%0ADO NOT modify any part of this email or your image will not upload.";
	$maxUploadSize = 500;
	$maxSizeString = "maxSize:".$maxUploadSize;
	
	if (isset($_SESSION['username'])){
		$username = $_SESSION['username'];
		$vendor = $vpc->findVendorWithEmail($username);
		if (!is_null($vendor)){
			$isAdmin = $vendor->isAdmin();
			$currentUser_id = $vendor->id;
		}
	}
	
	if ($vendor_id == $currentUser_id){
		$validToEdit = true;
	}else{
		if ($isAdmin) $validToEdit = true;
	}
	
	
	if (!((is_numeric($vendor_id)) && ($vendor_id != "")))
		$validToEdit = false;
	
	$usingVendor = NULL;
	if ($validToEdit){
		$usingVendor = $vpc->getVendorByID($vendor_id);	
		$mailToLink = 'mailto:vendorUpload@stlbridalpros.com?';
		$mailToLinkSubject = 'subject=uid:'.$usingVendor->id. ' dir:';
		
	}
	
	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,  user-scalable=no"/>
<title>Edit Vendor- St. Louis Bridal Professionals</title>
<script src="scripts/jquery/jquery-1.11.2.min.js"></script>
<script src="scripts/serialize.js"></script>
<script src="scripts/plupload-2.1.2/js/plupload.full.min.js"></script>
<link href="html5reset-1.6.1.css" rel="stylesheet" type="text/css">
<link href="editVendor.css" rel="stylesheet" type="text/css">
<link href="privateVendorPages.css" rel="stylesheet" type="text/css">
</head>
<body>

<header>
<h1>St. Louis Bridal Professionals</h1>
<h2>Edit Vendor</h2>
</header>

<section>
<form id="vendorInfoForm" name="vendorInfoForm" method="post" action="#">
	<input type="hidden" name="id" id="id" value="<?=$usingVendor->id?>"/>
    <p>
    	<label for="name">Business Name:</label>
    	<input type="text" name="name" id="name" value="<?=$usingVendor->name?>">
  	</p>
    <div class="grouping" id="categories">
    	<p class="groupingDescription">Briefly Describe the service you are providing.(1-2 words)</p>
        <p class="groupingDescription">If you have more than one service click "Add Service".</p>
        <?
			//make sure that if a new vendor is passed there is one empty phone in phoneArray
			$categoryCount = 0;
			foreach($usingVendor->categoryArray as $category){
				
		?>	
        <p class="categoryBlock">
            <label for="category_<?=$categoryCount?>">Service Description:</label>
            <input type="text" name="category" id="category_<?=$categoryCount?>" title="service description" value="<?=$category?>">
            <span class="serviceDeleteSpan" title="delete service" onClick="deleteCategory(this);"><img class="serviceDeleteBtnImg" src="images/redX_20x18.gif"/></span>
        </p>
        <?
			$categoryCount++;
			}
		?>
        <div id="newServiceBtnDiv">
            	<input type="button" id="addCategory" onClick="addNewCategory();" value="Add Service"/>
        </div>
    </div>
   
    <div class="grouping" id="phones">
    	<?
			//make sure that if a new vendor is passed there is one empty phone in phoneArray
			$phoneCount = 0;
			foreach($usingVendor->phoneArray as $phone){				
				$checkTextingClass = "";
				$cellChecked = "checked";
				$landChecked = "";
				if ($phone->type != PHONETYPE::CELL){
					$checkTextingClass = " hidden";
					$cellChecked = "";
					$landChecked = "checked";
				}
					
		
				?>
                <div class="phoneBlock">
        			<span class="phoneDeleteSpan" title="delete phone" onClick="deletePhone(this)">
            			<img class="phoneDeleteBtnImg" src="images/redX_20x18.gif" />
            		</span>
            		<p>
            			Phone Type: 
            	<input type="radio" name="phoneType_<?=$phoneCount?>" <?=$cellChecked?> value="<?=PHONETYPE::CELL?>"/>cell
                <input type="radio" name="phoneType_<?=$phoneCount?>" <?=$landChecked?> value="<?=PHONETYPE::LAND?>" />land
            		</p>
                
    	
            <p>
                <label for="phone_<?=$phoneCount?>">Phone Number:</label>
                <input type="tel" class="phoneNumber" name="phone" title="phone number" id="phone_<?=$phoneCount?>" value="<?=$phone->number?>">
            </p>
            
            <p class="checkTexting<?=$checkTextingClass?>">
            	<? 
					$checked = "";
					$carrierSelectShowClass = " hidden";
					if ($phone->allowAnyTexting()){
						$checked = "checked";
						$carrierSelectShowClass = "";
					}
				?>
                <input type="checkbox" name="textingClients" id="textingClients_<?=$phoneCount?>" <?=$checked?> onClick="showHideCarrierSelect(this);">
                <label for="texting_<?=$phoneCount?>">Allow Clients To Text You?</label>
                <input type="checkbox" name="textingTeam" id="textingTeam_<?=$phoneCount?>" <?=$checked?> onClick="showHideCarrierSelect(this);">
                <label for="texting_<?=$phoneCount?>">Receive Text Alerts From Web-Site?</label>
                <span class="textingCarrierClass<?=$carrierSelectShowClass?>">
                	<label for="textingCarrier_<?=$phoneCount?>">Email address for texting</label>
                    <?
					$aCC = new allCarriersClass();
					echo $aCC->getSelectCode("textingCarrier_".$phoneCount,"",$phone);
					?>
                </span>
            </p>
        </div> <!--end DIV phoneBlock -->
        <?
				$phoneCount++;
			}
		?>
        <p id="newPhoneBtnDiv" onClick="addNewPhone();">
            	<input type="button" id="addPhone" value="Add Phone"/>
        </p>
    </div> <!--end DIV phones -->
    <p>
    	<label for="email">email:</label>
    	<input type="email" name="email" id="email" Title="email" value="<?=$usingVendor->email?>">
  	</p>
    <p>
    	<label for="website">Website:</label>
    	<input type="text" name="website" id="website" title="Website" value="<?=$usingVendor->website?>">
  	</p>
    <p>
    	<label for="address">Address(optional):</label>
    	<input type="text" name="address" id="address" title="Address(optional)" value="<?=$usingVendor->address?>">
  	</p>
    <p>
    	<span class="showSocialMedia">
        	<? 
				$checked = "";
				if ($vendor->showFacebook){
					$checked = "checked";
				}
			?>
            <label for="showFacebook">Show?</label>
            <input type="checkbox" name="showFacebook" id="showFacebook" <?=$checked?>/> 
    	</span>
        <label for="facebook">Facebook(optional):</label>
    	<input class="hasShowOption" type="text" name="facebook" id="facebook" title="Facebook(optional)" value="<?=$usingVendor->facebook?>">
  	</p>
    <p>
    	<span class="showSocialMedia">
        <? 
				$checked = "";
				if ($vendor->showInstagram){
					$checked = "checked";
				}
			?>
            <label for="showInstagram">Show?</label>
            <input type="checkbox" name="showInstagram" id="showInstagram"  <?=$checked?>/>
    	</span>
    	<label for="instagram">Instagram(optional):</label>
    	<input class="hasShowOption" type="text" name="instagram" id="instagram" title="Instagram(optional)" value="<?=$usingVendor->instagram?>">
  	</p>
    <p>
    	<span class="showSocialMedia">
            <? 
				$checked = "";
				if ($vendor->showTwitter){
					$checked = "checked";
				}
			?>
            <label for="showTwitter">Show?</label>
            <input type="checkbox" name="showTwitter" id="showTwitter"  <?=$checked?>/>
    	</span>
    	<label for="twitter">Twitter(optional):</label>
    	<input class="hasShowOption" type="text" name="twitter" id="twitter" title="Twitter(optional)" value="<?=$usingVendor->twitter?>">
  	</p>
    <p>
    	<span class="showSocialMedia">
            <? 
				$checked = "";
				if ($vendor->showPintrist){
					$checked = "checked";
				}
			?>
            <label for="showPintrist">Show?</label>
            <input type="checkbox" name="showPintrist" id="showPintrist" <?=$checked?>/>
    	</span>
    	<label for="pintrist">Pintrist(optional):</label>
    	<input class="hasShowOption" type="text" name="pintrist" id="pintrist" title="Pintrist(optional)" value="<?=$usingVendor->pintrist?>">
  	</p>
    <p>
    	<span class="showSocialMedia">
            <? 
				$checked = "";
				if ($vendor->showLinkedin){
					$checked = "checked";
				}
			?>
            <label for="showLinkedin">Show?</label>
            <input type="checkbox" name="showLinkedin" id="showLinkedin"  <?=$checked?>/>
    	</span>
    	<label for="linkedin">Linked-In(optional):</label>
    	<input class="hasShowOption" type="text" name="linkedin" id="linkedin" title="Linked-In(optional)" value="<?=$usingVendor->linkedin?>">
  	</p>
    <p>
    	<span class="showSocialMedia">
            <? 
				$checked = "";
				if ($vendor->showGooglePlus){
					$checked = "checked";
				}
			?>
            <label for="showGooglePlus">Show?</label>
            <input type="checkbox" name="showGooglePlus" id="showGooglePlus"  <?=$checked?>/>
    	</span>
    	<label for="googlePlus">GooglePlus(optional):</label>
    	<input class="hasShowOption" type="text" name="googlePlus" id="googlePlus" title="GooglePlus(optional)" value="<?=$usingVendor->googlePlus?>">
  	</p>
    <p>
    	<span class="showSocialMedia">
            <? 
				$checked = "";
				if ($vendor->showYoutube){
					$checked = "checked";
				}
			?>
            <label for="showYoutube">Show?</label>
            <input type="checkbox" name="showYoutube" id="showYoutube"  <?=$checked?>/>
    	</span>
    	<label for="youtube">youtube(optional):</label>
    	<input class="hasShowOption" type="text" name="youtube" id="youtube" title="youtube(optional)" value="<?=$usingVendor->youtube?>">
  	</p>
    <p>
    	<span class="showSocialMedia">
            <? 
				$checked = "";
				if ($vendor->showVimeo){
					$checked = "checked";
				}
			?>
            <label for="showVimeo">Show?</label>
            <input type="checkbox" name="showVimeo" id="showVimeo"  <?=$checked?>/>
    	</span>
    	<label for="vimeo">Vimeo(optional):</label>
    	<input class="hasShowOption" type="text" name="vimeo" id="vimeo" title="Vimeo(optional)" value="<?=$usingVendor->vimeo?>">
  	</p>
    <p>
    	<label for="introText">Brief Introduction:</label>
    	<textarea name="introText" id="introText" title="Brief Introduction"><?=$usingVendor->introText?></textarea>
    </p>
    <p>
    	<label for="ticketToSavings">Describe Your Ticket To Savings:</label>
    	<textarea name="ticketToSavings" id="ticketToSavings" title="Describe Your Ticket To Savings"><?=$usingVendor->ticketToSavings?></textarea>
    </p>
    <p>
    	<label for="ticketToSavingsAmount">Dollar Amount of Ticket To Savings:</label>
    	<input type="text" name="ticketToSavingsAmount" id="ticketToSavingsAmount" title="Ticket To Savings Dollar Amount" value="<?=$usingVendor->ticketToSavingsAmount?>">
  	</p>
    <input type="hidden" id="logo" name="logo" value="<?=$usingVendor->logo?>"/>
    <p>
    	<div id="logoDiv"><img src="images/uploadNeeded_missingImage.gif" id="logoImg"/></div>
    </p>
    <?
		if ($mobileDetect->isMobile() || $mobileDetect->isTablet()){
			echo '<p class="centerButton"><input type="button" id="logoBrowseButton_email" value="Upload Logo via Email";/></p>';
		}else{
			echo '<p class="centerButton"><input type="button" id="logoBrowseButton" value="Upload Logo";/></p>';
		}
	?>
    <p class="centerButton"><input type="button" class="removeImageButton" id="logo_RemoveButton" value="Remove Logo";/></p>
   <!-- <input type="button" id="logoBrowseButton" value="Upload Logo";/>
    <input type="button" id="logoBrowseButton_email" value="Upload Logo via Email";/>-->
    
    <input type="hidden" id="headshotImage" id="headshotImage" value="<?=$usingVendor->headshotImage?>"/>
    <p>
    	<div id="headshotImageDiv"><img src="images/uploadNeeded_missingImage.gif" id="headshotImageImg"/></div>
    </p>
    
    <?
		if ($mobileDetect->isMobile() || $mobileDetect->isTablet()){
			echo '<p class="centerButton"><input type="button" id="headshotImageBrowseButton_email" value="Upload Profile Photo via Email";/></p>';
		}else{
			echo '<p class="centerButton"><input type="button" id="headshotImageBrowseButton" value="Upload Profile Photo";/></p>';
		}
	?>
    <p class="centerButton"><input type="button" class="removeImageButton" id="headshotImage_RemoveButton" value="Remove Profile Photo";/></p>
   <!-- <input type="button" id="headshotImageBrowseButton" value="Upload Profile Photo";/>
    <input type="button" id="headshotImageBrowseButton_email" value="Upload Profile Photo via Email";/> -->
    
</form>
<input type="button" id="cancelEditButton" value="Cancel Edit"/>
<input type="submit" id="saveEditButton" value="Save"/>
<input type="button" id="saveAndCloseButton" value="Save And Close"/>
</section>
<script>
var phoneCount = <?=$phoneCount?>;
var categoryCount = <?=$categoryCount?>;

function addNewCategory(){
	var newCategoryP = $('.categoryBlock').first().clone();
	var categoryInput = $(newCategoryP).find('[name=category]');
	var newUniqueName = placeUniqueNumberInString($(categoryInput).prop("id"),categoryCount);
	$(categoryInput).prop("id",newUniqueName);
	$(categoryInput).parent().find("label").attr("for",newUniqueName);
	$(newCategoryP).insertAfter($('.categoryBlock').last());
	clearCategory(newCategoryP);
	$(newCategoryP).find("label").each(function(){
			addInlineLabelEvent(this);
		});
	categoryCount++;
}

function addNewPhone(){
	var newPhoneBlock = $('.phoneBlock').first().clone();
	$(newPhoneBlock).find('[type=radio]').each(function(){
		var currentName = $(this).attr("name");
		var newUniqueName = placeUniqueNumberInString(currentName,phoneCount);
		$(this).attr("name",newUniqueName);
	});
	var phoneInput = newPhoneBlock.find('[name=phone]');
	var newUniqueName = placeUniqueNumberInString(phoneInput.prop("id"),phoneCount);
	$(phoneInput).prop("id",newUniqueName);
	$(phoneInput).parent().find("label").attr("for",newUniqueName);
	
	var textingCB = newPhoneBlock.find('[name=texting]');	var newUniqueName = placeUniqueNumberInString(textingCB.prop("id"),phoneCount);
	$(textingCB).prop("id",newUniqueName);
	$(textingCB).parent().find("label").attr("for",newUniqueName);
	
	var textingAddrInput = newPhoneBlock.find('[name=textingAddr]');
	var newUniqueName = placeUniqueNumberInString(textingAddrInput.prop("id"),phoneCount);
	$(textingAddrInput).prop("id",newUniqueName);
	$(textingAddrInput).parent().find("label").attr("for",newUniqueName);
	
	$(newPhoneBlock).insertAfter($('.phoneBlock').last());
	clearPhone(newPhoneBlock);
	addRadioChangeEvent(newPhoneBlock);
	addCanTextEvt(textingCB);
	$(newPhoneBlock).find("label").each(function(){
			addInlineLabelEvent(this);
		});

	phoneCount++;
}

function placeUniqueNumberInString(stringItem,newNumber){
	var newString = stringItem;
	if (stringItem.indexOf("_") != -1){
		var stringItemArray = stringItem.split('_');
		newString = stringItemArray[0]+"_"+newNumber;
	}
	return newString;
}
function deletePhone(phoneDeleteSpan){
	if ($('.phoneBlock').length != 1)
		$(phoneDeleteSpan).parent().remove();
	else
		clearPhone($('.phoneBlock').first());	
}

function deleteCategory(categoryDeleteSpan){
	if ($('.categoryBlock').length != 1)
		$(categoryDeleteSpan).parent().remove();
	else
		clearCategory($('.categoryBlock').first());	
}

function clearCategory(categoryBlockToClear){
	var category = $(categoryBlockToClear).find("input");
	var labelText = $(categoryBlockToClear).find("label").text().replace(":","");
	$(category).val(labelText);
	$(category).addClass("fieldDefault");
}

function clearPhone(phoneBlockToClear){
	var phoneNumber = $(phoneBlockToClear).find(".phoneNumber");
	var labelText = $(phoneNumber).parent().find("label").text().replace(":","");
	$(phoneNumber).val(labelText);
	$(phoneNumber).addClass("fieldDefault");
	$(phoneBlockToClear).find('[type=radio]').each(function(){
			if ($(this).val() == "cell"){
				$(this).prop("checked",true);
				changePhoneType(this);
			}
		});
	$(phoneBlockToClear).find(".checkTexting").find('[type=checkbox]').prop("checked",false);
	$(phoneBlockToClear).find("[name=textingAddr]").val("");
	$(phoneBlockToClear).find("[name=textingAddr]").parent().addClass("hidden");
}

function changePhoneType(phoneTypeRB){
	var phoneBlock = $(phoneTypeRB).parentsUntil(".phoneBlock").parent();
	if ($(phoneTypeRB).val() == "cell"){
		$(phoneBlock).find(".checkTexting").removeClass("hidden");
		$(phoneBlock).find(".checkTexting").find('[type=checkbox]').prop("checked",false);
	}else{
		$(phoneBlock).find(".checkTexting").addClass("hidden");
		$(phoneBlock).find(".checkTexting").find('[type=checkbox]').prop("checked",false);
		$(phoneBlock).find("[name=textingAddr]").val("");
		$(phoneBlock).find("[name=textingAddr]").parent().addClass("hidden");
	}
}

function addRadioChangeEvent(phoneBlock){
	$(phoneBlock).find('[type=radio]').each(function(){
			var currentRadio = $(this);
			$(this).on("change",function(){
					changePhoneType(currentRadio);
				});
		});
}

function addInlineLabelEvent(labelElement){
	var forElement = $(labelElement).attr("for");
	if (!$("#"+forElement).is(":checkbox")){
		$(labelElement).addClass("labelHide");
		var labelText = $(labelElement).text().replace(":","");
		if ($("#"+forElement).val() == ""){
			$("#"+forElement).val(labelText)
							 .addClass("fieldDefault")
			}
	$("#"+forElement).focusin(function(e){
								$(this).removeClass("fieldDefault");
								if ($(this).val() == labelText)
									$(this).val('');
							  })
							  .focusout(function(e) {
									if (($(this).val() == labelText)|| ($(this).val() == '')){
										$(this).val(labelText);
										$(this).addClass("fieldDefault");
									}
								});	
	}
}

function getValueNoLabel(itemToGetValFrom){
	var valueToReturn = "";
	if (!$(itemToGetValFrom).hasClass("fieldDefault")){
		valueToReturn = $(itemToGetValFrom).val();
	}
	return (valueToReturn);
	
}

function checkForEmailedImage(dropDivID,imgID,pathID,dir,retries){	
	$.ajax({url:'checkForEmailedVendorImage.php', 
				type:'POST',
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				dataType:'json',
				data:{uid:<?=$usingVendor->id?>,dir:dir,maxSize:<?=$maxUploadSize?>}
			})
	.done(function(imageData){
		foundImage = imageData.found;
		if (foundImage){
			//update link
			var imageFilePath = 'images/'+dir+'/'+imageData.fileName;
			var d = new Date();
			//$("#"+dropDivID).html('<img src="'+imageFilePath+"?"+d.getTime()+'" id="'+imgID+'"/>');
			$("#"+imgID).attr("src",imageFilePath+"?"+d.getTime());
			$("#"+pathID).val(imageFilePath);
			saveVendor();	
		}else{
			retries++;
			setTimeoutForEmailedImageCheck(dropDivID,imgID,pathID,dir,retries);
			//setTimeoutForEmailedImageCheck('logoDiv','logoImg','logo','vendorLogos',retries);	
		}
	});
}

function saveVendor(){
	$("#saveEditButton").click();	
}
function setTimeoutForEmailedImageCheck(dropDivID,imgID,pathID,dir,retries){
		if (retries < 20){
			window.setTimeout(function(){
				checkForEmailedImage(dropDivID,imgID,pathID,dir,retries);
			},15000);	
			
		}else{
			//put time out message
			//$("#"+dropDivID).html('<img src=" id="imgID"/>');	
			var d = new Date();
			$("#"+imgID).attr("src","images/uploadTimedOut.gif"+"?"+d.getTime());
		}
}

function addCanTextEvt(canTextCB){
	$(canTextCB).on("click",function(evt){
		var textAddrSpan = $(this).parent().find("span");
		if ($(this).prop("checked"))
			$(textAddrSpan).removeClass("hidden");
		else
			$(textAddrSpan).addClass("hidden");
	});
		
}

function initializeEvents(){
	$(".phoneBlock").each(function(){
			addRadioChangeEvent(this);
		});	
	$("label").each(function(){
		addInlineLabelEvent(this);
	});
	$('[name=texting]').each(function(){
		addCanTextEvt(this);
	});
	$("#logoBrowseButton_email").on("click",function(){
		$.ajax({url:'deletePriorEmailedVendorImage.php',
				type:'POST',
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				data:{fName:<?=$usingVendor->id?>+"_vendorLogos_"+<?=$maxUploadSize?>}
			})
		 .done(function(){
		<?
			if ($mailToLink != ""){
				//echo "$('#logoDiv').html('<img src=\"images/uploadingImage_forEmail.gif\" id=\"logoImg\"/>');";
				echo 'var d = new Date();';
				echo '$("#logoImg").attr("src","images/uploadingImage_forEmail.gif"+"?"+d.getTime());';
				$mailToLink .= $mailToLinkSubject.'vendorLogos '.$maxSizeString.'&body='.$mailToLinkBody;
				echo 'window.setTimeout(function(){window.location.href = "'.$mailToLink.'";},1000);';
				
				//echo 'window.location.href = "'.$mailToLink.'";';
			//	echo "checkForEmailedImage('logoDiv','logoImg','logo','vendorLogos',0);";
			}
		?>
		 });
		 
	});
	$("#headshotImageBrowseButton_email").on("click",function(){
		$.ajax({url:'deletePriorEmailedVendorImage.php',
				type:'POST',
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				data:{fName:<?=$usingVendor->id?>+"_vendorHeadshots_"+<?=$maxUploadSize?>}
			})
		 .done(function(){
		<?
			if ($mailToLink != ""){
				//echo "$('#headshotImageDiv').html('<img src=\"images/uploadingImage_forEmail.gif\" id=\"headshotImageImg\"/>');";
				echo 'var d = new Date();';
				echo '$("#headshotImageImg").attr("src","images/uploadingImage_forEmail.gif"+"?"+d.getTime());';
				$mailToLink .= $mailToLinkSubject.'vendorHeadshots '.$maxSizeString.'&body='.$mailToLinkBody;
				echo 'window.setTimeout(function(){window.location.href = "'.$mailToLink.'";},1000);';
				//echo 'window.location.href = "'.$mailToLink.'";';
				echo "checkForEmailedImage('headshotImageDiv','headshotImageImg','headshotImage','vendorHeadshots',0);";
			}
		?>
		 });
		 
	});
	
	$(".removeImageButton").on("click",function(evt){
		if (confirm("Remove Image?")){
			var idArr = $(this).prop("id").split("_");
			var keyword = idArr[0];
			var imageToRemove = $("#"+keyword).val();
			$("#"+keyword).val('');
			$("#"+keyword+"Img").error();
			saveVendor();
			$.ajax({url:'removeVendorImage.php', 
					type:'POST',
					contentType:'application/x-www-form-urlencoded; charset=UTF-8',
					data:{img:imageToRemove
					  }
				});
			
		}
	});
	
	 $("#logoImg").on("error",function(e){
		 var d = new Date(); 
		 $("#logoImg").attr("src","images/uploadNeeded_missingImage.gif"+"?"+d.getTime());	
	 });
	 
	 $("#headshotImageImg").on("error",function(e){
		 var d = new Date();
		 $("#headshotImageImg").attr("src","images/uploadNeeded_missingImage.gif"+"?"+d.getTime());	
	 });
	
	$("#cancelEditButton").on("click",function(){
		window.location.href="http://www.stlbridalpros.com/vendorAdmin.php";
		});
	$("#saveAndCloseButton").on("click",function(){
		save(true);
	});
	$("#saveEditButton").on("click",function(){
			save(false);
		});
}

function save(closeFormWhenDone){
	var savedVendorObj = {};
			savedVendorObj['id'] = $("#id").val();
			savedVendorObj['name'] = getValueNoLabel($("#name"));
			savedVendorObj['email'] = getValueNoLabel($("#email"));
			savedVendorObj['website'] = getValueNoLabel($("#website"));
			savedVendorObj['address'] = getValueNoLabel($("#address"));
			savedVendorObj['facebook'] = getValueNoLabel($("#facebook"));
			savedVendorObj['showFacebook'] = ($("#showFacebook").prop("checked"))?true:false;
			savedVendorObj['instagram'] = getValueNoLabel($("#instagram"));
			savedVendorObj['showInstagram'] = ($("#showInstagram").prop("checked"))?true:false;
			savedVendorObj['twitter'] = getValueNoLabel($("#twitter"));
			savedVendorObj['showTwitter'] = ($("#showTwitter").prop("checked"))?true:false;
			savedVendorObj['pintrist'] = getValueNoLabel($("#pintrist"));
			savedVendorObj['showPintrist'] = ($("#showPintrist").prop("checked"))?true:false;
			savedVendorObj['linkedin'] = getValueNoLabel($("#linkedin"));
			savedVendorObj['showLinkedin'] = ($("#showLinkedin").prop("checked"))?true:false;
			savedVendorObj['googlePlus'] = getValueNoLabel($("#googlePlus"));
			savedVendorObj['showGooglePlus'] = ($("#showGooglePlus").prop("checked"))?true:false;
			savedVendorObj['youtube'] = getValueNoLabel($("#youtube"));
			savedVendorObj['showYoutube'] = ($("#showYoutube").prop("checked"))?true:false;
			savedVendorObj['vimeo'] = getValueNoLabel($("#vimeo"));
			savedVendorObj['showVimeo'] = ($("#showVimeo").prop("checked"))?true:false;
			savedVendorObj['introText'] = getValueNoLabel($("#introText"));
			savedVendorObj['ticketToSavings'] = getValueNoLabel($("#ticketToSavings"));
			savedVendorObj['ticketToSavingsAmount'] = getValueNoLabel($("#ticketToSavingsAmount"));
			//savedVendorObj['landingPage'] = $("#landingPage").val();
			savedVendorObj['logo'] = $("#logo").val();
			savedVendorObj['headshotImage'] = $("#headshotImage").val();
			savedVendorObj['categories'] = new Array();
			$('[name=category]').each(function(){
				if ($(this).val() != "")
					savedVendorObj['categories'].push(getValueNoLabel($(this)));
			});
			savedVendorObj['phones'] = new Array();
			$(".phoneBlock").each(function(){
				var phoneObj = {};
				$(this).find('[type=radio]').each(function(){
					if ($(this).prop("checked")){
						phoneObj.type = $(this).val();
					}
				});
				phoneObj.phoneNumber = getValueNoLabel($(this).find(".phoneNumber"));
				if ($(this).find('[name=texting]').prop("checked"))
					phoneObj.textMe = "yes";
				else
					phoneObj.textMe = "no";
					
				if (phoneObj.phoneNumber != ""){
					savedVendorObj['phones'].push(phoneObj);	
				}
				if (phoneObj.phoneNumber !=
			});
		//public $password = "";
			
			$.ajax({url:'saveEditedVendor.php', 
				type:'POST',
				contentType:'application/x-www-form-urlencoded; charset=UTF-8',
				dataType:'html',
				data:{vendorObj:serialize(savedVendorObj)
				  }
			})
			.done(function(){
				if (closeFormWhenDone){
					$("#cancelEditButton").click();
				}
			});	
}
function imageUploaderInit(imageUploader,uploadDir,browseButton,dropDiv,uploadedImageID,pathHolderID)
 {
		imageUploader = new plupload.Uploader({
		  browse_button: browseButton, // this can be an id of a DOM element or the DOM element itself
		  url: 'uploadVendorImage.php?folder='+encodeURIComponent(uploadDir)+'&size=<?=$maxUploadSize?>'+'&fileName=<?=$usingVendor->id?>',
		  chunk_size: '200kb',
		  max_retries: 3,
		  filters: {
			  mime_types : [{ title : "Image files", extensions : "jpg,gif,png" }]
		  },
		  container: dropDiv,
		  drop_element: dropDiv,
		  multi_selection: false,
		  init: {
				FilesAdded: function(up, files) {
					plupload.each(files, function(file) {
						$("#"+uploadedImageID).addClass("hidden");
						$("#"+dropDiv).append('<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>');
					});
					imageUploader.start();
				},
		 
				UploadProgress: function(up, file) {
					document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
				},
				FileUploaded: function(up,file,info){
					info = $.parseJSON(info.response);
					if (info.uploaded){
						var d = new Date();
						//$("#"+dropDiv).html('<img src="'+info.filePath+"?"+d.getTime()+'" id="'+uploadedImageID+'"/>');
						$("#"+uploadedImageID).removeClass("hidden").attr("src",info.filePath+"?"+d.getTime());
						$("#"+pathHolderID).val(info.filePath);
						$("#"+file.id).remove();
					}
					else
						$("#"+dropDiv).append("\nError #" + info.code + ": " + info.message);
					
					saveVendor();  //autosave after updating image
					imageUploader.destroy();
					imageUploader = imageUploaderInit(imageUploader,uploadDir,browseButton,dropDiv,uploadedImageID,pathHolderID);
				},
		 		Error: function(up, err) {
					$("#"+dropDiv).append("\nError #" + err.code + ": " + err.message);
				}
			}
		
		});
		
		imageUploader.init();
		return (imageUploader);
	 
 }
 
var logoImageUploader,headshotImageUploader;
$( document ).ready(function() {	
	initializeEvents();	
	$("#logoImg").attr("src",$("#logo").val());
	$("#headshotImageImg").attr("src",$("#headshotImage").val());
	logoImageUploader = imageUploaderInit(logoImageUploader,'images/vendorLogos/','logoBrowseButton','logoDiv','logoImg','logo');
	headshotImageUploader = imageUploaderInit(headshotImageUploader,'images/vendorHeadshots/','headshotImageBrowseButton','headshotImageDiv','headshotImageImg','headshotImage');
	//logoImageUploader.init()
});
</script>
</body>
</html>