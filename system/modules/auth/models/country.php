<?php
/**
 * Class Country
 * handles controller Class Auth requests dealing with Country table in DB
 * called in controller Auth.php by function _register_index()
 * 
 *
 * @package     FreakAuth
 * @subpackage  Models
 * @category    Authentication
 * @author      Daniel Vecchiato (danfreak)
 * @copyright   Copyright (c) 2007, 4webby.com
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link 		http://4webby.com/FreakAuth
 * @version 	1.0.2-Beta
 */


class Country extends Model 
{
	
	// ------------------------------------------------------------------------
	/**
	 * initialises the class inheriting the methods of the class Model 
	 *
	 * @return Usermodel
	 */
    function Country()
    {     
        parent::Model();
        
        //FreakAuth_light table prefix
        $this->_prefix = $this->config->item('FAL_table_prefix');
    }
	
    // ------------------------------------------------------------------------
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    function getCountriesForSelect()
    {	
        	//SELECT id, name FROM country
            $this->db->select('id, name');
            $query = $this->db->get($this->_prefix.'country');
            
            $options['0']="-----------------";
           	
            foreach ($query->result() as $row)
           	{
                    $options[$row->{'id'}] = $row->{'name'};
           	}
            
           	$query->free_result();
           	   
           	return $options;
               
    }
    
    function getCountryById($id)
    {	
        	//SELECT id, name FROM country
            $this->db->select('name');
            $this->db->where('id', $id);
            $query = $this->db->get($this->_prefix.'country');
            
            if ($query->num_rows() > 0)
            {
            	
                return $query;
                
            }
        
        	return null;
    }
    
}