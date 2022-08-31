<body>

<?=$this->load->view('sites/tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert"><?=$admin['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

&nbsp;

               </div>   <!-- page_header_links -->

   <h1>Edit the site settings</h1>

            </div>   <!-- page_header -->

            <div class="innercol">
            
               <div id="basic-form">

<form method="post" action="<?=site_url('sites/settings/index/'.$site_id.'/'.$last_action);?>">

<div class="block">
   <dl>
      <dt><label for="Description">Description:</label></dt>
      <dd><?=form_textarea(array('name'=>'Description', 'id'=>'Description', 'cols' => 50, 'rows' => 3, 'value'=>$this->validation->Description, 'class'=>'box'));?>
      <?=$this->validation->Description_error;?></dd>

      <dt><label for="Region">Region:</label></dt>
      <dd><?=form_dropdown('Region', $regions, $this->validation->Region);?>
      <?=$this->validation->Region_error;?></dd>

      <dt><label for="Type">Site Type:</label></dt>
      <dd><?=form_dropdown('Type', $types, $this->validation->Type);?>
      <?=$this->validation->Type_error;?></dd>

      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>

      <dt><label for="RedirectSiteID">Redirect Site:</label></dt>
      <dd><?=form_dropdown('RedirectSiteID', $sites, $this->validation->RedirectSiteID);?>
      <?=$this->validation->RedirectSiteID_error;?></dd>
   </dl>
</div>

<div class="block">
   <dl>
      <dt><label for="LaunchDate">Launch date:</label></dt>
      <dd><?=form_input(array('name'=>'LaunchDate', 'id'=>'LaunchDate', 'maxlength'=>'127', 'size'=>'40', 'value'=>$this->validation->LaunchDate));?>
      <?=$this->validation->LaunchDate_error;?></dd>

      <dt><label for="DiscontinuedDate">Discontinued date:</label></dt>
      <dd><?=form_input(array('name'=>'DiscontinuedDate', 'id'=>'DiscontinuedDate', 'maxlength'=>'127', 'size'=>'40', 'value'=>$this->validation->DiscontinuedDate));?>
      <?=$this->validation->DiscontinuedDate_error;?></dd>
   </dl>
</div>

<div class="block">
   <dl>
      <dt><label for="DomainID">Primary domain:</label></dt>
      <dd><?=form_dropdown('DomainID', $domains, $this->validation->DomainID);?>
      <?=$this->validation->DomainID_error;?></dd>
   </dl>
</div>

<div class="block">
   <dl>
      <dt><label for="DevVendorURL">Vendor's Dev URL:</label></dt>
      <dd><?=form_input(array('name'=>'DevVendorURL', 'id'=>'DevVendorURL', 'maxlength'=>'127', 'size'=>'40', 'value'=>$this->validation->DevVendorURL));?>
      <?=$this->validation->DevVendorURL_error;?></dd>

      <dt><label for="DevVendorName">Vendor's Name:</label></dt>
      <dd><?=form_input(array('name'=>'DevVendorName', 'id'=>'DevVendorName', 'maxlength'=>'127', 'size'=>'20', 'value'=>$this->validation->DevVendorName));?>
      <?=$this->validation->DevVendorName_error;?></dd>

      <dt><label for="DevURL">Development URL:</label></dt>
      <dd><?=form_input(array('name'=>'DevURL', 'id'=>'DevURL', 'maxlength'=>'127', 'size'=>'40', 'value'=>$this->validation->DevURL));?>
      <?=$this->validation->DevURL_error;?></dd>

      <dt><label for="StageURL">Staging URL:</label></dt>
      <dd><?=form_input(array('name'=>'StageURL', 'id'=>'StageURL', 'maxlength'=>'127', 'size'=>'40', 'value'=>$this->validation->StageURL));?>
      <?=$this->validation->StageURL_error;?></dd>

      <dt><label for="LiveURL">Live URL:</label></dt>
      <dd><?=form_input(array('name'=>'LiveURL', 'id'=>'LiveURL', 'maxlength'=>'127', 'size'=>'40', 'value'=>$this->validation->LiveURL));?>
      <?=$this->validation->LiveURL_error;?></dd>
   </dl>
</div>

<p class="blockintro">If we don't have access to a repository, type 'none' into the field.</p>
<div class="block">
   <dl>
      <dt><label for="RepositoryURL">Repository URL:</label></dt>
      <dd><?=form_input(array('name'=>'RepositoryURL', 'id'=>'RepositoryURL', 'maxlength'=>'256', 'size'=>'70', 'value'=>$this->validation->RepositoryURL));?>
      <?=$this->validation->RepositoryURL_error;?></dd>
   </dl>
</div>

<p class="blockintro">Link patterns used along with the URLs above to correctly format links to this site from other HCG websites. Use {variable-name} to specify variables (e.g. {ProductID}). Include the initial slash.</p>
<div class="block">
   <dl>
      <dt><label for="ProductLink">Product Link:</label></dt>
      <dd><?=form_input(array('name'=>'ProductLink', 'id'=>'ProductLink', 'maxlength'=>'127', 'size'=>'70', 'value'=>$this->validation->ProductLink));?>
      <?=$this->validation->ProductLink_error;?></dd>

      <dt><label for="RecipeLink">Recipe Link:</label></dt>
      <dd><?=form_input(array('name'=>'RecipeLink', 'id'=>'RecipeLink', 'maxlength'=>'127', 'size'=>'70', 'value'=>$this->validation->RecipeLink));?>
      <?=$this->validation->RecipeLink_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?>
</div>

</form>

               </div>   <!-- basic-form -->
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007-<?=date('Y');?> The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
         <h2>Variable reference</h2>
         
         <p>The following variables, placed in the link patterns, will be replaced with the correct information:</p>
         
         <ul>
         <li>{ProductID}</li>
         <li>{ProductCode}</li>
         <li>{ProductCategoryID}
         <br /><samp>Ex: 579</samp></li>
         <li>{ProductCategoryCode}
         <br /><samp>Ex: gluten-free-flours</samp></li>
         <li>{ProductCategoryIDPath}
         <br /><samp>Ex: 62/63/579</samp></li>
         <li>{ProductCategoryCodePath}
         <br /><samp>Ex: baking/flours/gluten-free-flours</samp></li>
         <li>{RecipeID}</li>
         <li>{RecipeCode}</li>
         </ul>

         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>

