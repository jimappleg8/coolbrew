<?=$this->lang->line('FAL_welcome')?> <?=$user_name?>,

<?=$this->lang->line('FAL_activation_email_body_message')?>

{unwrap}<?=$activation_url?>{/unwrap}

<?=$this->lang->line('FAL_activation_login_instruction')?>


username: <?=$user_name?>

password: <?=$password?>


<?=$this->lang->line('FAL_activation_keep_data')?>


<?=$this->lang->line('FAL_citation_message')?>

--------------------------------------------------------
<?=$this->config->item('FAL_website_name');?> - <?=$this->config->item('base_url');?>