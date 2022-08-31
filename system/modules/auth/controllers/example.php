<?php
class Example extends Controller {

       function Example()
       {
            parent::Controller();
			 
            //thanks to this line of code you can protect this controller
            $this->freakauth_light->check();
       }
       
       //----------------------------------------------------------------------
       ##### index #####
		  function index()
		  {
		 
		  	echo '<h1>You can view this message because you logged in and are at least an user!</h1>';
		  }	
   
}
?>