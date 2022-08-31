<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
<?php if ($products['message'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertgood_icon.gif) #E2F9E3 left no-repeat; clear:both; color:#060; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #9c9;"><?=$products['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">

            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a>

               </div>

   <h1 id="top"><span><a href="<?=site_url('products/edit/'.$site_id.'/'.$product_id.'/'.$last_action);?>">Product Info</a> | </span><strong>Nutrition Facts</strong><span> | <a href="<?=site_url('categories/assign/'.$site_id.'/'.$product_id.'/'.$last_action);?>">Categories</a></span>

            </div>

            <div class="innercol">

<?php if ($products['error_msg'] != ""): ?>
<div class="error">
ERROR: <?=$products['error_msg'];?>
</div>
<?php endif; ?>

<h1 style="margin-bottom:12px;"><?=$product['ProductName'];?></h1>

<form method="post" action="<?=site_url('nleas/edit/'.$site_id.'/'.$product_id.'/'.$last_action);?>">

<div id="nlea" class="clearfix">

<div style="border:1px solid #000; padding:6px; background:#CCC; margin-bottom:12px;">
   <label for="TYPE">Display Type:</label>
   <?=form_dropdown('TYPE', $types, $this->validation->TYPE);?>
   <?=$this->validation->TYPE_error;?>
</div>

<h1>Nutrition Facts</h1>

<div class="fact clearfix" style="border-width:0;">
   <div class="col1" style="width:150px; text-align:right;">Serving Size</div>
   <div class="colA" style="left:200px;"><?=form_input(array('name'=>'SSIZE', 'id'=>'SSIZE', 'maxlength'=>'255', 'style'=>'text-align:left; width:300px;', 'value'=>$this->validation->SSIZE));?>
   <?=$this->validation->SSIZE_error;?></div>
</div>

<div class="fact clearfix" style="border-width:0;">
   <div class="col1" style="width:150px; text-align:right;">Makes</div>
   <div class="colA" style="left:200px;"><?=form_input(array('name'=>'MAKE', 'id'=>'MAKE', 'maxlength'=>'255', 'style'=>'text-align:left; width:300px;', 'value'=>$this->validation->MAKE));?>
   <?=$this->validation->MAKE_error;?></div>
</div>

<div class="fact clearfix" style="border-width:0;">
   <div class="col1" style="width:150px; text-align:right;">Servings per Container</div>
   <div class="colA" style="left:200px;"><?=form_input(array('name'=>'SERV', 'id'=>'SERV', 'maxlength'=>'255', 'style'=>'text-align:left; width:300px;', 'value'=>$this->validation->SERV));?>
   <?=$this->validation->SERV_error;?></div>
</div>

<div class="fact clearfix" style="border-width:12px;">
   <div class="col1">Amount Per Serving</div>
   <div class="colA" style="left:260px;"><?=form_input(array('name'=>'COL1HD', 'id'=>'COL1HD', 'maxlength'=>'24', 'style'=>'width:100px;', 'value'=>$this->validation->COL1HD));?>
   <?=$this->validation->COL1HD_error;?></div>
   <div class="colB" style="left:440px;"><?=form_input(array('name'=>'COL2HD', 'id'=>'COL2HD', 'maxlength'=>'24', 'style'=>'width:100px;', 'value'=>$this->validation->COL2HD));?>
   <?=$this->validation->COL2HD_error;?></div>
</div>

<div class="fact clearfix">
   <div class="col1"><strong>Calories</strong></div>
   <div class="colA" style="left:270px;"><?=form_input(array('name'=>'CAL', 'id'=>'CAL', 'maxlength'=>'8', 'style'=>'width:80px;', 'value'=>$this->validation->CAL));?>
   <?=$this->validation->CAL_error;?></div>
   <div class="colB" style="left:450px;"><?=form_input(array('name'=>'CAL2', 'id'=>'CAL2', 'maxlength'=>'8', 'style'=>'width:80px;', 'value'=>$this->validation->CAL2));?>
   <?=$this->validation->CAL2_error;?></div>
</div>

<div class="fact clearfix" style="border-width:0;">
   <div class="col1"><strong>Calories from Fat</strong></div>
   <div class="colA" style="left:270px;"><?=form_input(array('name'=>'FATCAL', 'id'=>'FATCAL', 'maxlength'=>'8', 'style'=>'width:80px;', 'value'=>$this->validation->FATCAL));?>
   <?=$this->validation->FATCAL_error;?></div>
   <div class="colB" style="left:450px;"><?=form_input(array('name'=>'FATCAL2', 'id'=>'FATCAL2', 'maxlength'=>'8', 'style'=>'width:80px;', 'value'=>$this->validation->FATCAL2));?>
   <?=$this->validation->FATCAL2_error;?></div>
</div>

<div class="fact clearfix" style="border-width:6px;">
   <div class="col1" style="padding:9px 0 5px 0;">&nbsp;</div>
   <div class="col2" style="padding:9px 0 5px 0;">Quantity</div>
   <div class="col3" style="padding:9px 0 5px 0;">% Daily Value</div>
   <div class="col4" style="padding:9px 0 5px 0;">Quantity</div>
   <div class="col5" style="padding:9px 0 5px 0;">% Daily Value</div>
</div>

<div class="fact clearfix">
   <div class="col1"><strong>Total Fat</strong></div>
   <div class="col2"><?=form_input(array('name'=>'TFATQ', 'id'=>'TFATQ', 'maxlength'=>'8', 'value'=>$this->validation->TFATQ));?>
   <?=$this->validation->TFATQ_error;?></div>
   <div class="col3"><?=form_input(array('name'=>'TFATP', 'id'=>'TFATP', 'maxlength'=>'8', 'value'=>$this->validation->TFATP));?>
   <?=$this->validation->TFATP_error;?> %</div>
   <div class="col4"><?=form_input(array('name'=>'TFATQ2', 'id'=>'TFATQ2', 'maxlength'=>'8', 'value'=>$this->validation->TFATQ2));?>
   <?=$this->validation->TFATQ2_error;?></div>
   <div class="col5"><?=form_input(array('name'=>'TFATP2', 'id'=>'TFATP2', 'maxlength'=>'8', 'value'=>$this->validation->TFATP2));?>
   <?=$this->validation->TFATP2_error;?> %</div>
</div>

<div class="fact subnlea clearfix">
   <div class="col1">Saturated Fat</div>
   <div class="col2"><?=form_input(array('name'=>'SFATQ', 'id'=>'SFATQ', 'maxlength'=>'8', 'value'=>$this->validation->SFATQ));?>
   <?=$this->validation->SFATQ_error;?></div>
   <div class="col3"><?=form_input(array('name'=>'SFATP', 'id'=>'SFATP', 'maxlength'=>'8', 'value'=>$this->validation->SFATP));?>
   <?=$this->validation->SFATP_error;?> %</div>
   <div class="col4"><?=form_input(array('name'=>'SFATQ2', 'id'=>'SFATQ2', 'maxlength'=>'8', 'value'=>$this->validation->SFATQ2));?>
   <?=$this->validation->SFATQ2_error;?></div>
   <div class="col5"><?=form_input(array('name'=>'SFATP2', 'id'=>'SFATP2', 'maxlength'=>'8', 'value'=>$this->validation->SFATP2));?>
   <?=$this->validation->SFATP2_error;?> %</div>
</div>

<div class="fact subnlea clearfix">
   <div class="col1">Polyunsaturated Fat</div>
   <div class="col2"><?=form_input(array('name'=>'PFATQ', 'id'=>'PFATQ', 'maxlength'=>'8', 'value'=>$this->validation->PFATQ));?>
   <?=$this->validation->PFATQ_error;?></div>
   <div class="col4"><?=form_input(array('name'=>'PFATQ2', 'id'=>'PFATQ2', 'maxlength'=>'8', 'value'=>$this->validation->PFATQ2));?>
   <?=$this->validation->PFATQ2_error;?></div>
</div>

<div class="fact subnlea clearfix">
   <div class="col1">Monounsaturated Fat</div>
   <div class="col2"><?=form_input(array('name'=>'MFATQ', 'id'=>'MFATQ', 'maxlength'=>'8', 'value'=>$this->validation->MFATQ));?>
   <?=$this->validation->MFATQ_error;?></div>
   <div class="col4"><?=form_input(array('name'=>'MFATQ2', 'id'=>'MFATQ2', 'maxlength'=>'8', 'value'=>$this->validation->MFATQ2));?>
   <?=$this->validation->MFATQ2_error;?></div>
</div>

<div class="fact subnlea clearfix">
   <div class="col1"><i>Trans</i> Fat</div>
   <div class="col2"><?=form_input(array('name'=>'HFATQ', 'id'=>'HFATQ', 'maxlength'=>'8', 'value'=>$this->validation->HFATQ));?>
   <?=$this->validation->HFATQ_error;?></div>
   <div class="col4"><?=form_input(array('name'=>'HFATQ2', 'id'=>'HFATQ2', 'maxlength'=>'8', 'value'=>$this->validation->HFATQ2));?>
   <?=$this->validation->HFATQ2_error;?></div>
</div>

<div class="fact clearfix">
   <div class="col1"><strong>Cholesterol</strong></div>
   <div class="col2"><?=form_input(array('name'=>'CHOLQ', 'id'=>'CHOLQ', 'maxlength'=>'8', 'value'=>$this->validation->CHOLQ));?>
   <?=$this->validation->CHOLQ_error;?></div>
   <div class="col3"><?=form_input(array('name'=>'CHOLP', 'id'=>'CHOLP', 'maxlength'=>'8', 'value'=>$this->validation->CHOLP));?>
   <?=$this->validation->CHOLP_error;?> %</div>
   <div class="col4"><?=form_input(array('name'=>'CHOLQ2', 'id'=>'CHOLQ2', 'maxlength'=>'8', 'value'=>$this->validation->CHOLQ2));?>
   <?=$this->validation->CHOLQ2_error;?></div>
   <div class="col5"><?=form_input(array('name'=>'CHOLP2', 'id'=>'CHOLP2', 'maxlength'=>'8', 'value'=>$this->validation->CHOLP2));?>
   <?=$this->validation->CHOLP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1"><strong>Sodium</strong></div>
   <div class="col2"><?=form_input(array('name'=>'SODQ', 'id'=>'SODQ', 'maxlength'=>'8', 'value'=>$this->validation->SODQ));?>
   <?=$this->validation->SODQ_error;?></div>
   <div class="col3"><?=form_input(array('name'=>'SODP', 'id'=>'SODP', 'maxlength'=>'8', 'value'=>$this->validation->SODP));?>
   <?=$this->validation->SODP_error;?> %</div>
   <div class="col4"><?=form_input(array('name'=>'SODQ2', 'id'=>'SODQ2', 'maxlength'=>'8', 'value'=>$this->validation->SODQ2));?>
   <?=$this->validation->SODQ2_error;?></div>
   <div class="col5"><?=form_input(array('name'=>'SODP2', 'id'=>'SODP2', 'maxlength'=>'8', 'value'=>$this->validation->SODP2));?>
   <?=$this->validation->SODP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1"><strong>Potassium</strong></div>
   <div class="col2"><?=form_input(array('name'=>'POTQ', 'id'=>'POTQ', 'maxlength'=>'8', 'value'=>$this->validation->POTQ));?>
   <?=$this->validation->POTQ_error;?></div>
   <div class="col3"><?=form_input(array('name'=>'POTP', 'id'=>'POTP', 'maxlength'=>'8', 'value'=>$this->validation->POTP));?>
   <?=$this->validation->POTP_error;?> %</div>
   <div class="col4"><?=form_input(array('name'=>'POTQ2', 'id'=>'POTQ2', 'maxlength'=>'8', 'value'=>$this->validation->POTQ2));?>
   <?=$this->validation->POTQ2_error;?></div>
   <div class="col5"><?=form_input(array('name'=>'POTP2', 'id'=>'POTP2', 'maxlength'=>'8', 'value'=>$this->validation->POTP2));?>
   <?=$this->validation->POTP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1"><strong>Total Carbohydrate</strong></div>
   <div class="col2"><?=form_input(array('name'=>'TCARBQ', 'id'=>'TCARBQ', 'maxlength'=>'8', 'value'=>$this->validation->TCARBQ));?>
   <?=$this->validation->TCARBQ_error;?></div>
   <div class="col3"><?=form_input(array('name'=>'TCARBP', 'id'=>'TCARBP', 'maxlength'=>'8', 'value'=>$this->validation->TCARBP));?>
   <?=$this->validation->TCARBP_error;?> %</div>
   <div class="col4"><?=form_input(array('name'=>'TCARBQ2', 'id'=>'TCARBQ2', 'maxlength'=>'8', 'value'=>$this->validation->TCARBQ2));?>
   <?=$this->validation->TCARBQ2_error;?></div>
   <div class="col5"><?=form_input(array('name'=>'TCARBP2', 'id'=>'TCARBP2', 'maxlength'=>'8', 'value'=>$this->validation->TCARBP2));?>
   <?=$this->validation->TCARBP2_error;?> %</div>
</div>

<div class="fact subnlea clearfix">
   <div class="col1">Dietary Fiber</div>
   <div class="col2"><?=form_input(array('name'=>'DFIBQ', 'id'=>'DFIBQ', 'maxlength'=>'8', 'value'=>$this->validation->DFIBQ));?>
   <?=$this->validation->DFIBQ_error;?></div>
   <div class="col3"><?=form_input(array('name'=>'DFIBP', 'id'=>'DFIBP', 'maxlength'=>'8', 'value'=>$this->validation->DFIBP));?>
   <?=$this->validation->DFIBP_error;?> %</div>
   <div class="col4"><?=form_input(array('name'=>'DFIBQ2', 'id'=>'DFIBQ2', 'maxlength'=>'8', 'value'=>$this->validation->DFIBQ2));?>
   <?=$this->validation->DFIBQ2_error;?></div>
   <div class="col5"><?=form_input(array('name'=>'DFIBP2', 'id'=>'DFIBP2', 'maxlength'=>'8', 'value'=>$this->validation->DFIBP2));?>
   <?=$this->validation->DFIBP2_error;?> %</div>
</div>

<div class="fact subnlea clearfix">
   <div class="col1">Soluble Fiber</div>
   <div class="col2"><?=form_input(array('name'=>'SFIBQ', 'id'=>'SFIBQ', 'maxlength'=>'8', 'value'=>$this->validation->SFIBQ));?>
   <?=$this->validation->SFIBQ_error;?></div>
   <div class="col4"><?=form_input(array('name'=>'SFIBQ2', 'id'=>'SFIBQ2', 'maxlength'=>'8', 'value'=>$this->validation->SFIBQ2));?>
   <?=$this->validation->SFIBQ2_error;?></div>
</div>

<div class="fact subnlea clearfix">
   <div class="col1">Insoluble Fiber</div>
   <div class="col2"><?=form_input(array('name'=>'IFIBQ', 'id'=>'IFIBQ', 'maxlength'=>'8', 'value'=>$this->validation->IFIBQ));?>
   <?=$this->validation->IFIBQ_error;?></div>
   <div class="col4"><?=form_input(array('name'=>'IFIBQ2', 'id'=>'IFIBQ2', 'maxlength'=>'8', 'value'=>$this->validation->IFIBQ2));?>
   <?=$this->validation->IFIBQ2_error;?></div>
</div>

<div class="fact subnlea clearfix">
   <div class="col1">Sugar</div>
   <div class="col2"><?=form_input(array('name'=>'SUGQ', 'id'=>'SUGQ', 'maxlength'=>'8', 'value'=>$this->validation->SUGQ));?>
   <?=$this->validation->SUGQ_error;?></div>
   <div class="col4"><?=form_input(array('name'=>'SUGQ2', 'id'=>'SUGQ2', 'maxlength'=>'8', 'value'=>$this->validation->SUGQ2));?>
   <?=$this->validation->SUGQ2_error;?></div>
</div>

<div class="fact subnlea clearfix">
   <div class="col1">Other Carbohydrates</div>
   <div class="col2"><?=form_input(array('name'=>'OCARBQ', 'id'=>'OCARBQ', 'maxlength'=>'8', 'value'=>$this->validation->OCARBQ));?>
   <?=$this->validation->OCARBQ_error;?></div>
   <div class="col4"><?=form_input(array('name'=>'OCARBQ2', 'id'=>'OCARBQ2', 'maxlength'=>'8', 'value'=>$this->validation->OCARBQ2));?>
   <?=$this->validation->OCARBQ2_error;?></div>
</div>

<div class="fact clearfix">
   <div class="col1"><strong>Protein</strong></div>
   <div class="col2"><?=form_input(array('name'=>'PROTQ', 'id'=>'PROTQ', 'maxlength'=>'8', 'value'=>$this->validation->PROTQ));?>
   <?=$this->validation->PROTQ_error;?></div>
   <div class="col3"><?=form_input(array('name'=>'PROTP', 'id'=>'PROTP', 'maxlength'=>'8', 'value'=>$this->validation->PROTP));?>
   <?=$this->validation->PROTP_error;?> %</div>
   <div class="col4"><?=form_input(array('name'=>'PROTQ2', 'id'=>'PROTQ2', 'maxlength'=>'8', 'value'=>$this->validation->PROTQ2));?>
   <?=$this->validation->PROTQ2_error;?></div>
   <div class="col5"><?=form_input(array('name'=>'PROTP2', 'id'=>'PROTP2', 'maxlength'=>'8', 'value'=>$this->validation->PROTP2));?>
   <?=$this->validation->PROTP2_error;?> %</div>
</div>

<div class="fact clearfix" style="border-width:12px;">
   <div class="col1">Vitamin A</div>
   <div class="col3"><?=form_input(array('name'=>'VITAP', 'id'=>'VITAP', 'maxlength'=>'8', 'value'=>$this->validation->VITAP));?>
   <?=$this->validation->VITAP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'VITAP2', 'id'=>'VITAP2', 'maxlength'=>'8', 'value'=>$this->validation->VITAP2));?>
   <?=$this->validation->VITAP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Vitamin C</div>
   <div class="col2"><?=form_input(array('name'=>'VITCQ', 'id'=>'VITCQ', 'maxlength'=>'8', 'value'=>$this->validation->VITCQ));?>
   <?=$this->validation->VITCQ_error;?></div>
   <div class="col3"><?=form_input(array('name'=>'VITCP', 'id'=>'VITCP', 'maxlength'=>'8', 'value'=>$this->validation->VITCP));?>
   <?=$this->validation->VITCP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'VITCP2', 'id'=>'VITCP2', 'maxlength'=>'8', 'value'=>$this->validation->VITCP2));?>
   <?=$this->validation->VITCP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Calcium</div>
   <div class="col3"><?=form_input(array('name'=>'CALCP', 'id'=>'CALCP', 'maxlength'=>'8', 'value'=>$this->validation->CALCP));?>
   <?=$this->validation->CALCP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'CALCP2', 'id'=>'CALCP2', 'maxlength'=>'8', 'value'=>$this->validation->CALCP2));?>
   <?=$this->validation->CALCP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Iron</div>
   <div class="col3"><?=form_input(array('name'=>'IRONP', 'id'=>'IRONP', 'maxlength'=>'8', 'value'=>$this->validation->IRONP));?>
   <?=$this->validation->IRONP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'IRONP2', 'id'=>'IRONP2', 'maxlength'=>'8', 'value'=>$this->validation->IRONP2));?>
   <?=$this->validation->IRONP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Vitamin D</div>
   <div class="col3"><?=form_input(array('name'=>'VITDP', 'id'=>'VITDP', 'maxlength'=>'8', 'value'=>$this->validation->VITDP));?>
   <?=$this->validation->VITDP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'VITDP2', 'id'=>'VITDP2', 'maxlength'=>'8', 'value'=>$this->validation->VITDP2));?>
   <?=$this->validation->VITDP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Vitamin E</div>
   <div class="col3"><?=form_input(array('name'=>'VITEP', 'id'=>'VITEP', 'maxlength'=>'8', 'value'=>$this->validation->VITEP));?>
   <?=$this->validation->VITEP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'VITEP2', 'id'=>'VITEP2', 'maxlength'=>'8', 'value'=>$this->validation->VITEP2));?>
   <?=$this->validation->VITEP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Vitamin K</div>
   <div class="col3"><?=form_input(array('name'=>'VITKP', 'id'=>'VITKP', 'maxlength'=>'8', 'value'=>$this->validation->VITKP));?>
   <?=$this->validation->VITKP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'VITKP2', 'id'=>'VITKP2', 'maxlength'=>'8', 'value'=>$this->validation->VITKP2));?>
   <?=$this->validation->VITKP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Thiamin</div>
   <div class="col3"><?=form_input(array('name'=>'THIAP', 'id'=>'THIAP', 'maxlength'=>'8', 'value'=>$this->validation->THIAP));?>
   <?=$this->validation->THIAP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'THIAP2', 'id'=>'THIAP2', 'maxlength'=>'8', 'value'=>$this->validation->THIAP2));?>
   <?=$this->validation->THIAP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Riboflavin</div>
   <div class="col3"><?=form_input(array('name'=>'RIBOP', 'id'=>'RIBOP', 'maxlength'=>'8', 'value'=>$this->validation->RIBOP));?>
   <?=$this->validation->RIBOP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'RIBOP2', 'id'=>'RIBOP2', 'maxlength'=>'8', 'value'=>$this->validation->RIBOP2));?>
   <?=$this->validation->RIBOP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Niacin</div>
   <div class="col3"><?=form_input(array('name'=>'NIACP', 'id'=>'NIACP', 'maxlength'=>'8', 'value'=>$this->validation->NIACP));?>
   <?=$this->validation->NIACP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'NIACP2', 'id'=>'NIACP2', 'maxlength'=>'8', 'value'=>$this->validation->NIACP2));?>
   <?=$this->validation->NIACP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Vitamin B6</div>
   <div class="col3"><?=form_input(array('name'=>'VITB6P', 'id'=>'VITB6P', 'maxlength'=>'8', 'value'=>$this->validation->VITB6P));?>
   <?=$this->validation->VITB6P_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'VITB6P2', 'id'=>'VITB6P2', 'maxlength'=>'8', 'value'=>$this->validation->VITB6P2));?>
   <?=$this->validation->VITB6P2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Folic Acid</div>
   <div class="col3"><?=form_input(array('name'=>'FOLICP', 'id'=>'FOLICP', 'maxlength'=>'8', 'value'=>$this->validation->FOLICP));?>
   <?=$this->validation->FOLICP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'FOLICP2', 'id'=>'FOLICP2', 'maxlength'=>'8', 'value'=>$this->validation->FOLICP2));?>
   <?=$this->validation->FOLICP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Folate</div>
   <div class="col3"><?=form_input(array('name'=>'FOLATEP', 'id'=>'FOLATEP', 'maxlength'=>'8', 'value'=>$this->validation->FOLATEP));?>
   <?=$this->validation->FOLATEP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'FOLATEP2', 'id'=>'FOLATEP2', 'maxlength'=>'8', 'value'=>$this->validation->FOLATEP2));?>
   <?=$this->validation->FOLATEP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Chloride</div>
   <div class="col3"><?=form_input(array('name'=>'CHLORP', 'id'=>'CHLORP', 'maxlength'=>'8', 'value'=>$this->validation->CHLORP));?>
   <?=$this->validation->CHLORP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'CHLORP2', 'id'=>'CHLORP2', 'maxlength'=>'8', 'value'=>$this->validation->CHLORP2));?>
   <?=$this->validation->CHLORP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Vitamin B12</div>
   <div class="col3"><?=form_input(array('name'=>'VITB12P', 'id'=>'VITB12P', 'maxlength'=>'8', 'value'=>$this->validation->VITB12P));?>
   <?=$this->validation->VITB12P_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'VITB12P2', 'id'=>'VITB12P2', 'maxlength'=>'8', 'value'=>$this->validation->VITB12P2));?>
   <?=$this->validation->VITB12P2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Biotin</div>
   <div class="col3"><?=form_input(array('name'=>'BIOTINP', 'id'=>'BIOTINP', 'maxlength'=>'8', 'value'=>$this->validation->BIOTINP));?>
   <?=$this->validation->BIOTINP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'BIOTINP2', 'id'=>'BIOTINP2', 'maxlength'=>'8', 'value'=>$this->validation->BIOTINP2));?>
   <?=$this->validation->BIOTINP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Pantothenic Acid</div>
   <div class="col3"><?=form_input(array('name'=>'PACIDP', 'id'=>'PACIDP', 'maxlength'=>'8', 'value'=>$this->validation->PACIDP));?>
   <?=$this->validation->PACIDP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'PACIDP2', 'id'=>'PACIDP2', 'maxlength'=>'8', 'value'=>$this->validation->PACIDP2));?>
   <?=$this->validation->PACIDP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Phosphorus</div>
   <div class="col3"><?=form_input(array('name'=>'PHOSP', 'id'=>'PHOSP', 'maxlength'=>'8', 'value'=>$this->validation->PHOSP));?>
   <?=$this->validation->PHOSP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'PHOSP2', 'id'=>'PHOSP2', 'maxlength'=>'8', 'value'=>$this->validation->PHOSP2));?>
   <?=$this->validation->PHOSP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Iodine</div>
   <div class="col3"><?=form_input(array('name'=>'IODIP', 'id'=>'IODIP', 'maxlength'=>'8', 'value'=>$this->validation->IODIP));?>
   <?=$this->validation->IODIP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'IODIP2', 'id'=>'IODIP2', 'maxlength'=>'8', 'value'=>$this->validation->IODIP2));?>
   <?=$this->validation->IODIP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Magnesium</div>
   <div class="col3"><?=form_input(array('name'=>'MAGNP', 'id'=>'MAGNP', 'maxlength'=>'8', 'value'=>$this->validation->MAGNP));?>
   <?=$this->validation->MAGNP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'MAGNP2', 'id'=>'MAGNP2', 'maxlength'=>'8', 'value'=>$this->validation->MAGNP2));?>
   <?=$this->validation->MAGNP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Zinc</div>
   <div class="col3"><?=form_input(array('name'=>'ZINCP', 'id'=>'ZINCP', 'maxlength'=>'8', 'value'=>$this->validation->ZINCP));?>
   <?=$this->validation->ZINCP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'ZINCP2', 'id'=>'ZINCP2', 'maxlength'=>'8', 'value'=>$this->validation->ZINCP2));?>
   <?=$this->validation->ZINCP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Selenium</div>
   <div class="col3"><?=form_input(array('name'=>'SELEP', 'id'=>'SELEP', 'maxlength'=>'8', 'value'=>$this->validation->SELEP));?>
   <?=$this->validation->SELEP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'SELEP2', 'id'=>'SELEP2', 'maxlength'=>'8', 'value'=>$this->validation->SELEP2));?>
   <?=$this->validation->SELEP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Copper</div>
   <div class="col3"><?=form_input(array('name'=>'COPPP', 'id'=>'COPPP', 'maxlength'=>'8', 'value'=>$this->validation->COPPP));?>
   <?=$this->validation->COPPP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'COPPP2', 'id'=>'COPPP2', 'maxlength'=>'8', 'value'=>$this->validation->COPPP2));?>
   <?=$this->validation->COPPP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Manganese</div>
   <div class="col3"><?=form_input(array('name'=>'MANGP', 'id'=>'MANGP', 'maxlength'=>'8', 'value'=>$this->validation->MANGP));?>
   <?=$this->validation->MANGP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'MANGP2', 'id'=>'MANGP2', 'maxlength'=>'8', 'value'=>$this->validation->MANGP2));?>
   <?=$this->validation->MANGP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Chromium</div>
   <div class="col3"><?=form_input(array('name'=>'CHROMP', 'id'=>'CHROMP', 'maxlength'=>'8', 'value'=>$this->validation->CHROMP));?>
   <?=$this->validation->CHROMP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'CHROMP2', 'id'=>'CHROMP2', 'maxlength'=>'8', 'value'=>$this->validation->CHROMP2));?>
   <?=$this->validation->CHROMP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Molybdenum</div>
   <div class="col3"><?=form_input(array('name'=>'MOLYP', 'id'=>'MOLYP', 'maxlength'=>'8', 'value'=>$this->validation->MOLYP));?>
   <?=$this->validation->MOLYP_error;?> %</div>
   <div class="col5"><?=form_input(array('name'=>'MOLYP2', 'id'=>'MOLYP2', 'maxlength'=>'8', 'value'=>$this->validation->MOLYP2));?>
   <?=$this->validation->MOLYP2_error;?> %</div>
</div>

<div class="fact clearfix">
   <div class="col1">Not a significant source of...</div>
   <div class="col4"><?=form_dropdown('STMT1', $yesno, $this->validation->STMT1);?>
   <?=$this->validation->STMT1_error;?></div>
   <br style="clear:both;" />
   <div class="col4"><?=form_input(array('name'=>'STMT1Q', 'id'=>'STMT1Q', 'maxlength'=>'12', 'style'=>'width:100px;', 'value'=>$this->validation->STMT1Q));?>
   <?=$this->validation->STMT1Q_error;?></div>
   <br style="clear:both;" />
   <table cellspacing="0" cellpadding="6" border="0">
   <tr><td>
   a = saturated fat
   <br />b = cholesterol
   <br />c = dietary fiber
   </td>
   <td>
   d = sugars
   <br />e = Vitamin A
   <br />f = Vitamin C
   </td>
   <td>
   g = calcium
   <br />h = iron
   <br />i = protein
   </td></tr>
   </table>
</div>

<div class="fact clearfix" style="margin-top:6px;">
   <div class="col1">Prep Statement...</div>
   <div class="col4"><?=form_dropdown('STMT2', $yesno, $this->validation->STMT2);?>
   <?=$this->validation->STMT2_error;?></div>
   <br style="clear:both;" />
   <textarea name="STMT2Q" cols="60" rows="6"></textarea>
</div>

<div class="fact clearfix" style="margin-top:6px;">
   <div class="col1">Short version of % Daily Value statement</div>
   <div class="col4"><?=form_dropdown('PDV1', $yesno, $this->validation->PDV1);?>
   <?=$this->validation->PDV1_error;?></div>
</div>

<div class="fact clearfix" style="border-width:0;">
   <div class="col1">Long version of % Daily Value statement</div>
   <div class="col4"><?=form_dropdown('PDV2', $yesno, $this->validation->PDV2);?>
   <?=$this->validation->PDV2_error;?></div>
</div>

<div class="fact clearfix">
   <div class="col1">% Daily Value table</div>
   <div class="col4"><?=form_dropdown('PDVT', $yesno, $this->validation->PDVT);?>
   <?=$this->validation->PDVT_error;?></div>
</div>

</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a>
</div>

</form>

            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
         </div>   <?php // col ?>
         
      </div>   <?php // Right ?>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
