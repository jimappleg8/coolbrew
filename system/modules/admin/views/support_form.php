
<div id="basic-form">

<form action="/support/submit-a-ticket.php" method="post" name="contactus" id="contactus">

<?=form_hidden('siteid', SITE_ID);?>

<p class="blockintro" style="margin-left:164px; color:#333;">Give us some information about who you are...</p>
<div class="block" style="background-color:transparent;">
   <dl>
      <dt class="required"><label for="FName">First name:</label></dt>
      <dd><?=form_input(array('name'=>'FName', 'id'=>'FName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->FName));?>
      <?=$this->validation->FName_error;?></dd>

      <dt class="required"><label for="LName">Last name:</label></dt>
      <dd><?=form_input(array('name'=>'LName', 'id'=>'LName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->LName));?>
      <?=$this->validation->LName_error;?></dd>

      <dt class="required"><label for="Phone">Work Phone:</label></dt>
      <dd><?=form_input(array('name'=>'Phone', 'id'=>'Phone', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->Phone));?>
      <?=$this->validation->Phone_error;?></dd>

      <dt class="required"><label for="Email">Email:</label></dt>
      <dd><?=form_input(array('name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email));?>
      <?=$this->validation->Email_error;?></dd>
   </dl>
</div>

<p class="blockintro" style="margin-left:164px; color:#333;">...and tell us specifically what requires our attention.</p>
<div class="block" style="background-color:transparent;">
   <dl>
      <dt class="required"><label for="Website">Website:</label></dt>
      <dd><?=form_dropdown('Website', $websites, $this->validation->Website);?>
      <?=$this->validation->Website_error;?></dd>

      <dt class="required"><label for="Issue">Issue:</label></dt>
      <dd><?=form_dropdown('Issue', $issues, $this->validation->Issue);?>
      <?=$this->validation->Issue_error;?></dd>

      <dt class="required"><label for="Comment">Message:</label></dt>
      <dd><?=form_textarea(array('name'=>'Comment', 'id'=>'Comment', 'cols' => 50, 'rows' => 15, 'wrap' => "virtual", 'value'=>$this->validation->Comment));?>
      <?=$this->validation->Comment_error;?></dd>
   </dl>
</div>

<div class="action" style="margin-bottom:30px;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Send message'))?>
</div>


</form>

</div>

