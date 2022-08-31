<?php if ($form_part == 1): ?>

<script type="text/javascript">
	<!--
	function counter() {
		var text = document.getElementById("Message").value;
		var len = text.length;
		if (len > 1000) {
			alert ("Sorry, you've exceeded the maximum number of characters allowed.");
			text = text.substring(1, len-3);
			document.getElementById("Message").value = text;
			return false;
		} else {
			document.getElementById("cntr").value = 1000 - len;
		return;
		}
	}
	//-->
</script>

   <div id="eyelevel-contact">
     <div id="eyelevel-contact-inner">

   <form action="<?=$action;?>" method="post" class="horizontal">
      <fieldset>
      
      <div class="field">
         <?=form_hidden('siteid', SITE_ID);?>
      </div>

      <div id="eyelevel-contact-customer" class="field required">
         <label for="Customer"><?=$labels['Customer'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'Customer', 'id'=>'Customer', 'maxlength'=>'60', 'size'=>'40', 'value'=>$this->validation->Customer));?>
         <?=$this->validation->Customer_error;?>
      </div>

      <div id="eyelevel-contact-email" class="field required">
         <label for="Email"><?=$labels['Email'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->Email));?>
         <?=$this->validation->Email_error;?>
      </div>

      <div id="eyelevel-contact-reason" class="field required">
         <label for="Reason"><?=$labels['Reason'];?>:</label>
         <?=form_dropdown('Reason', $reasons, $this->validation->Reason);?>
         <?=$this->validation->Reason_error;?>
      </div>

      <div id="eyelevel-contact-message" class="field required">
         <label for="Message" class="required"><?=$labels['Message'];?>:</label>
         <?=form_textarea(array('name'=>'Message', 'id'=>'Message', 'cols' => 45, 'rows' => 10, 'value'=>$this->validation->Message, 'onkeyup'=>"counter();"));?>
         <?=$this->validation->Message_error;?>
      </div>

      <div id="eyelevel-contact-cntr" class="field">
         <input type="text" id="cntr" size="5" maxlength="5" readonly="readonly" /> Characters Left
      </div>

      <div id="eyelevel-contact-submit" class="buttons">
         <?=form_submit(array('name'=>'submit', 'class'=>'button', 'value'=>$labels['SubmitText']))?>
      </div>

   </fieldset>

   </form>

      </div>   <?php // contact-us ?>
   </div>   <?php // contact-us-inner ?>

<?php elseif ($form_part == 2): ?>

   <div id="contact-us-wrapper">
   <div id="contact-us-inner">

   <h2>Your message has been sent!</h2>
   
   <p>Thank you for contacting the Jason Natural Online Store. Someone will reply, if applicable, within 2 business days.</p>

   <p><a href="/">Return to shopping</a></p>

   </div>   <?php /* eyelevel-contact */ ?>
   </div>   <?php /* eyelevel-contact-inner */ ?>

<?php endif; ?>
