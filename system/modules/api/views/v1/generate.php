<?php if ($display_response == FALSE): ?>

   <div id="sign-up">
     <div id="sign-up-inner">
   
   <form action="<?=$action;?>" method="post" class="horizontal">
      <fieldset>
      
      <div class="field">
         <?=form_hidden('siteid', SITE_ID);?>
      </div>

      <div class="field required">
         <label for="URL">URL:</label><br />
         <?=form_input(array('class'=>'text', 'name'=>'URL', 'id'=>'URL', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->URL));?>
         <?=$this->validation->URL_error;?>
      </div>

      <div class="field required">
         <label for="ServerLevel">Server Level:</label><br />
         <?=form_dropdown('ServerLevel', $server_levels, $this->validation->ServerLevel);?>
         <?=$this->validation->ServerLevel_error;?>
      </div>

      <div class="buttons clear">
         <?=form_submit(array('name'=>'submit', 'class'=>'button', 'value'=>'Generate key'))?>
      </div>

   </fieldset>

   </form>

      </div>   <?php // sign-up ?>
   </div>   <?php // sign-up-inner ?>

<?php else: ?>

<p>Thank you for applying for a key. Here is the information that you need to know:</p>

<p>Key:
<pre style="padding:9px; border:1px solid #666; background-color:white;"><?=$key['APIKey'];?></pre></p>

<p>Valid URL:
<pre style="padding:9px; border:1px solid #666; background-color:white;"><?=$key['ValidURL'];?></pre></p>

<p>Server Level:
<pre style="padding:9px; border:1px solid #666; background-color:white;"><?=$key['ServerLevel'];?></pre></p>

<?php endif; ?>