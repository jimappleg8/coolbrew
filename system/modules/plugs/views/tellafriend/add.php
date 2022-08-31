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

   <a class="admin" href="<?=site_url('tellafriend/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Add a new tell-a-friend widget</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('tellafriend/add/'.$site_id.'/'.$last_action);?>">

<p class="blockintro">Choose a short string ID for this widget. This string is limited to characters, numbers and underlines or dashes. It and the language identifier are used to identify the widget on your website.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="TellName">Widget Name:</label></dt>
      <dd><?=form_input(array('name'=>'TellName', 'id'=>'TellName', 'maxlength'=>'200', 'size'=>'30', 'value'=>$this->validation->TellName));?>
      <?=$this->validation->TellName_error;?></dd>

      <dt class="required"><label for="Language">Language:</label></dt>
      <dd><?=form_input(array('name'=>'Language', 'id'=>'Language', 'maxlength'=>'10', 'size'=>'10', 'value'=>$this->validation->Language));?>
      <?=$this->validation->Language_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Continue'))?> or <a class="admin" href="<?=site_url('tellafriend/index/'.$site_id);?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2009 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>