<body>

<?=$this->load->view('cp/tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('cp/sites/index');?>">Cancel</a>

               </div>
               
   <h1>Add a Site</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/sites/add/'.$last_action);?>">

<p class="blockintro">First, choose a short string ID for the site. This identifies the site in many places, so it is good to make it short but memorable.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="ID">Site ID:</label></dt>
      <dd><?=form_input(array('name'=>'ID', 'id'=>'ID', 'maxlength'=>'20', 'size'=>'10', 'value'=>$this->validation->ID));?>
      <?=$this->validation->ID_error;?></dd>
   </dl>
</div>

<p class="blockintro">Next, give some details about the site. These help identify the site in lists and the brand name is used to give a common name to the site in e-mails, etc.</p>
<div class="block">
   <p>Description:</p>
   <p><?=form_textarea(array('name'=>'Description', 'id'=>'Description', 'cols' => 60, 'rows' => 10, 'value'=>$this->validation->Description, 'class'=>'box'));?>
   <?=$this->validation->Description_error;?></p>
</div>

<p class="blockintro">Now, associate a BRAND with this site. You will be able to add other brands later if needed.</p>
<div class="block">
<table>
<tr>
<td><label for="OldBrandID">Choose an existing brand</label></td>
<td><label for="NewBrandName"><strong>OR</strong> Enter a new brand name</label></td>
</tr>
<tr>
<td><?=form_dropdown('OldBrandID', $brands, $this->validation->OldBrandID);?>
      <?=$this->validation->OldBrandID_error;?></td>
<td><?=form_input(array('name'=>'NewBrandName', 'id'=>'NewBrandName', 'maxlength'=>'64', 'size'=>'30', 'value'=>$this->validation->NewBrandName));?>
      <?=$this->validation->NewBrandName_error;?></td>
</tr>
</table>
</div>

<p class="blockintro">Lastly, assign the PRIMARY DOMAIN associated with this site. You will be able to add other domains later if needed. Note that if a domain is already the primary domain for another site, it will not appear in this list.</p>
<div class="block">
<table>
<tr>
<td><label for="OldBrandID">Choose an existing domain</label></td>
<td><label for="NewBrandName"><strong>OR</strong> Enter a new domain name</label></td>
</tr>
<tr>
<td><?=form_dropdown('OldDomainID', $domains, $this->validation->OldDomainID);?>
      <?=$this->validation->OldDomainID_error;?></td>
<td><?=form_input(array('name'=>'NewDomain', 'id'=>'NewDomain', 'maxlength'=>'64', 'size'=>'30', 'value'=>$this->validation->NewDomain));?>
      <?=$this->validation->NewDomain_error;?></td>
</tr>
</table>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this site'))?> or <a class="admin" href="<?=site_url('cp/sites/index');?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
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
            
            <p>&nbsp;</p>
  
            <div class="indent">

               <p>&nbsp;</p>
        
            </div>   <!-- indent -->

         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>