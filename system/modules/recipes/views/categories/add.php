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
               
   <h1>Add a Recipe Category</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('categories/add/'.$site_id.'/'.$parent.'/'.$sort.'/'.$last_action);?>">

<h2>Get Started</h2>
<p class="blockintro">Enter the category name to get started.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="CategoryName">Category Name:</label></dt>
      <dd><?=form_input(array('name'=>'CategoryName', 'id'=>'CategoryName', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->CategoryName));?>
      <?=$this->validation->CategoryName_error;?></dd>

</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save and continue'))?> or <a class="admin" href="<?=site_url('categories/index/'.$site_id);?>">Cancel</a>
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