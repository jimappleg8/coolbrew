<style type="text/css">

#contact-us {
  font-size: 12px;
  line-height: 16px;
  font-weight: normal;
  margin-right:72px;
}

#contact-us h2 {
  font-size: 16px;
  line-height: 18px;
  font-weight: bold;
  border-bottom: 1px solid #666;
  padding-bottom: 6px;
  margin-top: 1.5em;
}

#contact-us table {
  margin-bottom: 0;
  border: 0;
}

#contact-us table td {
  vertical-align: top;
}

#contact-us table td.label {
  text-align: right;
}
#contact-us table td.field {
  text-align: left;
}

#contact-us .required {
  font-size: 0.8em;
  color: #F00;
}

#contact-us .directions {
  font-size: 0.9em;
  color: #666;
  padding-left: 6px;
}

#contact-us .error {
  display: inline;
  font-size: 0.8em;
  color: #F00;
  background-color: #FF0;
  border: 1px solid #F00;
  padding: 1px 6px;
  margin-left: 6px;
}

</style>

<?php if ($form_part == 1): ?>

   <!-- This is supposed to be the shorter "start" form. It is not implemented in this template. -->

<?php elseif ($form_part == 2): ?>

   <div id="contact-us">

   <form action="<?=$action;?>" method="post" name="contactus" id="contactus">

   <?=form_hidden('siteid', SITE_ID);?>
   <?=form_hidden('form_token', $this->validation->form_token);?>

   <table>

      <tr>
      <td>&nbsp;</td>
      <td colspan="2"><h2>Your Information</h2></td>
      </tr>

      <tr>
      <td class="label"><label for="FName"><?=$labels['FName'];?>:</label><span class="required">*</span></td>
      <td colspan="2" class="field">
      <?=form_input(array('name'=>'FName', 'id'=>'FName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->FName));?>
      <?=$this->validation->FName_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="LName"><?=$labels['LName'];?>:</label><span class="required">*</span></td>
      <td colspan="2" class="field">
      <?=form_input(array('name'=>'LName', 'id'=>'LName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->LName));?>
      <?=$this->validation->LName_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="Address1"><?=$labels['Address1'];?>:</label><span class="required">*</span></td>
      <td colspan="2" class="field">
      <?=form_input(array('name'=>'Address1', 'id'=>'Address1', 'maxlength'=>'40', 'size'=>'35', 'value'=>$this->validation->Address1));?>
      <?=$this->validation->Address1_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="Address2"><?=$labels['Address2'];?>:</label></td>
      <td colspan="2" class="field">
      <?=form_input(array('name'=>'Address2', 'id'=>'Address2', 'maxlength'=>'40', 'size'=>'35', 'value'=>$this->validation->Address2));?>
      <?=$this->validation->Address2_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="City"><?=$labels['City'];?>:</label><span class="required">*</span></td>
      <td colspan="2" class="field">
      <?=form_input(array('name'=>'City', 'id'=>'City', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->City));?>
      <?=$this->validation->City_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="State"><?=$labels['State'];?>:</label><span class="required">*</span></td>
      <td colspan="2" class="field">
      <?=form_dropdown('State', $states, $this->validation->State);?>
      <?=$this->validation->State_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="Zip"><?=$labels['Zip'];?>:</label><span class="required">*</span></td>
      <td colspan="2" class="field">
      <?=form_input(array('name'=>'Zip', 'id'=>'Zip', 'maxlength'=>'10', 'size'=>'10', 'value'=>$this->validation->Zip));?>
      <?=$this->validation->Zip_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="Country"><?=$labels['Country'];?>:</label></td>
      <td colspan="2" class="field">
      <?=form_dropdown('Country', $countries, $this->validation->Country);?>
      <?=$this->validation->Country_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="Phone"><?=$labels['Phone'];?>:</label></td>
      <td colspan="2" class="field">
      <?=form_input(array('name'=>'Phone', 'id'=>'Phone', 'maxlength'=>'14', 'size'=>'14', 'value'=>$this->validation->Phone));?>
      <?=$this->validation->Phone_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="Email"><?=$labels['Email'];?>:</label><span class="required">*</span></td>
      <td colspan="2" class="field">
      <?=form_input(array('name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email));?>
      <?=$this->validation->Email_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="Email2"><?=$labels['Email2'];?>:</label><span class="required">*</span></td>
      <td colspan="2" class="field">
      <?=form_input(array('name'=>'Email2', 'id'=>'Email2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email2));?>
      <?=$this->validation->Email2_error;?></td>
      </tr>

      <tr>
      <td>&nbsp;</td>
      <td colspan="2"><h2>Product Information</h2>
      <p>If you are contacting us about a product that you purchased, please provide the product information below so we can better help you.</p></td>
      </tr>

      <tr>
      <td class="label"><label for="ProductUPC"><?=$labels['ProductUPC'];?>:</label></td>
      <td class="directions"><?=$labels['ProductUPCDesc'];?></td>
      <td rowspan="2"><img src="http://resources.hcgweb.net/shared/mailform/upc-example.gif" width="200" height="50" alt=""></td>
      </tr>

      <tr>
      <td class="label">&nbsp;</td>
      <td class="field">
      <?=form_input(array('name'=>'ProductUPC', 'id'=>'ProductUPC', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->ProductUPC));?>
      <?=$this->validation->ProductUPC_error;?></td>
      </tr>

      <tr>
      <td class="label"><label for="BestByDateLotCode"><?=$labels['BestByDateLotCode'];?>:</label></td>
      <td colspan="2" class="directions"><?=$labels['BestByDateLotCodeDesc'];?></td>
      </tr>

      <tr>
      <td class="label">&nbsp;</td>
      <td colspan="2" class="field">
      <?=form_input(array('name'=>'BestByDateLotCode', 'id'=>'BestByDateLotCode', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->BestByDateLotCode));?>
      <?=$this->validation->BestByDateLotCode_error;?></td>
      </tr>

      <tr>
      <td>&nbsp;</td>
      <td colspan="2"><h2>What's On Your Mind?</h2>
      </tr>

      <tr>
      <td class="label"><label for="Comment"><?=$labels['Comment'];?>:</label><span class="required">*</span></td>
      <td colspan="2" class="field">
      <?=form_textarea(array('name'=>'Comment', 'id'=>'Comment', 'cols' => 60, 'rows' => 6, 'wrap' => "virtual", 'value'=>$this->validation->Comment));?>
      <?=$this->validation->Comment_error;?></td>
      </tr>

   <?php if ($marketing == TRUE || $release == TRUE): ?>
      <tr>
      <td>&nbsp;</td>
      <td colspan="2"><h2>Your Preferences</h2></td>
      </tr>
   <?php endif; ?>
      
   <?php if ($marketing == TRUE): ?>
      <tr>
      <td class="label">&nbsp;</td>
      <td colspan="2" class="field"><input type="checkbox" class="checkbox" value="YES" name="Marketing" id="Marketing"<?=$this->validation->set_checkbox('Marketing','YES');?> /> <label for="Marketing"><?php if ($labels['Marketing'] == '##default##'): ?>Would you like to receive information from <?=$hcg_site;?> in the future? If yes, leave this box checked.<?php if ($privacy != ''): ?> If you have any concerns, please read our <a href="<?=$privacy;?>">privacy policy</a>.<?php endif; ?><?php else: ?><?=$labels['Marketing'];?><?php endif; ?></label></td>
      </tr>
   <?php else: ?>
         <?=form_hidden('Marketing', 0);?>
   <?php endif; ?>

   <?php if ($release == TRUE): ?>
      <tr>
      <td class="label">&nbsp;</td>
      <td colspan="2" class="field"><input type="checkbox" class="checkbox" value="YES" name="Release" id="Release"<?=$this->validation->set_checkbox('Release','YES');?> /> <label for="Release"><?=$labels['Release'];?></label>
      </td>
      </tr>
   <?php else: ?>
         <?=form_hidden('Release', 0);?>
   <?php endif; ?>

      <tr>
      <td colspan="3">&nbsp;</td>
      </tr>

      <tr>
      <td class="label">&nbsp;</td>
      <td colspan="2" class="field"><?=form_submit(array('name'=>'submit', 'class'=>'button', 'value'=>$labels['SubmitText']))?></td>
      </tr>

      <tr>
      <td width="<?=$left_column;?>"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="<?=$left_column;?>" height="1" alt=""></td>
      <td width="300"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="300" height="1" alt=""></td>
      <td width="100%"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="100" height="1" alt=""></td>
      </tr>

   </table>
   </form>
   </div>

<?php elseif ($form_part == 3): ?>

   <div id="contact-us">

   <h3>Your message has been sent!</h3>

   <p><a href="<?=$home_link;?>">Return to the home page.</a></p>

   </div>   <?php /* contact-us */ ?>

<?php endif; ?>
