<body><?=$this->load->view('cp/tabs');?><div id="Wrapper">  <?php if ($admin['message'] != ''): ?><div id="flash_alert"><?=$admin['message'];?></div><?php endif; ?><?php if ($admin['error_msg'] != ''): ?><div id="flash_error"><?=$admin['error_msg'];?></div><?php endif; ?>   <div class="container">      <div class="Full">         <div class="col">            <div class="page-header">               <div class="page-header-links">               </div>   <h1>Vendor support projects:</h1>            </div>            <div class="innercol"><p>These projects are ones for which we play a support role.</p><?php $today = date('Y-m-d'); ?><?php if ($admin['project_exists'] == true): ?><div id="sprints">   <?php // list projects in priority order ?>   <?php $curr_project = 0; $first = TRUE; ?>      <div class="listing">      <div class="heading clearfix">         <div class="itemnum">Item #</div>         <div class="description">Description</div>         <div class="client">Client</div>         <div class="assigned">Assigned</div>         <div class="deadline">Deadline</div>         <div class="status">Status</div>      </div>   <?php foreach($story_list as $story): ?>      <?php $project = $project_list[$project_lookup[$story['ProjectID']]]; ?>      <?php if ($project['ID'] != $curr_project): ?>         <?php $curr_project = $project['ID']; ?>         <?php if ( ! $first): ?>                        </div> <?php // project ?>         <?php endif; ?>         <?php $first = FALSE; ?>                     <div class="project">         <div class="summary">                  <a href="<?=site_url('cp/stories/index/'.$project['ID']);?>/"><span class="projectName"><?=$project['GroupName'];?></span> <?=$project['ProjectName'];?></a>                  </div>  <?php // summary ?>               <?php endif; ?>                     <div class="story clearfix">            <div class="itemnum"><?=($story['HeatID'] != 0) ? $story['HeatID'].'-'.$story['HeatAssignment'] : '&nbsp;';?></div>            <div class="description"><?php if ($admin['group'] == 'admin'): ?><a  href="<?=site_url('cp/stories/edit/'.$story['ID'].'/'.$last_action);?>"><?php endif; ?><?=$story['Description'];?><?php if ($admin['group'] == 'admin'): ?></a><?php endif; ?></div>            <div class="client"><?=($story['Client'] != '') ? $story['Client'] : '&nbsp;';?></div>            <div class="assigned"><?=($story['Assigned'] != '') ? $story['Assigned'] : '&nbsp;';?></div>            <div class="deadline"><?=($story['Deadline'] != '0000-00-00') ? date('m/d/y', strtotime($story['Deadline'])) : 'none set';?></div>            <div class="status <?=strtolower(str_replace(' ', '-', $story['Status']));?>"><?=$story['Status'];?></div>         </div> <?php // story ?>               <?php endforeach; ?>            <?php if ( ! $first): ?>            </div> <?php // project ?>   <?php endif; ?>   </div> <?php // listing ?></div> <?php // sprints ?>   <?php else: ?>   <p>There are no projects to display.</p><?php endif; ?>            </div>   <!-- innercol -->         </div>   <!-- col -->         <div class="bottom">&nbsp;</div>         <div id="Footer">   &copy; 2011 The Hain Celestial Group, Inc.        </div>   <!-- Footer -->      </div>   <!-- Full -->   </div>   <!-- class="container" --></div>   <!-- Wrapper --></body>