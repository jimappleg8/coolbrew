<div class="faq-search">

<form method="post" action="<?=$action;?>" name="faq" id="faq">

<?=form_input(array('name'=>'Words', 'id'=>'Words', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->Words));?>
<?=$this->validation->Words_error;?>

<input type="submit" name="faqSearch" id="faqSearch" value="Search">


</form>

</div>

<?=$popular_searches;?>