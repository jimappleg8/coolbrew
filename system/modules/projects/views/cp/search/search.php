<body>

<?=$this->load->view('cp/tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert"><?=$admin['message'];?></div>
<?php endif; ?>

<?php if ($admin['error_msg'] != ''): ?>
<div id="flash_error"><?=$admin['error_msg'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">

            <div class="page-header">

               <div class="page-header-links">

               </div>

   <h1>Search Projects</h1>

            </div>

            <div class="innercol">
    
               <div id="basic-form">

<form method="post" action="<?=site_url('cp/search/index/');?>">

<p class="blockintro">Search all Project records. The searched fields include the project title, site domain, story title, client, HEAT ticket number and story notes.</p>
<div class="block">
   <dl>
      <dt><label for="Words">Keywords:</label></dt>
      <dd><?=form_input(array('name'=>'Words', 'id'=>'Words', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->Words));?>
   <?=$this->validation->Words_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Search'))?>
</div>

</form>
               </div> <?php // basic-form ?>


<?php if ($admin['project_exists'] == true): ?>

   <h2 style="margin-bottom:2em;">Search Results</h2>
   
   <div id="searches">

   <div class="listing">
   
   <?php foreach($project_list AS $story): ?>
      <div class="item">
         <div class="project">
            <a href="<?=site_url('cp/stories/index/'.$story['ProjectID']);?>/"><span class="groupName"><?=$story['GroupName'];?></span> <?=$story['ProjectName'];?></a>
         </div>
      <?php if (isset($story['Description'])): ?>
         <div class="story clearfix">
            <div class="itemnum"><?=($story['HeatID'] != 0) ? $story['HeatID'].'-'.$story['HeatAssignment'] : '&nbsp;';?></div>
            <div class="description"><?php if ($admin['group'] == 'admin'): ?><a href="<?=site_url('cp/stories/edit/'.$story['ID'].'/'.$last_action);?>"><?php endif; ?><?=$story['Description'];?><?php if ($admin['group'] == 'admin'): ?></a><?php endif; ?></div>
            <div class="client"><?=($story['Client'] != '') ? $story['Client'] : '&nbsp;';?></div>
         </div> <?php // story ?>
      <?php endif; ?>
      </div>
   <?php endforeach; ?>
   
   <div style="border-top:1px solid #666; clear:both;"></div>

   </div> <?php // listing ?>
   
   </div> <?php // stories ?>

<?php elseif (isset($search)): ?>

   <h2>Search Results</h2>

   <p>No results were found.</p>

<?php endif; ?>

            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2010 The Hain Celestial Group, Inc.

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
