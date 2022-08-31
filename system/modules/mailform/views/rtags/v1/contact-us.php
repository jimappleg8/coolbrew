<?php if ($form_part == 1): ?>

<div id="contact-us">
  <div id="contact-us-inner">

    <div id="contact-us-start">
      <div id="contact-us-start-inner">

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
         <div id="mailform-start-fname" class="field">
            <label for="FName"><?=$labels['FName'];?>:</label>
            <?=form_input(array('class'=>'text', 'name'=>'FName', 'id'=>'FName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->FName));?>
            <?=$this->validation->FName_error;?>
         </div>
         <div id="mailform-start-lname" class="field">
            <label for="LName"><?=$labels['LName'];?>:</label>
            <?=form_input(array('class'=>'text', 'name'=>'LName', 'id'=>'LName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->LName));?>
            <?=$this->validation->LName_error;?>
         </div>
         <div id="mailform-start-email" class="field">
            <label for="Email"><?=$labels['Email'];?>:</label>
            <?=form_input(array('class'=>'text', 'name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email));?>
            <?=$this->validation->Email_error;?>
         </div>
         <div id="mailform-start-submit" class="buttons">
            <?=form_submit(array('name'=>'submit', 'class'=>'button', 'value'=>'Continue'))?>
         </div>
      </fieldset>
   </form>

       </div>   <?php /* contact-us-start-inner */ ?>
    </div>   <?php /* contact-us-start */ ?>

  </div>   <?php /* contact-us-inner */ ?>
</div>   <?php /* contact-us */ ?>

<?php elseif ($form_part == 2): ?>

   <div id="contact-us">
     <div id="contact-us-inner">
   
   <form action="<?=$action;?>" method="post" class="horizontal">
      <fieldset>
      
      <div class="field">
         <?=form_hidden('siteid', SITE_ID);?>
      </div>
      <div class="field">
         <?=form_hidden('form_token', $this->validation->form_token);?>
      </div>

      <div id="mailform-fname" class="field required">
         <label for="FName"><?=$labels['FName'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'FName', 'id'=>'FName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->FName));?>
         <?=$this->validation->FName_error;?>
      </div>

      <div id="mailform-lname" class="field required">
         <label for="LName"><?=$labels['LName'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'LName', 'id'=>'LName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->LName));?>
         <?=$this->validation->LName_error;?>
      </div>

      <div id="mailform-address1" class="field required">
         <label for="Address1"><?=$labels['Address1'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'Address1', 'id'=>'Address1', 'maxlength'=>'40', 'size'=>'35', 'value'=>$this->validation->Address1));?>
         <?=$this->validation->Address1_error;?>
      </div>

      <div id="mailform-address2" class="field">
         <label for="Address2"><?=$labels['Address2'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'Address2', 'id'=>'Address2', 'maxlength'=>'40', 'size'=>'35', 'value'=>$this->validation->Address2));?>
         <?=$this->validation->Address2_error;?>
      </div>

      <div id="mailform-city" class="field required">
         <label for="City"><?=$labels['City'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'City', 'id'=>'City', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->City));?>
         <?=$this->validation->City_error;?>
      </div>

      <div id="mailform-state" class="field required">
         <label for="State"><?=$labels['State'];?>:</label>
         <?=form_dropdown('State', $states, $this->validation->State);?>
         <?=$this->validation->State_error;?>
      </div>

      <div id="mailform-zip" class="field required">
         <label for="Zip"><?=$labels['Zip'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'Zip', 'id'=>'Zip', 'maxlength'=>'10', 'size'=>'10', 'value'=>$this->validation->Zip));?>
         <?=$this->validation->Zip_error;?>
      </div>

      <div id="mailform-country" class="field">
         <label for="Country"><?=$labels['Country'];?>:</label>
         <?=form_dropdown('Country', $countries, $this->validation->Country);?>
         <?=$this->validation->Country_error;?>
      </div>

      <div id="mailform-phone" class="field">
         <label for="Phone"><?=$labels['Phone'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'Phone', 'id'=>'Phone', 'maxlength'=>'14', 'size'=>'14', 'value'=>$this->validation->Phone));?>
         <?=$this->validation->Phone_error;?>
      </div>

      <div id="mailform-email" class="field required">
         <label for="Email"><?=$labels['Email'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email));?>
         <?=$this->validation->Email_error;?>
      </div>

      <div id="mailform-email2" class="field required">
         <label for="Email2"><?=$labels['Email2'];?>:</label>
         <?=form_input(array('class'=>'text', 'name'=>'Email2', 'id'=>'Email2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email2));?>
         <?=$this->validation->Email2_error;?>
      </div>

      <div id="mailform-comment" class="field required">
         <label for="Comment" class="required"><?=$labels['Comment'];?>:</label>
         <?=form_textarea(array('name'=>'Comment', 'id'=>'Comment', 'cols' => 40, 'rows' => 10, 'wrap' => "virtual", 'value'=>$this->validation->Comment));?>
         <?=$this->validation->Comment_error;?>
      </div>

   <?php if ($marketing == TRUE): ?>
      <ul id="mailform-marketing" class="fields">
         <li>
         <input type="checkbox" class="checkbox" value="YES" name="Marketing" id="Marketing"<?=$this->validation->set_checkbox('Marketing','YES');?> /> <label for="Marketing"><?php if ($labels['Marketing'] == '##default##'): ?>Would you like to receive information from <?=$hcg_site;?> in the future? If yes, leave this box checked.<?php if ($privacy != ''): ?> If you have any concerns, please read our <a href="<?=$privacy;?>">privacy policy</a>.<?php endif; ?><?php else: ?><?=$labels['Marketing'];?><?php endif; ?></label>
         </li>
      </ul>
   <?php else: ?>
      <div class="field">
         <?=form_hidden('Marketing', 0);?>
      </div>
   <?php endif; ?>

   <?php if ($release == TRUE): ?>
      <ul id="mailform-release" class="fields">
         <li>
         <input type="checkbox" class="checkbox" value="YES" name="Release" id="Release"<?=$this->validation->set_checkbox('Release','YES');?> /> <label for="Release"><?=$labels['Release'];?></label>
         </li>
      </ul>
   <?php else: ?>
      <div class="field">
         <?=form_hidden('Release', 0);?>
      </div>
   <?php endif; ?>
   
      <div id="mailform-submit" class="buttons">
         <?=form_submit(array('name'=>'submit', 'class'=>'button', 'value'=>$labels['SubmitText']))?>
      </div>

   </fieldset>

   </form>

      </div>   <?php /* contact-us */ ?>
   </div>   <?php /* contact-us-inner */ ?>

<?php elseif ($form_part == 3): ?>

   <div id="contact-us-wrapper">
   <div id="contact-us-inner">

   <h2>Your message has been sent!</h2>
   
   <p>Thank you for taking the time to contact us.</p>

   <p><a href="/">Return to the home page.</a></p>

   </div>   <?php /* contact-us-wrapper */ ?>
   </div>   <?php /* contact-us-inner */ ?>

<?php endif; ?>