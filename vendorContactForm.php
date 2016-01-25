<? //assumes that this will be included in a page which reads the xml file ?>
<form id="vendorContactForm" name="vendorContactForm" method="post" action="#">
  <div id="validationMessages"></div>
  <p>
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" required data-validation_label="Name">
  </p>
  <p>
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required data-validation_label="Email" data-validation_type="EMAIL">
  </p>
  <p>
    <label for="phone">Phone:</label>
    <input type="tel" name="phone" id="phone" required data-validation_label="Phone" data-validation_type="USA_PHONE">
  </p>
  <p>
    <label for="weddingDate">Wedding Date:</label>
    <input type="date" name="weddingDate" id="weddingDate">
  </p>
  <p>I am interested in the following services:</p>
  <?
  		$categoryCount = 0;
  		foreach($vpc->postableVendors as $vendor){
		//foreach($vpc->vendors as $vendor){
			foreach($vendor->categoryArray as $sortedCategory){
				if ($sortedCategory != ""){
				?>
                	<p>
                    	<input type="checkbox" name="category" id="category<?=$categoryCount?>" value="<?=$sortedCategory?>"/>
                        <label for="category<?=$categoryCount?>"><?=$sortedCategory?></label>
                    </p>
                <?
				$categoryCount++;
				}
			}
		}
			
	?>
  <p>
    <label for="otherServicesNeeded">Are there other services you need? Let us know.  We can help.</label>
    <textarea name="otherServicesNeeded" id="otherServicesNeeded"></textarea>
  </p>
  <p>
    <label for="comments">Comments:</label>
    <textarea name="comments" id="comments"></textarea>
  </p>
  <p>
    <label for="spamCheck">Are you human? Then type 'yes':</label>
    <input type="text" name="spamCheck" id="spamCheck" required data-validation_label="Spam Check" data-validation_type="SPAMCHECK"> 
  </p>
  <p>
  	<input type="button" name="contactSubmitButton" id="contactSubmitButton" value="Submit">
    <input type="reset" name="reset" id="reset" value="Clear">
  </p>
</form>