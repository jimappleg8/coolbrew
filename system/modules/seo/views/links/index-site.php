<body>

<script type="text/javascript">
<!--
var submitted = false;
function doSubmit()
{
   if (! submitted)
   {
      submitted = true;
      ProgressImg = document.getElementById('inprogress_img');
      document.getElementById("inprogress").style.display = "block";
      document.getElementById("index_submit").style.display = "none";
      setTimeout("ProgressImg.src = ProgressImg.src",100);
      return true;
   }
   else
   {
      return false;
   }
}
//-->
</script>

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

   <a class="admin" href="<?=site_url('links/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Index <?=$site['Domain'];?></h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('links/index_site/'.$site_id.'/'.$last_action.'/');?>">

<p class="blockintro">Enter the settings for the index. Defaults are probably OK.</p>
<div class="block">
   <dl>
      <dt>Start&nbsp;from:</dt>
      <dd><?=form_input(array('name'=>'RootURL', 'id'=>'RootURL', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->RootURL));?>
      <?=$this->validation->RootURL_error;?></dd>

      <dt>Max&nbsp;number&nbsp;of&nbsp;pages:</dt>
      <dd><?=form_input(array('name'=>'MaxPages', 'id'=>'MaxPages', 'maxlength'=>'255', 'size'=>'8', 'value'=>$this->validation->MaxPages));?> Enter "0" to index all pages in site.
      <?=$this->validation->MaxPages_error;?></dd>

      <dt>Webpage&nbsp;extensions:</dt>
      <dd><?=form_input(array('name'=>'Extensions', 'id'=>'Extensions', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->Extensions));?>
      <?=$this->validation->Extensions_error;?></dd>

      <dt style="height:42px;">www&nbsp;treatment:</dt>
      <dd><input type="radio" name="WwwTreatment" value="default" <?=$this->validation->set_radio('WwwTreatment', 'default');?> /> None
      <br /><input type="radio" name="WwwTreatment" value="strip" <?=$this->validation->set_radio('WwwTreatment', 'strip');?> /> Auto-strip 'www.'
      <br /><input type="radio" name="WwwTreatment" value="append" <?=$this->validation->set_radio('WwwTreatment', 'append');?> /> Auto-append 'www.'</dd>

      <dt style="height:42px;">Index&nbsp;treatment:</dt>
      <dd><input type="radio" name="IndexTreatment" value="default" <?=$this->validation->set_radio('IndexTreatment', 'default');?> /> None 
      <br /><input type="radio" name="IndexTreatment" value="strip" <?=$this->validation->set_radio('IndexTreatment', 'strip');?> /> Auto-strip 'index.*' 
      <br /><input type="radio" name="IndexTreatment" value="append" <?=$this->validation->set_radio('IndexTreatment', 'append');?> /> Auto-append
      <?=form_input(array('name'=>'IndexAppend', 'id'=>'IndexAppend', 'maxlength'=>'40', 'size'=>'8', 'value'=>$this->validation->IndexAppend));?>
      <?=$this->validation->IndexAppend_error;?></dd>

      <dt>Exclude&nbsp;from&nbsp;query:</dt>
      <dd><?=form_input(array('name'=>'QueryExcludes', 'id'=>'QueryExcludes', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->QueryExcludes));?>
      <?=$this->validation->QueryExcludes_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="ExternalTitles" id="ExternalTitles" value="1" <?=$this->validation->set_checkbox('ExternalTitles', '1');?> />  <span style="font-size:11px;"><label for="ExternalTitles">Extract titles from external webpages (time-consuming)</label></span>
      <?=$this->validation->ExternalTitles_error;?></dd>
   </dl>
</div>

<div class="action" id="index_submit" style="display:block;"><?=form_submit(array('name'=>'submit', 'id'=>'index_submit', 'class'=>'submit', 'value'=>'Index this site', 'onclick'=>'return doSubmit()'))?> or <a class="admin" href="<?=site_url('links/index/'.$site_id);?>">Cancel</a></div>
   
<div class="action clearfix" id="inprogress" style="display:none;"><div style="text-align:right; float:right;"><img id="inprogress_img" src="/images/admin/ajax-loader.gif" width="16" height="16" alt="" style="float:left; margin:2px 9px 0 0;" />Please Wait... or <a class="admin" href="<?=site_url('links/index/'.$site_id);?>">Cancel</a></div></div>

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