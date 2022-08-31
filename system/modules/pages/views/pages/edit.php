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

   <a class="admin" href="<?=site_url('pages/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Edit a Page</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('pages/edit/'.$site_id.'/'.$page_id.'/'.$last_action);?>">

<p class="blockintro">First, choose a short string ID for the page. This string is limited to characters, numbers and underlines or dashes. Be careful about changing this as it may be referenced in site pages and it could break how some of the pages work.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="PageName">Page Name:</label></dt>
      <dd><?=form_input(array('name'=>'PageName', 'id'=>'PageName', 'maxlength'=>'200', 'size'=>'30', 'value'=>$this->validation->PageName));?>
      <?=$this->validation->PageName_error;?></dd>
   </dl>
</div>

<p class="blockintro">Now, give us the basic link information:</p>
<div class="block">
   <dl>
      <dt class="required"><label for="MenuText">Menu Text:</label></dt>
      <dd><?=form_input(array('name'=>'MenuText', 'id'=>'MenuText', 'maxlength'=>'127', 'size'=>'30', 'value'=>$this->validation->MenuText));?>
      <?=$this->validation->MenuText_error;?></dd>
      <dt class="required"><label for="URL">URL:</label></dt>
      <dd><?=form_input(array('name'=>'URL', 'id'=>'URL', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->URL));?>
      <?=$this->validation->URL_error;?></dd>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="DisplayInMenu" id="DisplayInMenu" value="1" <?=$this->validation->set_checkbox('DisplayInMenu', '1');?> \>  Display this page in the menu.
      <?=$this->validation->DisplayInMenu_error;?></dd>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="ExternalLink" id="ExternalLink" value="1" <?=$this->validation->set_checkbox('ExternalLink', '1');?> \>  This URL is to an external website.
      <?=$this->validation->ExternalLink_error;?></dd>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="NewWindow" id="NewWindow" value="1" <?=$this->validation->set_checkbox('NewWindow', '1');?> \>  Open this page in a new window.
      <?=$this->validation->NewWindow_error;?></dd>
   </dl>
</div>

<div class="block">
   <dl>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="ProductCategory" id="ProductCategory" value="1" <?=$this->validation->set_checkbox('ProductCategory', '1');?> \><label for="ProductCategory">  This page is a product category page.</label>
      <?=$this->validation->ProductCategory_error;?></dd>
   </dl>
</div>

<h2>Page Content</h2>
<div class="block">
   <?=form_textarea(array('name'=>'Content', 'id'=>'Content', 'cols' => 80, 'rows' => 12, 'value'=>$this->validation->Content, 'class'=>'box'));?>
   <?=$this->validation->Content_error;?></dd>
</div>

<h2>Meta Data</h2>
<p class="blockintro">Enter the page's metadata for use by search engines.</p>
<div class="block">
   <dl>
      <dt>Page Title:</dt>
      <dd><?=form_input(array('name'=>'PageTitle', 'id'=>'PageTitle', 'maxlength'=>'255', 'size'=>'55', 'value'=>$this->validation->PageTitle));?>
      <?=$this->validation->PageTitle_error;?></dd>
      <dt>Meta Description:</dt>
      <dd><?=form_textarea(array('name'=>'MetaDescription', 'id'=>'MetaDescription', 'cols' => 55, 'rows' => 6, 'value'=>$this->validation->MetaDescription, 'class'=>'box'));?>
      <?=$this->validation->MetaDescription_error;?></dd>
      <dt>Meta Keywords:</dt>
      <dd><?=form_textarea(array('name'=>'MetaKeywords', 'id'=>'MetaKeywords', 'cols' => 55, 'rows' => 6, 'value'=>$this->validation->MetaKeywords, 'class'=>'box'));?>
      <?=$this->validation->MetaKeywords_error;?></dd>
      <dt>Meta Abstract:</dt>
      <dd><?=form_textarea(array('name'=>'MetaAbstract', 'id'=>'MetaAbstract', 'cols' => 55, 'rows' => 6, 'value'=>$this->validation->MetaAbstract, 'class'=>'box'));?>
      <?=$this->validation->MetaAbstract_error;?></dd>
      <dt>Meta Robots:</dt>
      <dd><?=form_input(array('name'=>'MetaRobots', 'id'=>'MetaRobots', 'maxlength'=>'127', 'size'=>'30', 'value'=>$this->validation->MetaRobots));?>
      <?=$this->validation->MetaRobots_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('pages/index/'.$site_id);?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

           &copy;2007 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
           
         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>