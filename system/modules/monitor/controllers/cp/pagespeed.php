<?php

class Pagespeed extends Controller {

   var $sites = array(
      'http://www.albabotanica.com',
/*      'http://www.albadrinks.com',
      'http://www.arrowheadmills.com',
      'http://www.avalonorganics.com',
      'http://www.blueprintcleanse.com',
      'http://www.bostonssnacks.com',
      'http://www.candlecafefoods.com',
      'http://www.casbahnaturalfoods.com', */
      'http://www.celestialseasonings.com',
/*      'http://www.deboles.com',
      'http://www.earthsbest.com',
      'http://www.esteefoods.com',
      'http://www.ethnicgourmet.com',
      'http://www.ffactorfoods.com',
      'http://www.freebirdchicken.com',
      'http://www.plainvillefarms.com',
      'http://www.gardenofeatin.com',
      'http://www.gguniquefiber.com',
      'http://www.glutenfreechoices.com',
      'http://www.hain-celestial.com',
      'http://www.hainpurefoods.com',
      'http://www.hcgweb.net',
      'http://www.healthvalley.com',
      'http://www.hollywoodoils.com',
      'http://www.imaginefoods.com',
      'http://www.jason-personalcare.com/home?jason_site=us',
      'http://www.littlebearfoods.com',
      'http://www.low-gnutrition.com',
      'http://www.maranathafoods.com',
      'http://www.marthastewartclean.com',
      'http://www.mountainsun.com',
      'http://www.myhaincelestial.com',
      'http://www.myglutenfreecafe.com',
      'http://www.nilespice.com',
      'http://www.queenhelene.com',
      'http://www.rosetto.com',
      'http://www.sensibleportions.com',
      'http://www.sleepytimelullaby.com',
      'http://www.spectrumingredients.com',
      'http://www.spectrumorganics.com',
      'http://www.sunspire.com',
      'http://www.takeahealthybite.com',
      'http://www.tastethedream.com',
      'http://www.terrachips.com',
      'http://www.turnyourtablegreen.com',
      'http://www.walnutacres.com',
      'http://www.westbrae.com',
      'http://www.westsoymilk.com',
      'http://www.westsoytofu.com',
      'http://www.yvesveggie.com', */
      'http://www.zianatural.com',
   );
   
   var $api_key = 'AIzaSyCS2adwBpD2ZACDRb7Piw9UlpNQlao5Xmc';
   
   var $api_url = 'https://www.googleapis.com/pagespeedonline/v1';

   // --------------------------------------------------------------------

   function Pagespeed()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'monitor'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------

   /**
    * List of Google PageSpeed scores
    *
    */
   function index()
   {
      set_time_limit(0);
      ob_start();

      $admin['message'] = '';
      
      // set these early to avoid the database timing out.
      $data['admin'] = $admin;
      $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
      $data['submenu'] = get_cp_submenu('Analytics');
      $data['last_action'] = $this->session->userdata('last_action') + 1;

      $scores = array();
      
      foreach ($this->sites AS $site)
      {
         $call = $this->api_url.'/runPagespeed'.
                 '?key='.$this->api_key.
                 '&strategy=desktop'.
                 '&url='.urlencode($site);
//         echo $call; exit;
                 
         $ch = curl_init($call);
//         curl_setopt($ch, CURLOPT_POST, 1);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vars);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         curl_setopt($ch, CURLOPT_HEADER, 0);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         $result = curl_exec($ch);
         if ($result == '')
         {
            echo curl_error($ch);
         }
         curl_close($ch);

         $output = json_decode($result);
//         echo '<pre>'; print_r($output); echo '</pre>';
         
         $scores[] = array(
            'url' => $site,
            'score' => $output->score,
            'output' => $result,
         );
         
//         echo $site.' is completed.<br />';

         // ------------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ------------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();
      }
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('monitor');

      $data['scores'] = $scores;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/pagespeed/list', NULL, TRUE);
   }


}