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

   <a class="admin" href="<?=site_url('categories/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Edit this Recipe Category</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('categories/edit/'.$site_id.'/'.$cat_id.'/'.$last_action);?>">

<p class="blockintro">First, choose a short string ID for the category. This string is limited to characters, numbers and underlines or dashes, and will be used to identify the category in URLs.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="CategoryCode">Category Code:</label></dt>
      <dd><?=form_input(array('name'=>'CategoryCode', 'id'=>'CategoryCode', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->CategoryCode));?>
      <?=$this->validation->CategoryCode_error;?></dd>
   </dl>
</div>

<p class="blockintro">Now, give us the basic category information. The Category Text is the text displayed on the website.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="CategoryName">Category Name:</label></dt>
      <dd><?=form_input(array('name'=>'CategoryName', 'id'=>'CategoryName', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->CategoryName));?>
      <?=$this->validation->CategoryName_error;?></dd>

      <dt>Category Text:</dt>
      <dd><?=form_textarea(array('name'=>'CategoryText', 'id'=>'CategoryText', 'cols' => 50, 'rows' => 10, 'value'=>$this->validation->CategoryText, 'class'=>'box'));?>
      <?=$this->validation->CategoryText_error;?></dd>
   </dl>
</div>

<p class="blockintro">Technical details:</p>
<div class="block">
   <dl>
      <dt><label for="CategoryID">Category ID:</label></dt>
      <dd><p style="font-size:12px; padding:3px;"><?=$this->validation->ID;?></p></dd>

      <dt><label for="ParentID">Parent:</label></dt>
      <dd><?=form_dropdown('ParentID', $parents, $this->validation->ParentID);?>
      <?=$this->validation->ParentID_error;?></dd>

      <dt><label for="Sort">Sort Order:</label></dt>
      <dd><?=form_input(array('name'=>'Sort', 'id'=>'Sort', 'maxlength'=>'12', 'size'=>'10', 'value'=>$this->validation->Sort));?>
      <?=$this->validation->Sort_error;?></dd>

      <dt><label for="Language">Language:</label></dt>
      <dd><?=form_dropdown('Language', $languages, $this->validation->Language);?>
      <?=$this->validation->Language_error;?></dd>

      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>
   </dl>
</div>

<p class="blockintro">Category image</p>
<div class="block">
   <dl>
      <dt><label for="CategoryImage">Category image:</label></dt>
      <dd><iframe id="upload_target" name="upload_target" src="<?=site_url('uploads/upload_category_image/'.$site_id.'/'.$cat_id);?>" style="width:400px;height:150px;border:0px solid #fff;"></iframe></dd>

   </dl>
</div>

<p class="blockintro">Enter the page's metadata for use by search engines.</p>
<div class="block">
   <dl>
      <dt>Page Title:</dt>
      <dd><?=form_input(array('name'=>'MetaTitle', 'id'=>'MetaTitle', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->MetaTitle));?>
      <?=$this->validation->MetaTitle_error;?></dd>

      <dt>Meta Description:</dt>
      <dd><?=form_textarea(array('name'=>'MetaDescription', 'id'=>'MetaDescription', 'cols' => 50, 'rows' => 6, 'value'=>$this->validation->MetaDescription, 'class'=>'box'));?>
      <?=$this->validation->MetaDescription_error;?></dd>

      <dt>Meta Keywords:</dt>
      <dd><?=form_textarea(array('name'=>'MetaKeywords', 'id'=>'MetaKeywords', 'cols' => 50, 'rows' => 6, 'value'=>$this->validation->MetaKeywords, 'class'=>'box'));?>
      <?=$this->validation->MetaKeywords_error;?></dd>

      <dt>Meta Abstract:</dt>
      <dd><?=form_textarea(array('name'=>'MetaAbstract', 'id'=>'MetaAbstract', 'cols' => 50, 'rows' => 6, 'value'=>$this->validation->MetaAbstract, 'class'=>'box'));?>
      <?=$this->validation->MetaAbstract_error;?></dd>

      <dt>Meta Robots:</dt>
      <dd><?=form_textarea(array('name'=>'MetaRobots', 'id'=>'MetaRobots', 'cols' => 50, 'rows' => 6, 'value'=>$this->validation->MetaRobots, 'class'=>'box'));?>
      <?=$this->validation->MetaRobots_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('categories/index/'.$site_id);?>">Cancel</a>
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