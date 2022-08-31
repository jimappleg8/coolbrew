<?php

class Tickets_Tags extends Controller {

   function Tickets_Tags()
   {
      parent::Controller();   
   }
   
   // ------------------------------------------------------------------------
   
   /**
    * Display form to submit a new HEAT ticket via e-mail
    *
    */
   function new_ticket()
   {
   
   }

   // ------------------------------------------------------------------------
   
   /**
    * Process the new_ticket form
    *
    */
   function _new_ticket()
   {
   
   }

   // ------------------------------------------------------------------------
   
   /**
    * Generate a list of tickets for a particular SiteID or CustomerID
    *
    */
   function list_tickets()
   {
   
   }

   // ------------------------------------------------------------------------
   
   /**
    *
    *
    */
   function view_ticket($ticket_id = NULL)
   {
      if ($ticket_id == NULL)
      {
         // display form to have them enter the number
      }
   
   }

   // ------------------------------------------------------------------------
   
   /**
    * Display a form to submit a journal entry for an existing ticket
    *
    */
   function edit_ticket($ticket_id = NULL)
   {
      if ($ticket_id == NULL)
      {
         // display field to enter the number
      }
   }

   // ------------------------------------------------------------------------
   
   /**
    * Process the edit_ticket form
    *
    */
   function edit_ticket($ticket_id = NULL)
   {
      if ($ticket_id == NULL)
      {
         // display field to enter the number
      }
   }

   // ------------------------------------------------------------------------
   
   /**
    * Return the total number of open tickets and the number of tickets
    * closed in the last X days.
    *
    */
   function total_tickets()
   {
   
   }


}
?>