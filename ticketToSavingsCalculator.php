<?
	$defaultTicket2SavingsCalcText = "Calculate your savings!";
?>
    <div id="tkt2SavCalculator">
        <section id="ticketCalcDisplay" class="ticket2SavingsCalcDefaultFont"><?=$defaultTicket2SavingsCalcText?></section>
        <section id="ticketVendorCB">
        <?
        foreach($vpc->postableVendorsNonVenue as $vendor){
            if (count($vendor->categoryArray) > 0){
                $service = $vendor->categoryArray[0];
                if ($service != ""){
                ?>
                    <p>
                        <span>
                        <input type="checkbox" name="calculator" id="calculator_<?=$vendor->id?>" value="<?=$vendor->ticketToSavingsAmount?>" onClick="addSavings(this);"/>
                        <?=$service?></span><br>
                    </p>
                <?
                }
            }
        }
        $highestVenueTicketToSavings = 0;
        foreach($vpc->postableVendorsVenue as $venueVendor){
            if ($venueVendor->ticketToSavingsAmount > $highestVenueTicketToSavings)
                $highestVenueTicketToSavings = $venueVendor->ticketToSavingsAmount;
        }
        if ($highestVenueTicketToSavings > 0){
        ?>
            <p>
                <span>
                <input type="checkbox" name="calculator" id="calculator_Venue" value="<?=$highestVenueTicketToSavings?>" onClick="addSavings(this);"/>
                Wedding Venue</span>
            </p>
        <?	
        }
        ?>
        </section>
    </div>  <!--end div id=tkt2SavCalculator-->
    <div id="ticket2SavingsVendorOfferings">
    	<?
        foreach($vpc->postableVendorsNonVenue as $vendor){
            if (count($vendor->categoryArray) > 0){
                $service = $vendor->categoryArray[0];
                if ($service != ""){
                ?>
                    <p id="calculatorVendorSavings_<?=$vendor->id?>" class="hidden">
                        <?=$vendor->ticketToSavings?>
                    </p><br>
                <?
                }
            }
        }
        $highestVenueTicketToSavings = 0;
        foreach($vpc->postableVendorsVenue as $venueVendor){
            if ($venueVendor->ticketToSavingsAmount > $highestVenueTicketToSavings)
                $highestVenueTicketToSavings = $venueVendor->ticketToSavingsAmount;
        }
        if ($highestVenueTicketToSavings > 0){
        ?>
            <p id="calculatorVendorSavings_Venue" class="hidden">
                  Check out each venue we have for awesome offers!
            </p><br>
        <?	
        }
        ?>
    </div>
<script>
function addSavings(cbClicked){
	var totalSavings = 0;
	var vendorCount = 0;
	/*if ($(cbClicked).prop("id") == "calculator_Venue")
		$("#calculatorVendorSavings_Venue").toggleClass("hidden");	
	else{
		$savingsIDArray = $(cbClicked).prop("id").split("_");
		$("#calculatorVendorSavings_"+$savingsIDArray[1]).toggleClass("hidden");
	}*/
	$("#ticketVendorCB input").each(function(){
		if ($(this).prop("checked")){
			totalSavings += Number($(this).val());
			vendorCount++;
		}
		
		//console.log("id clicked "+$(this).prop("id"));
		//console.log("#calculatorVendorSavings_"+$savingsIDArray[1]);
	});
	if (totalSavings != 0){
		switch (vendorCount){
			case 1:
				$("#ticketCalcDisplay").addClass("ticket2SavingsCalcDefaultFont");
				$("#ticketCalcDisplay").html("2 more needed!");
				break;
			case 2:
				$("#ticketCalcDisplay").addClass("ticket2SavingsCalcDefaultFont");
				$("#ticketCalcDisplay").html("Only 1 more needed!!");
				break;
			default:
				$("#ticketCalcDisplay").removeClass("ticket2SavingsCalcDefaultFont");
				$("#ticketCalcDisplay").html("Savings=>  $"+totalSavings);
				break;
		}
	}else{
		$("#ticketCalcDisplay").addClass("ticket2SavingsCalcDefaultFont");
		$("#ticketCalcDisplay").html("<?=$defaultTicket2SavingsCalcText?>");
	}
}

function initializeTicket2SavCalc(){
	//$("#ticketVendorCB input").on("click",addSavings);	
}
</script>