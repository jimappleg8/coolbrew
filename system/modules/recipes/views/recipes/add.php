<body>

<?=$this->load->view('tabs');?>

<?php if ($recipes['message'] != ''): ?>
<div id="message">
<p><?=$recipes['message'];?></p>
</div>
<?php endif; ?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">
            

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('recipes/index/'.$site_id);?>">Cancel</a>

               </div>

   <h1 id="top">New Recipe Start Page</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('recipes/add/'.$site_id.'/'.$last_action);?>">

<h2 id="basic_information">Get Started</h2>
<p class="blockintro">Enter the recipe's title to get started.</p>
<div class="block">
   <dl>
      <dt><strong><label for="Title">Recipe Title:</label></strong></dt>
      <dd><?=form_input(array('name'=>'Title', 'id'=>'Title', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->Title));?>
      <?=$this->validation->Title_error;?></dd>

   </dl>
</div>
<h2 id="xml_information">Or paste in an XML document</h2>
<div class="block">
   <dl>
      <dt><strong><label for="XMLText">XML:</label></strong></dt>
      <dd><?=form_textarea(array('name'=>'XMLText', 'id'=>'XMLText', 'cols'=>'50', 'rows'=>'12', 'value'=>$this->validation->XMLText));?>
      <?=$this->validation->XMLText_error;?></dd>
   </dl>
</div>


<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save and continue'))?> or <a class="admin" href="<?=site_url('recipes/index/'.$site_id);?>">Cancel</a>
</div>

</form>

               </div> <?php /* basic-form */ ?>
   
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
         
         <strong>Sample XML:</strong>
  <pre>
  &lt;?xml version="1.0" encoding="UTF-8"?&gt;
  &lt;Recipe&gt;
    &lt;Title&gt;&lt;![CDATA[Title]]&gt;&lt;/Title&gt;
    &lt;PrepTime /&gt;
    &lt;CookTime /&gt;
    &lt;Yield&gt;&lt;/Yield&gt;
    &lt;Description /&gt;
    &lt;Citation /&gt;
    &lt;Ingredients&gt;&lt;![CDATA[
    ]]&gt;&lt;/Ingredients&gt;
    &lt;Directions&gt;&lt;![CDATA[
    ]]&gt;&lt;/Directions&gt;
    &lt;Keywords /&gt;
  &lt;/Recipe&gt;</pre>

           
         </div>   <?php /* col */ ?>

      </div>   <?php /* Right */ ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php /* container */ ?>

</div>   <?php /* Wrapper */ ?>

</body>