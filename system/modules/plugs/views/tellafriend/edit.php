<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertgood_icon.gif) #E2F9E3 left no-repeat; clear:both; color:#060; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #9c9;"><?=$admin['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('tellafriend/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Edit this tell-a-friend widget</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('tellafriend/edit/'.$site_id.'/'.$tell_id.'/'.$last_action);?>">

<p class="blockintro">Choose a short string ID for this widget. This string is limited to characters, numbers and underlines or dashes. It and the language identifier are used to identify the widget on your website.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="TellName">Widget Name:</label></dt>
      <dd><?=form_input(array('name'=>'TellName', 'id'=>'TellName', 'maxlength'=>'200', 'size'=>'30', 'value'=>$this->validation->TellName));?>
      <?=$this->validation->TellName_error;?></dd>

      <dt class="required"><label for="Language">Language:</label></dt>
      <dd><?=form_input(array('name'=>'Language', 'id'=>'Language', 'maxlength'=>'10', 'size'=>'10', 'value'=>$this->validation->Language));?>
      <?=$this->validation->Language_error;?></dd>
   </dl>
</div>

<h2>Tell about what?</h2>
<p class="blockintro">Indicate the URL that should be sent to the friend. If this widget is intended to work on any page, leave the URL blank and check the box indicating that we should use the referring page.</p>
<div class="block">
   <dl>
      <dt><label for="URL">URL:</label></dt>
      <dd><?=form_input(array('name'=>'URL', 'id'=>'URL', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->URL));?>
      <?=$this->validation->URL_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="UseSuppliedURL" id="UseSuppliedURL" value="1" <?=$this->validation->set_checkbox('UseSuppliedURL', '1');?> \><label for="SendSenderCopy"> Use the URL supplied by the referring page.</label>
      <?=$this->validation->UseSuppliedURL_error;?></dd>
   </dl>
</div>

<h2>Tell a Friend Form</h2>
<div class="block">
   <dl>
      <dt><label for="NumFriendFields">Friend fields to display:</label></dt>
      <dd><?=form_input(array('name'=>'NumFriendFields', 'id'=>'NumFriendFields', 'maxlength'=>'6', 'size'=>'6', 'value'=>$this->validation->NumFriendFields));?>
      <?=$this->validation->NumFriendFields_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="SendSenderCopy" id="SendSenderCopy" value="1" <?=$this->validation->set_checkbox('SendSenderCopy', '1');?> \><label for="SendSenderCopy"> Give the option to send a copy of the email to the sender.</label>
      <?=$this->validation->SendSenderCopy_error;?></dd>

      <p>Wrapper Template:
      <br /><?=form_fckeditor(array('name'=>'WrapperTemplate', 'id'=>'WrapperTemplate', 'toolbarset'=>'HCG', 'basepath'=>'/fckeditor/', 'width'=>'640', 'height'=>'200', 'value'=>$this->validation->WrapperTemplate, 'class'=>'box'));?>
      <?=$this->validation->WrapperTemplate_error;?></p>

      <p>Form Template:
      <br /><?=form_fckeditor(array('name'=>'FormTemplate', 'id'=>'FormTemplate', 'toolbarset'=>'HCG', 'basepath'=>'/fckeditor/', 'width'=>'640', 'height'=>'200', 'value'=>$this->validation->FormTemplate, 'class'=>'box'));?>
      <?=$this->validation->FormTemplate_error;?></p>

      <p>Results Template:
      <br /><?=form_fckeditor(array('name'=>'ResultsTemplate', 'id'=>'ResultsTemplate', 'toolbarset'=>'HCG', 'basepath'=>'/fckeditor/', 'width'=>'640', 'height'=>'200', 'value'=>$this->validation->ResultsTemplate, 'class'=>'box'));?>
      <?=$this->validation->ResultsTemplate_error;?></p>

      <p>Email Template:
      <br /><?=form_textarea(array('name'=>'EmailTemplate', 'id'=>'EmailTemplate', 'cols' => '80', 'rows' => '12', 'value'=>$this->validation->EmailTemplate, 'class'=>'box'));?>
      <?=$this->validation->EmailTemplate_error;?></p>
   </dl>
</div>

<h2>Privacy Policy</h2>
<div class="block">
      <p>Privacy Policy Template:
      <br /><?=form_fckeditor(array('name'=>'PrivacyPolicy', 'id'=>'PrivacyPolicy', 'toolbarset'=>'HCG', 'basepath'=>'/fckeditor/', 'width'=>'640', 'height'=>'200', 'value'=>$this->validation->PrivacyPolicy, 'class'=>'box'));?>
      <?=$this->validation->PrivacyPolicy_error;?></p>
</div>

<h2>Meta Data</h2>
<p class="blockintro">Enter the widget's metadata for use by search engines.</p>
<div class="block">
   <dl>
      <dt>Meta Title:</dt>
      <dd><?=form_input(array('name'=>'MetaTitle', 'id'=>'MetaTitle', 'maxlength'=>'255', 'size'=>'60', 'value'=>$this->validation->MetaTitle));?>
      <?=$this->validation->MetaTitle_error;?></dd>

      <dt>Meta Description:</dt>
      <dd><?=form_textarea(array('name'=>'MetaDescription', 'id'=>'MetaDescription', 'cols' => '55', 'rows' => '6', 'value'=>$this->validation->MetaDescription, 'class'=>'box'));?>
      <?=$this->validation->MetaDescription_error;?></dd>

      <dt>Meta Keywords:</dt>
      <dd><?=form_textarea(array('name'=>'MetaKeywords', 'id'=>'MetaKeywords', 'cols' => '55', 'rows' => '6', 'value'=>$this->validation->MetaKeywords, 'class'=>'box'));?>
      <?=$this->validation->MetaKeywords_error;?></dd>

      <dt>Meta Abstract:</dt>
      <dd><?=form_textarea(array('name'=>'MetaAbstract', 'id'=>'MetaAbstract', 'cols' => '55', 'rows' => '6', 'value'=>$this->validation->MetaAbstract, 'class'=>'box'));?>
      <?=$this->validation->MetaAbstract_error;?></dd>

      <dt>Meta Robots:</dt>
      <dd><?=form_input(array('name'=>'MetaRobots', 'id'=>'MetaRobots', 'maxlength'=>'127', 'size'=>'30', 'value'=>$this->validation->MetaRobots));?>
      <?=$this->validation->MetaRobots_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('tellafriend/index/'.$site_id);?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2009 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
         
<h2>Template Variables</h2>

<p style="margin-bottom:0;"><strong>Available to all templates</strong></p>

<p style="padding-left:1em; margin-top:0.6em;">{base_uri}
<br />{base_url}
<br />{css}
<br />{error_message}
<br />{landing_link}
<br />{meta_abstract}
<br />{meta_description}
<br />{meta_keywords}
<br />{meta_robots}
<br />{meta_title}
<br />{num_friends}
<br />{offer_sender_copy}
<br />{privacy_link}
<br />{privacy_policy}</p>

<p style="margin-bottom:0;"><strong>Available in wrapper template</strong></p>

<p style="padding-left:1em; margin-top:0.6em;">{page_content}</p>


<p style="margin-bottom:0;"><strong>Available in form template</strong></p>

<p style="padding-left:1em; margin-top:0.6em;">{tell_a_friend_form}</p>


<p style="margin-bottom:0;"><strong>Available in email template</strong></p>

<p style="padding-left:1em; margin-top:0.6em;">{copy_of}
<br />{friend_email}
<br />{friend_first_name}
<br />{friend_last_name}
<br />{message}
<br />{sender_email}
<br />{sender_first_name}
<br />{sender_last_name}
<br />{url}
</p>

       
         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>