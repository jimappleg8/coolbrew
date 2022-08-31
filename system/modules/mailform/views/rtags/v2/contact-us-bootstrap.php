<?php if ($form_part == 1): ?>

<div id="contact-us-bootstrap">
  <div id="contact-us-bootstrap-inner">

    <div id="contact-us-bootstrap-start">
      <div id="contact-us-bootstrap-start-inner">

   <form action="<?=$action;?>" method="post" class="horizontal">
      <fieldset>
         <div class="field">
            <?=form_hidden('siteid', SITE_ID);?>
         </div>
         <div class="field">
            <?=form_hidden('form_token', $this->validation->form_token);?>
         </div>
         <div class="field">
            <?=form_hidden('form_part', $this->validation->form_part);?>
         </div>
         <div class="field">
            <?=form_hidden('Marketing', $this->validation->Marketing);?>
         </div>
         <div class="field">
            <?=form_hidden('Release', $this->validation->Release);?>
         </div>

    <div class="row">
      <div id="mailform-fname" class="form-group required col-md-6">
         <label for="FName"><?=$labels['FName'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'FName', 'id'=>'FName', 'maxlength'=>'25', 'value'=>$this->validation->FName));?>
         <?=$this->validation->FName_error;?>
      </div>

      <div id="mailform-lname" class="form-group required col-md-6">
         <label for="LName"><?=$labels['LName'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'LName', 'id'=>'LName', 'maxlength'=>'25', 'value'=>$this->validation->LName));?>
         <?=$this->validation->LName_error;?>
      </div>
    </div>

    <div class="row">
      <div id="mailform-email" class="form-group required col-md-6">
         <label for="Email"><?=$labels['Email'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'value'=>$this->validation->Email));?>
         <?=$this->validation->Email_error;?>
      </div>
    </div>

    <div class="row" style="margin-top:1em;">
      <div id="mailform-submit" class="buttons col-md-12">
         <?=form_submit(array('name'=>'submit', 'class'=>'btn btn-default', 'value'=>'Continue'))?>
      </div>
    </div>

      </fieldset>
   </form>

       </div>   <?php /* contact-us-start-inner */ ?>
    </div>   <?php /* contact-us-start */ ?>

  </div>   <?php /* contact-us-inner */ ?>
</div>   <?php /* contact-us */ ?>

<?php elseif ($form_part == 2): ?>

   <div id="contact-us-bootstrap">
     <div id="contact-us-bootstrap-inner">
     
   <form id="contact-us-form" action="<?=$action;?>" method="post" class="horizontal">
      <fieldset>
      
      <div class="field hidden">
         <?=form_hidden('siteid', SITE_ID);?>
      </div>
      <div class="field hidden">
         <?=form_hidden('form_token', $this->validation->form_token);?>
      </div>
      <div class="field hidden">
         <?=form_hidden('form_part', 2);?>
      </div>
      
      <h2>Your Information <span>Marked fields are required</span></h2>
      
    <div class="row">
      <div id="mailform-subject" class="form-group required col-md-6">
         <label for="Subject"><?=$labels['Subject'];?>:</label>
         <?=form_dropdown('Subject', $subjects, $this->validation->Subject, 'class="form-control"');?>
         <?=$this->validation->Subject_error;?>
      </div>
    </div>

    <div class="row">
      <div id="mailform-fname" class="form-group required col-md-6">
         <label for="FName"><?=$labels['FName'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'FName', 'id'=>'FName', 'maxlength'=>'25', 'value'=>$this->validation->FName));?>
         <?=$this->validation->FName_error;?>
      </div>

      <div id="mailform-lname" class="form-group required col-md-6">
         <label for="LName"><?=$labels['LName'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'LName', 'id'=>'LName', 'maxlength'=>'25', 'value'=>$this->validation->LName));?>
         <?=$this->validation->LName_error;?>
      </div>
    </div>

    <div class="row">
      <div id="mailform-address1" class="form-group required col-md-12">
         <label for="Address1"><?=$labels['Address1'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'Address1', 'id'=>'Address1', 'maxlength'=>'40', 'size'=>'35', 'value'=>$this->validation->Address1));?>
         <?=$this->validation->Address1_error;?>
      </div>
    </div>

    <div class="row">
      <div id="mailform-address2" class="form-group col-md-12">
         <label for="Address2"><?=$labels['Address2'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'Address2', 'id'=>'Address2', 'maxlength'=>'40', 'size'=>'35', 'value'=>$this->validation->Address2));?>
         <?=$this->validation->Address2_error;?>
      </div>
    </div>

    <div class="row">
      <div id="mailform-city" class="form-group required col-md-6">
         <label for="City"><?=$labels['City'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'City', 'id'=>'City', 'maxlength'=>'30', 'value'=>$this->validation->City));?>
         <?=$this->validation->City_error;?>
      </div>

      <div id="mailform-state" class="form-group required col-md-6">
         <label for="State"><?=$labels['State'];?>:</label>
         <?=form_dropdown('State', $states, $this->validation->State, 'class="form-control"');?>
         <?=$this->validation->State_error;?>
      </div>
    </div>

    <div class="row">
      <div id="mailform-zip" class="form-group required col-md-6">
         <label for="Zip"><?=$labels['Zip'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'Zip', 'id'=>'Zip', 'maxlength'=>'10', 'value'=>$this->validation->Zip));?>
         <?=$this->validation->Zip_error;?>
      </div>

      <div id="mailform-country" class="form-group col-md-6">
         <label for="Country"><?=$labels['Country'];?>:</label>
         <?=form_dropdown('Country', $countries, $this->validation->Country, 'class="form-control"');?>
         <?=$this->validation->Country_error;?>
      </div>
    </div>

    <div class="row">
      <div id="mailform-phone" class="form-group col-md-6">
         <label for="Phone"><?=$labels['Phone'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'Phone', 'id'=>'Phone', 'maxlength'=>'14', 'value'=>$this->validation->Phone));?>
         <?=$this->validation->Phone_error;?>
      </div>
    </div>

    <div class="row">
      <div id="mailform-email" class="form-group required col-md-6">
         <label for="Email"><?=$labels['Email'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'value'=>$this->validation->Email));?>
         <?=$this->validation->Email_error;?>
      </div>
    </div>

    <div class="row">
      <div id="mailform-email2" class="form-group required col-md-6">
         <label for="Email2"><?=$labels['Email2'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'Email2', 'id'=>'Email2', 'maxlength'=>'255', 'value'=>$this->validation->Email2));?>
         <?=$this->validation->Email2_error;?>
      </div>
    </div>

      <h2>Product Information</h2>
      <p>If you are contacting us about a product that you purchased, please provide the product information below so we can better help you.</p>

    <div class="row">
      <div id="mailform-productupc" class="form-group col-md-6">
         <label for="ProductUPC"><?=$labels['ProductUPC'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'ProductUPC', 'id'=>'ProductUPC', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->ProductUPC));?>
         <?=$this->validation->ProductUPC_error;?>
         <p class="help-block"><?=$labels['ProductUPCDesc'];?>
         <br><img src="http://resources.hcgweb.net/shared/mailform/upc-example.gif" width="200" height="50" alt="" style="padding:0 0 9px 9px;" /></p>
      </div>
    </div>

    <div class="row">
      <div id="mailform-bestbydatelotcode" class="form-group col-md-6">
         <label for="BestByDateLotCode"><?=$labels['BestByDateLotCode'];?>:</label>
         <?=form_input(array('class'=>'text form-control', 'name'=>'BestByDateLotCode', 'id'=>'BestByDateLotCode', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->BestByDateLotCode));?>
         <?=$this->validation->BestByDateLotCode_error;?>
         <p class="help-block"><?=$labels['BestByDateLotCodeDesc'];?></p>
      </div>
    </div>

      <h2>What's On Your Mind? <span>This field is required</span></h2>

    <div class="row">
      <div id="mailform-comment" class="form-group required col-md-12">
         <label for="Comment" class="required"><?=$labels['Comment'];?>:</label>
         <?=form_textarea(array('class'=>'form-control', 'name'=>'Comment', 'id'=>'Comment', 'cols' => 40, 'rows' => 10, 'wrap' => "virtual", 'value'=>$this->validation->Comment));?>
         <?=$this->validation->Comment_error;?>
      </div>
    </div>

   <?php if ($marketing == TRUE || $release == TRUE): ?>
      <h2>Your Preferences</h2>
   <?php endif; ?>
      
   <?php if ($marketing == TRUE): ?>
    <div class="row">
      <div id="mailform-marketing" class="checkbox col-md-12">
         <label for="Marketing">
         <input type="checkbox" class="checkbox" value="YES" name="Marketing" id="Marketing"<?=$this->validation->set_checkbox('Marketing','YES');?> /> <?php if ($labels['Marketing'] == '##default##'): ?> Would you like to receive information from <?=$hcg_site;?> in the future? If yes, leave this box checked.<?php if ($privacy != ''): ?> If you have any concerns, please read our <a href="<?=$privacy;?>">privacy policy</a>.<?php endif; ?><?php else: ?><?=$labels['Marketing'];?><?php endif; ?>
         </label>
      </div>
    </div>
   <?php else: ?>
      <div class="field">
         <?=form_hidden('Marketing', 0);?>
      </div>
   <?php endif; ?>

   <?php if ($release == TRUE): ?>
    <div class="row">
      <div id="mailform-release" class="checkbox col-md-12">
         <label for="Release">
         <input type="checkbox" class="checkbox" value="YES" name="Release" id="Release"<?=$this->validation->set_checkbox('Release','YES');?> /> <?=$labels['Release'];?>
         </label>
      </div>
    </div>
   <?php else: ?>
      <div class="field">
         <?=form_hidden('Release', 0);?>
      </div>
   <?php endif; ?>
   
    <div class="row" style="margin-top:1em;">
      <div id="mailform-submit" class="buttons col-md-12">
         <?=form_submit(array('id'=>'mailform-submit-button', 'name'=>'submit', 'class'=>'btn btn-default', 'value'=>$labels['SubmitText']))?>
         <p id="mailform-submit-spinner" style="display:none;"><img src="http://resources.hcgweb.net/rtags/images/36px-spinner-black.gif" style="vertical-align:middle;" /><span style="padding-left:9px;">Sending...</span></p>
      </div>
    </div>

   </fieldset>

   </form>

      </div>   <?php /* contact-us */ ?>
   </div>   <?php /* contact-us-inner */ ?>   

<?php elseif ($form_part == 3): ?>

   <div id="contact-us-wrapper">
   <div id="contact-us-inner">

   <h3>Your message has been sent!</h3>

   <p>Thank you for taking the time to contact us.</p>

   <p><a href="<?=$home_link;?>">Return to the home page.</a></p>

   </div>   <?php /* contact-us-wrapper */ ?>
   </div>   <?php /* contact-us-inner */ ?>

<?php endif; ?>
