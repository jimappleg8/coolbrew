<?=$this->validation->error_string;?>

<? if ($error_msg != ''): ?>
<p class="error"><?=$error_msg;?></p>
<? endif; ?>

<form method="post" action="/login.php">

<?=form_hidden('return_url', $return_url);?>

<p style="margin:0;"><label for="Title">Username:</label>
<br /><?=form_input(array('name'=>'Username', 'id'=>'Username', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Username, 'style'=>'width:140px;'));?></p>

<p style="margin:6px 0 0 0;"><label for="Title">Password:</label>
<br /><?=form_password(array('name'=>'Password', 'id'=>'Password', 'maxlength'=>'255', 'size'=>'30', 'value'=>'', 'style'=>'width:140px;'));?></p>

<p style="margin:6px 0 18px 0;"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Sign in'))?></p>

</form>
