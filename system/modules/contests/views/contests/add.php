<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('contests/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Add a new contest</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('contests/add/'.$site_id.'/'.$last_action);?>">

<p class="blockintro">Choose a short string ID for this contest. This string is limited to characters, numbers and underlines or dashes. It and the language identifier are used to identify the contest on your website.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="ContestName">Contest Name:</label></dt>
      <dd><?=form_input(array('name'=>'ContestName', 'id'=>'ContestName', 'maxlength'=>'200', 'size'=>'30', 'value'=>$this->validation->ContestName));?>
      <?=$this->validation->ContestName_error;?></dd>

      <dt class="required"><label for="Language">Language:</label></dt>
      <dd><?=form_input(array('name'=>'Language', 'id'=>'Language', 'maxlength'=>'10', 'size'=>'10', 'value'=>$this->validation->Language));?>
      <?=$this->validation->Language_error;?></dd>
   </dl>
</div>

<h2>Contest Details</h2>
<div class="block">
   <dl>
      <dt><label for="ContestTitle">Contest Title:</label></dt>
      <dd><?=form_input(array('name'=>'ContestTitle', 'id'=>'ContestTitle', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->ContestTitle));?>
      <?=$this->validation->ContestTitle_error;?></dd>

      <dt><label for="StartDate">Start Date/Time:</label></dt>
      <dd><?=form_input(array('name'=>'StartDate', 'id'=>'StartDate', 'maxlength'=>'128', 'size'=>'25', 'value'=>$this->validation->StartDate));?> MST
      <?=$this->validation->StartDate_error;?></dd>

      <dt><label for="EndDate">End Date/Time:</label></dt>
      <dd><?=form_input(array('name'=>'EndDate', 'id'=>'EndDate', 'maxlength'=>'128', 'size'=>'25', 'value'=>$this->validation->EndDate));?>  MST
      <?=$this->validation->EndDate_error;?></dd>

      <dt><label for="EntryFrequency">Users can enter...</label></dt>
      <dd><?=form_dropdown('EntryFrequency', $frequencies, $this->validation->EntryFrequency);?>
      <?=$this->validation->EntryFrequency_error;?></dd>
   </dl>
</div>

<h2>Wrapper Template</h2>
<p class="blockintro">The contents of all the pages will be placed within this template where the {page_content} tag is. This is a way to define a header and footer.</p>
<div class="block">
      <p>Wrapper Template:
      <br /><?=form_textarea(array('name'=>'WrapperTemplate', 'id'=>'WrapperTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->WrapperTemplate, 'class'=>'box'));?>
      <?=$this->validation->WrapperTemplate_error;?></p>
</div>

<h2>Landing Page Template</h2>
<div class="block">
      <p>Landing Page Template:
      <br /><?=form_textarea(array('name'=>'LandingPageTemplate', 'id'=>'LandingPageTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->LandingPageTemplate, 'class'=>'box'));?>
      <?=$this->validation->LandingPageTemplate_error;?></p>

      <p><input type="checkbox" name="EntryIsLandingPage" id="EntryIsLandingPage" value="1" <?=$this->validation->set_checkbox('EntryIsLandingPage', '1');?> \><label for="EntryIsLandingPage">  Entry is the landing page.</label>
      <?=$this->validation->EntryIsLandingPage_error;?></p>
</div>

<h2>Entry Templates</h2>
<div class="block">
      <p>Entry Wrapper Template:
      <br /><?=form_textarea(array('name'=>'EntryWrapperTemplate', 'id'=>'EntryWrapperTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->EntryWrapperTemplate, 'class'=>'box'));?>
      <?=$this->validation->EntryWrapperTemplate_error;?></p>

      <p>Entry Form Template:
      <br /><?=form_textarea(array('name'=>'EntryFormTemplate', 'id'=>'EntryFormTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->EntryFormTemplate, 'class'=>'box'));?>
      <?=$this->validation->EntryFormTemplate_error;?></p>

      <p>Entry Success Template:
      <br /><?=form_textarea(array('name'=>'EntrySuccessTemplate', 'id'=>'EntrySuccessTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->EntrySuccessTemplate, 'class'=>'box'));?>
      <?=$this->validation->EntrySuccessTemplate_error;?></p>

      <p>Entry Rejected Template:
      <br /><?=form_textarea(array('name'=>'EntryRejectedTemplate', 'id'=>'EntryRejectedTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->EntryRejectedTemplate, 'class'=>'box'));?>
      <?=$this->validation->EntryRejectedTemplate_error;?></p>

      <p>Entry Closed Template:
      <br /><?=form_textarea(array('name'=>'EntryClosedTemplate', 'id'=>'EntryClosedTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->EntryClosedTemplate, 'class'=>'box'));?>
      <?=$this->validation->EntryClosedTemplate_error;?></p>

      <p>Entry Email Template:
      <br /><?=form_textarea(array('name'=>'EntryEmailTemplate', 'id'=>'EntryEmailTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->EntryEmailTemplate, 'class'=>'box'));?>
      <?=$this->validation->EntryEmailTemplate_error;?></p>
</div>

<h2>Rules</h2>
<div class="block">
      <p>Short Rules Template:
      <br /><?=form_textarea(array('name'=>'ShortRules', 'id'=>'ShortRules', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->ShortRules, 'class'=>'box'));?>
      <?=$this->validation->ShortRules_error;?></p>

      <p>Official Rules Template:
      <br /><?=form_textarea(array('name'=>'OfficialRules', 'id'=>'OfficialRules', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->OfficialRules, 'class'=>'box'));?>
      <?=$this->validation->OfficialRules_error;?></p>
</div>

<h2>Tell a Friend</h2>
<div class="block">
   <dl>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="TellAFriendIsEnabled" id="TellAFriendIsEnabled" value="1" <?=$this->validation->set_checkbox('TellAFriendIsEnabled', '1');?> \><label for="TellAFriendIsEnabled">  Enable Tell a Friend.</label>
      <?=$this->validation->TellAFriendIsEnabled_error;?></dd>

      <dt>If friend enters...</dt>
      <dd><input type="radio" name="FriendEntryAction" id="FriendEntryAction" value="none" <?=$this->validation->set_radio('FriendEntryAction', 'none');?> \> do nothing
      <br /><input type="radio" name="FriendEntryAction" id="FriendEntryAction" value="extras" <?=$this->validation->set_radio('FriendEntryAction', 'extras');?> \> give teller and extra entry</dd>
      
      <dt><label for="MaxExtraEntries">Maximum Extra Entries:</label></dt>
      <dd><?=form_input(array('name'=>'MaxExtraEntries', 'id'=>'MaxExtraEntries', 'maxlength'=>'6', 'size'=>'6', 'value'=>$this->validation->MaxExtraEntries));?>
      <?=$this->validation->MaxExtraEntries_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="SendTellerNotice" id="SendTellerNotice" value="1" <?=$this->validation->set_checkbox('SendTellerNotice', '1');?> \><label for="SendTellerNotice">  send email notice to teller.</label>
      <?=$this->validation->SendTellerNotice_error;?></dd>

      <p>Tell a Friend Wrapper Template:
      <br /><?=form_textarea(array('name'=>'TellAFriendWrapperTemplate', 'id'=>'TellAFriendWrapperTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->TellAFriendWrapperTemplate, 'class'=>'box'));?>
      <?=$this->validation->TellAFriendWrapperTemplate_error;?></p>

      <p>Tell a Friend Form Template:
      <br /><?=form_textarea(array('name'=>'TellAFriendFormTemplate', 'id'=>'TellAFriendFormTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->TellAFriendFormTemplate, 'class'=>'box'));?>
      <?=$this->validation->TellAFriendFormTemplate_error;?></p>

      <p>Tell a Friend Results Template:
      <br /><?=form_textarea(array('name'=>'TellAFriendResultsTemplate', 'id'=>'TellAFriendResultsTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->TellAFriendResultsTemplate, 'class'=>'box'));?>
      <?=$this->validation->TellAFriendResultsTemplate_error;?></p>

      <p>Tell a Friend Email Template:
      <br /><?=form_textarea(array('name'=>'TellAFriendEmailTemplate', 'id'=>'TellAFriendEmailTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->TellAFriendEmailTemplate, 'class'=>'box'));?>
      <?=$this->validation->TellAFriendEmailTemplate_error;?></p>
   </dl>
</div>

<h2>Quiz</h2>
<div class="block">
   <dl>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="QuizIsEnabled" id="QuizIsEnabled" value="1" <?=$this->validation->set_checkbox('QuizIsEnabled', '1');?> \><label for="QuizIsEnabled">  Enable Quiz.</label>
      <?=$this->validation->QuizIsEnabled_error;?></dd>

      <dt><label for="QuizID">Select a quiz:</label></dt>
      <dd><?=form_dropdown('QuizID', $quizes, $this->validation->QuizID);?>
      <?=$this->validation->QuizID_error;?></dd>

      <p>Quiz Results Template:
      <br /><?=form_textarea(array('name'=>'QuizResultsTemplate', 'id'=>'QuizResultsTemplate', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->QuizResultsTemplate, 'class'=>'box'));?>
      <?=$this->validation->QuizResultsTemplate_error;?></p>
   </dl>
</div>

<h2>Meta Data</h2>
<p class="blockintro">Enter the contest's metadata for use by search engines.</p>
<div class="block">
   <dl>
      <dt>Meta Title:</dt>
      <dd><?=form_input(array('name'=>'MetaTitle', 'id'=>'MetaTitle', 'maxlength'=>'255', 'size'=>'60', 'value'=>$this->validation->MetaTitle));?>
      <?=$this->validation->MetaTitle_error;?></dd>
      <dt>Meta Description:</dt>
      <dd><?=form_textarea(array('name'=>'MetaDescription', 'id'=>'MetaDescription', 'cols' => 60, 'rows' => 6, 'value'=>$this->validation->MetaDescription, 'class'=>'box'));?>
      <?=$this->validation->MetaDescription_error;?></dd>
      <dt>Meta Keywords:</dt>
      <dd><?=form_textarea(array('name'=>'MetaKeywords', 'id'=>'MetaKeywords', 'cols' => 60, 'rows' => 6, 'value'=>$this->validation->MetaKeywords, 'class'=>'box'));?>
      <?=$this->validation->MetaKeywords_error;?></dd>
      <dt>Meta Abstract:</dt>
      <dd><?=form_textarea(array('name'=>'MetaAbstract', 'id'=>'MetaAbstract', 'cols' => 60, 'rows' => 6, 'value'=>$this->validation->MetaAbstract, 'class'=>'box'));?>
      <?=$this->validation->MetaAbstract_error;?></dd>
      <dt>Meta Robots:</dt>
      <dd><?=form_input(array('name'=>'MetaRobots', 'id'=>'MetaRobots', 'maxlength'=>'127', 'size'=>'30', 'value'=>$this->validation->MetaRobots));?>
      <?=$this->validation->MetaRobots_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this contest'))?> or <a class="admin" href="<?=site_url('contests/index/'.$site_id);?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2008 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
         <h2>Variable reference</h2>
         
         <p>The following variables, placed in these templates, will be replaced with the correct information:</p>
         
         <h3>All Templates</h3>
         
         <ul>
         <li>{base_url}</li>
         <li>{base_uri}</li>
         <li>{error_message}</li>
         <li>{short_rules}</li>
         <li>{meta_title}</li>
         <li>{meta_description}</li>
         <li>{meta_keywords}</li>
         <li>{meta_abstract}</li>
         <li>{meta_robots}</li>
         <li>{landing_link}</li>
         <li>{entry_link}</li>
         <li>{rules_link}</li>
         </ul>
         
         <p><strong>NOTE:</strong> There are also mechanisms to include quizes and random list items in any of the templates. Quizes are defined in the local config file for the contest. List items are defined using the Lists module for this site.</p>
                    
         <h3>Wrapper Template</h3>
         
         <ul>
         <li>{page_content}</li>
         </ul>
                    
         <h3>Entry Wrapper Template</h3>
         
         <ul>
         <li>{entry_content}</li>
         </ul>
         
         <h3>Entry Form Template</h3>
         
         <ul>
         <li>{entry_form}</li>
         </ul>
         
         <h3>Email Template</h3>
         
         <ul>
         <li>{first_name}</li>
         <li>{last_name}</li>
         <li>{email}</li>
         </ul>
         
         <p><strong>NOTE:</strong> there is also a hook that you can use to further cusomize the content of the email.</p>

         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>