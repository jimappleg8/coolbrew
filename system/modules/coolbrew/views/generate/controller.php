&lt;?php

class <?=ucfirst($controller_name);?> extends Controller {

   function <?=ucfirst($controller_name);?>()
   {
      parent::Controller();   
   }

<?php if ($controller_methods != ''): ?>
<?=$controller_methods;?>
<?php else: ?>
   function index()
   {
      
   }
<?php endif; ?>

}
?&gt;