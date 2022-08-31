<?php
// Copyright (c) 2001-2003 ars Cognita, Inc., all rights reserved
/*****************************************************************************
   
     This program is free software; you can redistribute it and/or modify
     it under the terms of the GNU General Public License as published by
     the Free Software Foundation; version 2 of the License.
   
     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.
   
     You should have received a copy of the GNU General Public License
     along with this program; if not, write to the Free Software
     Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
   
// ------------------------------------------------------------------------

/**
 * Tacl - Tiny ACL Library for PHP
 *
 * Tacl class and methods.
 *  
 * @author      $Author: richtl $
 * @version     $Revision: 1.18 $
 *
 * @package tackle
 */

// ------------------------------------------------------------------------

/**
 * Cool Brew ACL Class
 *
 * This class provides simple Access Control List funtionality
 *
 * @access      private
 * @package     tackle
 * @author      $Author: richtl $
 * @version     $Revision: 1.18 $
 * @since       $Date: 2003/10/21 21:19:08 $
 * @copyright   ars Cognita, Inc. 2002
 */
class Tacl
{
   
   /**
    * @var       object   CodeIgniter object
    * @access    private
    */
   var $CI;

   /**
    * @var       array   List of valid object types.
    * @access    private
    */
   var $object_types;
   
   /**
    * @var       string   prefix for database tables
    * @access    private
    */
   var $prefix;


   /**
    * Class constructor
    *
    * Initialize the database connection.
    *
    * access   public
    */
   function Tacl($params = NULL)
   {
      $params['debug'] = isset($params['debug']) ? $params['debug'] : TRUE;
      $params['db'] = isset($params['db']) ? $params['db'] : 'default';
      $params['prefix'] = isset($params['prefix']) ? $params['prefix'] : 'tacl';
      
      $this->object_types = array('member', 'group', 'membership',
         'resource', 'action', 'permission');
         
      $this->prefix = $params['prefix'];
      
      $this->CI =& get_instance();

      $this->CI->load->database($params['db']);
      
      log_message('debug', "ACL Class Initialized (db)");
   }
   
   // ====================================================================
   // General Methods
   // ====================================================================
   
   /**
    * Return the id of an object given it's identity
    *
    * @access   private
    * @param    string      Type of object
    * @param    string      Name of object
    * @return   mixed       id if successful, else FALSE
    */
   function get_id($obj_type, $name)
   {
      $result = FALSE;
         
      if (in_array($obj_type, $this->object_types))
      {
         $sql = 'SELECT ID '.
                'FROM '.$this->prefix.'_'.$obj_type.' '.
                'WHERE Name = "'.$name.'"';

         $query = $this->CI->db->query($sql);
         $row = $query->row_array($sql);

         if ($query->num_rows() > 0)
         {
            $result = $row['ID'];
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Verify an instance exists with the given id
    *
    * @access   private
    * @param    string     Object type
    * @param    int        Id to check
    * @return   mixed      TRUE if exists, else FALSE
    */
   function check_id($obj_type, $obj_id)
   {
      $result = FALSE;
         
      if ($obj_id && in_array($obj_type, $this->object_types))
      {
         $sql = 'SELECT ID '.
                'FROM '.$this->prefix.'_'.$obj_type.' '.
                'WHERE ID = '.$obj_id;

         $query = $this->CI->db->query($sql);
         $row = $query->row_array($sql);

         if ($query->num_rows() > 0)
         {
            $result = TRUE;
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Remove the object with the specified id
    *
    * @access   private
    * @param    int          objid of object instance
    * @return   boolean      FALSE if objid still exists after removal.
    */
   function remove_by_id($obj_type, $obj_id)
   {
      $result = FALSE;
         
      if (in_array($obj_type, $this->object_types))
      {
         if ($this->check_id($obj_type, $obj_id))
         {
            $this->CI->db->where('ID', $obj_id);
            $query = $this->CI->db->delete($this->prefix.'_'.$obj_type);

            if ($this->CI->db->affected_rows() == 1)
            {
               $result = TRUE;
            }
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Update the specified field of a record in the database given the id
    *
    * @access   private
    * @param    string       Type of object
    * @param    int          id of record
    * @param    string       Field/Property to change
    * @param    variable     New value of field/property
    * @return   boolean      TRUE if successful, else FALSE
    */
   function commit_by_id($obj_type, $obj_id, $field, $value)
   {
      $result = FALSE;
         
      if ($this->check_id($obj_type, $obj_id))
      {
         $values = array();
         $values[$field] = $value;
            
         $this->CI->db->where('ID', $obj_id);
         $query = $this->CI->db->update($this->prefix.'_'.$obj_type, $values);

         if ($this->CI->db->affected_rows() == 1)
         {
            $result = TRUE;
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Add a new record to the database for the requested ARO or ACO. 
    * This is generally called by a wrapper for the specific object.
    *
    * @access   private
    * @param    string      Type of object (member, group, etc.)
    * @param    string      Name of instance
    * @param    string      Description of instance
    * @param    boolean     Active status of the instance
    * @return   int         objid of new object, else FALSE
    */
   function create($obj_type, $name, $descr = NULL, $enabled = TRUE)
   {
      if (in_array($obj_type, $this->object_types))
      {
         $descr = isset($descr) ? $descr : $name;
         $enabled = $enabled == TRUE ? 1 : 0;
            
         // Strip double quotes from the name string
         $name = strtr($name, "\"", " ");
            
         // check if record with this ident exists
         $sql = 'SELECT ID '.
                'FROM '.$this->prefix.'_'.$obj_type.' '.
                'WHERE Name = "'.$name.'"';

         $query = $this->CI->db->query($sql);
            
         if ($query->num_rows() == 0)
         {
            $values = array('Name' => 'temp');
            $this->CI->db->insert($this->prefix.'_'.$obj_type, $values);
            
            $new_id = $this->CI->db->insert_id();
            
            // set the ident and descr to the ID if they are blank
            if ($name == NULL || $name == '') $name = $new_id;
            if ($descr == NULL || $descr == '') $descr = $new_id;

            $values['Name'] = $name;
            $values['Descr'] = $descr;
            $values['Enabled'] = $enabled;
            
            $this->CI->db->where('ID', $new_id);
            $this->CI->db->update($this->prefix.'_'.$obj_type, $values);
         }
      }
      return $this->get_id($obj_type, $name);
   }
      
   // --------------------------------------------------------------------

   /**
    * Remove a record from the database
    *
    * @access   private
    * @param   string      $name      Name of instance
    * @return   boolean      TRUE if removed, else FALSE
    */
   function remove($obj_type, $name)
   {
      $result = FALSE;
         
      if (in_array($obj_type, $this->object_types))
      {      
         if ($name)
         {   
            $result = $this->remove_by_id($obj_type, $this->get_id($obj_type, $name));
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Update the specified field of an record in the database
    *
    * @access   private
    * @param    string       Type of object
    * @param    string       Name of instance
    * @param    string       Field/Property to change
    * @param    variable     New value of field/property
    * @return   boolean      TRUE if successful, else FALSE
    */
   function commit($obj_type, $name, $variable, $value)
   {
      $result = FALSE;
         
      $result = $this->commit_by_id($obj_type, $this->get_id($obj_type, $name), $variable, $value);
         
      return $result;
   }
   
   // ====================================================================
   // Fundamental Wrapper Methods
   // ====================================================================
   
   /**
    * Change the identity of an instance
    *
    * @access   public
    * @param    string      Type of object (member, group, etc.)
    * @param    string      Current identity of object
    * @param    string      New identity
    */
   function change_name($obj_type, $old_name, $new_name)
   {
      return $this->commit($obj_type, $old_name, 'Name', $new_name);
   }
      
   // --------------------------------------------------------------------

   /**
    * Change the description of an instance
    *
    * @access   public
    * @param    string      Type of object (member, group, etc.)
    * @param    string      Name of object
    * @param    string      New description
    */
   function change_descr($obj_type, $name, $descr)
   {
      return $this->commit($obj_type, $name, 'Descr', $descr);
   }
      
   // --------------------------------------------------------------------

   /**
    * Set the status flag for the specified object to enabled.
    *
    * @access   public
    * @param   string      Type of object (member, group, etc.)
    * @param   string      Name of object
    */
   function enable($obj_type, $name)
   {
      return $this->commit($obj_type, $name, 'Enabled', 1);
   }
      
   // --------------------------------------------------------------------

   /**
    * Set the status flag for the specified object to disabled.
    *      
    * @access   public
    * @param   string      $obj_type   Type of object (member, group, etc.)
    * @param   string      $name      Name of object
    */
   function disable($obj_type, $name)
   {
      return $this->commit($obj_type, $name, 'Enabled', 0);
   }
      
   // --------------------------------------------------------------------

   /**
    * Create a hierarchy by linking the object to a parent object.
    * The object will inherit permissions from the parent object.
    *
    * @access   private
    * @param   string      $obj_type      Type of object (member, group, etc.)
    * @param   string      $name         Name of object
    * @param   string      $parent_name   Name of parent
    */
   function link_parent($obj_type, $name, $parent_name = NULL)
   {
      if ($parent_name != $name)
      {
         $parent_id = $this->get_id($obj_type, $parent_name);
      }
      else  // If the parent is the self, there's no parent.
      {
         $parent_id = 0;
      }
      
      if ($parent_id && $parent_id > 0)
      {
         $this->commit($obj_type, $name, 'Parent', $parent_id);
      }
      return $parent_id;
   }
      
   // --------------------------------------------------------------------

   /**
    * Disenherit the group from its parent.
    *
    * @access   private
    * @param    string     Type of object (member, group, etc.)
    * @param    string     Name of object
    * @return   string     Name of group
    */
   function unlink_parent($obj_type, $name)
   {
      $this->commit($obj_type, $name, 'Parent', 0);
   }
      
   // --------------------------------------------------------------------

   /**
    * Fetch the objid of an object's parent given an id
    *
    * @access   private
    * @param    string      Type of object
    * @param    int         Name of object
    * @return   int         objid of parent if successful, else NULL
    */
   function get_parent_by_id($obj_type, $obj_id)
   {
      $result = FALSE;
         
      if (in_array($obj_type, $this->object_types) && $obj_id)
      {
         $sql = 'SELECT Parent '.
                'FROM '.$this->prefix.'_'.$obj_type.' '.
                'WHERE ID = '.$obj_id;

         $query = $this->CI->db->query($sql);
         $row = $query->row_array($sql);

         if ($query->num_rows() > 0)
         {
            $result = $row['Parent'];
         }
      }
      return $result;
   }
      
   // ====================================================================
   // Member Wrapper Methods
   // ====================================================================
   
   /**
    * Creates new member
    *
    * @access   public
    * @param    string      Member identity
    * @param    string      Member description
    * @param    boolean     Member status
    * @return   int         objid of new member, else FALSE
    */
   function create_member($name, $descr = NULL, $enabled = TRUE)
   {
      if ( ! $descr)
      {
         $descr = $name;
      }
      return $this->create('member', $name, $descr, $enabled);
   }
      
   // --------------------------------------------------------------------

   /**
    * Removes a member. Also removes any permissions attached to the member.
    *
    * @access   public
    * @param    string      Member identity
    * @return   boolean     TRUE if removed, else FALSE
    */
   function remove_member($name)
   {
      $this->remove_permission("member", $name);

      $result = $this->remove('member', $name);

      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Return the list of groups the member belongs to as an array.
    *
    * @access   public
    * @param    string     Member identity
    * @return   array      List of group memberships (objid => ident)
    */
   function memberships($name)
   {
      $result = FALSE;
         
      $obj_id = $this->get_id('member', $name);
      if ($obj_id)
      {
         $sql = 'SELECT GroupID, g.Name '.
                'FROM '.$this->prefix.'_membership, '.
                        $this->prefix.'_group AS g '.
                'WHERE GroupID = g.ID '.
                'AND MemberID = '.$obj_id;

         $query = $this->CI->db->query($sql);
         $groups = $query->result_array($sql);
         
         foreach ($groups AS $group)
         {
            $result[$group['GroupID']] = $group['Name'];
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Check a member for access to the specified resource and action.
    *
    * @access   public
    * @param    string      Name of member
    * @param    string      Name of action to check
    * @param    string      Name of action's resource
    * @return   string      TRUE if member is allowed access, else FALSE
    */
   function authorized_member($name, $action, $resource)
   {
      $result = FALSE;
         
      $auth = $this->authorized_requestor('member', $name, $action, $resource);
      if ($auth != 'ALLOW')
      {
         $membership = $this->memberships($name);
         if ($membership)
         {
            foreach ($membership as $group_id => $group_name)
            {
               $groupAuth = $this->authorized_group($group_name, $action, $resource);
               if ($groupAuth == 'ALLOW')
               {
                  $auth = 'ALLOW';
               }
            }
         }
      }
      $result = $auth;
      return $result;
   }
   
   // ====================================================================
   // Group Wrapper Methods
   // ====================================================================
   
   /**
    * Creates new group
    *
    * @access   public
    * @param    string      Group identity
    * @param    string      Group description
    * @param    boolean     Group status
    * @return   int         objid of new group, else FALSE
    */
   function create_group($name, $descr = NULL, $enabled = TRUE)
   {
      if ( ! $descr)
      {
         $descr = $name;
      }
      return $this->create('group', $name, $descr, $enabled);
   }
      
   // --------------------------------------------------------------------

   /**
    * Removes a group. Also removes all memberships and dereferences
    * all children.
    *
    * @access   public
    * @param   string      $name      Group identity
    * @return   boolean      TRUE if removed, else FALSE
    */
   function remove_group($name)
   {
      $result = FALSE;
         
      // Remove memberships, permissions and unlink children first
      $this->remove_permission("group", $name);
      $result = $this->remove_group_children($name);
      $result = $result && $this->remove_group_members($name);
      $result = $result && $this->remove('group', $name);
         
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Remove all memberships that reference this group
    *
    * @access   public
    * @param    string       identity of group
    * @return   boolean      TRUE if removed, else FALSE
    */
   function remove_group_members($name)
   {
      $result = FALSE;
         
      $obj_id = $this->get_id('group', $name);
      if ($obj_id)
      {
         $this->CI->db->where('GroupID', $obj_id);
         $query = $this->CI->db->delete($this->prefix.'_membership');

         if ($this->CI->db->affected_rows() > 0)
         {
            $result = TRUE;
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Dereference all children that reference this group.
    *
    * @access   private
    * @param    string       identity of object instance
    * @return   boolean      TRUE if removed, else FALSE
    */
   function remove_group_children($name)
   {
      $result = FALSE;
         
      $obj_id = $this->get_id('group', $name);   
      if ($obj_id)
      {
         $values = array();
         $values['Parent'] = NULL;
            
         $this->CI->db->where('Parent', $obj_id);
         $query = $this->CI->db->update($this->prefix.'_group', $values);

         if ($this->CI->db->affected_rows() == 1)
         {
            $result = TRUE;
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Adds the specified member to the specified group.
    *
    * @access   public
    * @param    string   Name of group
    * @param    string   Name of member (default to instance property)
    * @return   string   Name of group
    */
   function add_to_group($group_name, $member_name)
   {
      $result = FALSE;
         
      $member_id = $this->get_id('member', $member_name);
      $group_id = $this->get_id('group', $group_name);
         
      if ($member_id && $group_id)
      {
         // make sure the user isn't already a group member
         $sql = 'SELECT * FROM '.$this->prefix.'_membership '.
                'WHERE MemberID = '.$member_id.' '.
                'AND GroupID = '.$group_id;
         $query = $this->CI->db->query($sql);
      
         if ($query->num_rows() == 0)
         {
            $newRecord = array();
            $newRecord['MemberID'] = $member_id;
            $newRecord['GroupID'] = $group_id;
            
            $this->CI->db->insert($this->prefix.'_membership', $newRecord);

            $result = $group_name;
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Remove the specified member from the specified group
    *
    * @access   public
    * @param    string      identity of group
    * @param    string      identity of member
    * @return   boolean     TRUE if removed, else FALSE
    */
   function remove_from_group($group_name, $member_name)
   {
      $group_id = $this->get_id('group', $group_name);
      $member_id = $this->get_id('member', $member_name);
         
      if ($group_id && $member_id)
      {
         $this->CI->db->where('GroupID', $group_id);
         $this->CI->db->where('MemberID', $member_id);
         $query = $this->CI->db->delete($this->prefix.'_membership');

         if ($this->CI->db->affected_rows() > 0)
         {
            return TRUE;
         }
      }
      return FALSE;
   }
      
   // --------------------------------------------------------------------

   /**
    * Link a group to another group (the parent). The child will
    * eventually inherit characteristics from the parent.
    *      
    * @access   public
    * @param    string      Name of group
    * @param    string      Name of parent group
    */
   function link_group_to_parent($name, $parent_name)
   {
      return $this->link_parent('group', $name, $parent_name);
   }
      
   // --------------------------------------------------------------------

   /**
    * Disenherit the group from its parent.
    *
    * @access   public
    * @param    string     Name of group
    * @return   string     Name of group
    */
   function unlink_group_from_parent($name)
   {
      return $this->unlink_parent('group', $name);
   }
      
   // --------------------------------------------------------------------

   /**
    * Check a group for access to the specified resource and action.
    *
    * @access   public
    * @param    string      Name of group
    * @param    string      Name of action to check
    * @param    string      Name of action's resource
    * @return   string      TRUE if group is allowed access, else FALSE
    *
    */
   function authorized_group($name, $action, $resource)
   {
      return $this->authorized_group_by_id(
               $this->get_id('group', $name),
               $this->get_id('action', $action), 
               $this->get_id('resource', $resource));
   }
      
   // --------------------------------------------------------------------

   /**
    * Check a group for access to the specified resource and action given
    * the objid of the group, action, and resource. This is a recursive
    * method. Be careful when messing around with it!
    *
    * @access   private
    * @param    int       ID of group
    * @param    int       ID of action to check
    * @param    int       ID of action's resource
    * @return   bool      TRUE if group is allowed access, else FALSE
    */
   function authorized_group_by_id($group_id, $action_id, $resource_id)
   {
      $result = FALSE;
         
      // Make sure we have necessary objids
      if ($group_id && $action_id && $resource_id)
      {
         $auth = $this->authorized_requestor_by_id('group', $group_id, $action_id, $resource_id);
            
         // If authorized, we can stop here. Else check parent.
         // Here's the fun recursive part.
            
         if ($auth != 'ALLOW')
         {
            // Hang in with me here. If our group has a parent, the group_id
            // is replaced by the current group's parent. Parent is now the 
            // new group's (the old parent's) parent. I.e., the grandparent.
            $group_id = $this->get_parent_by_id('group', $group_id);
            if ($group_id)
            {
               $auth = $this->authorized_group_by_id($group_id, $action_id, $resource_id);
            }
         }
         $result = $auth;
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Return the id of the parent of the specified group, if it exists.
    *
    * @access   public
    * @param   string      $name      Name of group
    * @return   int         objid of parent group, else NULL
    *
    * TODO: Test parent_group method
    */
   function parent_group($name)
   {
      $result = FALSE;
         
      $sql = 'SELECT Parent '.
             'FROM '.$this->prefix.'_group '.
             'WHERE Name = "'.$name.'"';
      $query = $this->CI->db->query($sql);
      $parent = $query->row_array;
      
      if ($query->num_rows() == 1)
      {
         $result = $parent['Parent'];
      }
      return $result;
   }
   
   // ====================================================================
   // Resource Wrapper Methods
   // ====================================================================
   
   /**
    * Creates new resource
    *
    * @access   public
    * @param    string      Resource identity
    * @param    string      Resource description
    * @param    boolean     Resource status
    * @return   int         objid of new resource, else FALSE
    */
   function create_resource($name, $descr = NULL, $enabled = TRUE)
   {
      if ( ! $descr)
      {
         $descr = $name;
      }
      return $this->create('resource', $name, $descr, $enabled);
   }
      
   // --------------------------------------------------------------------

   /**
    * Removes a resource
    *
    * @access   public
    * @param    string      Resource identity
    * @return   boolean     TRUE if removed, else FALSE
    */
   function remove_resource($name)
   {
      $result = FALSE;
         
      // Remove actions and permissions and unlink children first
      $this->remove_permission('*', '*', $name);
      $result = $this->remove_resource_children($name);
      $result = $result && $this->remove_resource_actions($name);
      $result = $result && $this->remove('resource', $name);
         
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Link a resource to another resource (the parent). The child will
    * eventually inherit characteristics from the parent.
    *      
    * @access   public
    * @param    string      Name of resource
    * @param    string      Name of parent resource
    */
   function link_resource_to_parent($name, $parent_name)
   {
      return $this->link_parent('resource', $name, $parent_name);
   }
      
   // --------------------------------------------------------------------

   /**
    * Unlink resource from parent resource
    *
    * @access   public
    * @param    string     Name of resource
    * @return   int        ID of resource
    */
   function unlink_resource_from_parent($name)
   {
      return $this->unlink_parent('resource', $name);
   }
      
   // --------------------------------------------------------------------

   /**
    * Dereference all children that reference this resource.
    *
    * @access   private
    * @param    string      identity of parent resource
    * @return   boolean     TRUE if removed, else FALSE
    */
   function remove_resource_children($name)
   {
      $result = FALSE;
         
      $obj_id = $this->get_id('resource', $name);   
      if ($obj_id)
      {
         $values= array();
         $values['Parent'] = NULL;
         
         $this->CI->db->where('Parent', $obj_id);
         $query = $this->CI->db->update($this->prefix.'_resource', $values);
         
         if ($this->CI->db->affected_rows() > 0)
         {
            $result = TRUE;
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Delete all actions from a resource.
    *
    * @access   public
    * @param   string   Name of resource
    */
   function remove_resource_actions($name)
   {
      // Get objid of resource
      $resource_id = $this->get_id('resource', $name);
         
      if ($resource_id)
      {
         // Remove permissions related to actions.
         $this->remove_permission('*', '*', $name);
         
         // remove all actions
         $this->CI->db->where('ResourceID', $resource_id);
         $this->CI->db->delete($this->prefix.'_action');
      }
   }
   
   
   // ====================================================================
   // Action Wrapper Methods
   // ====================================================================
   
   /**
    * Create an action and add it to a resource.
    *
    * @access   public
    * @param    string   Name of resource
    * @param    string   Name of action
    * @return   string   Name of resource
    */
   function create_action($resource_name, $action_name, $descr = NULL, $enabled = TRUE)
   {
      $result = FALSE;
         
      if ( ! $descr)
      {
         $descr = $action_name;
      }
         
      $resource_id = $this->get_id('resource', $resource_name);
      if ($resource_id)
      {
         // make sure the action doesn't already exist
         $sql = 'SELECT * FROM '.$this->prefix.'_action '.
                'WHERE Name = "'.$action_name.'" '.
                'AND ResourceID = '.$resource_id;
         $query = $this->CI->db->query($sql);
            
         if ($query->num_rows() == 0)
         {
            $values = array();
            $values = array('Name' => 'temp');
            $this->CI->db->insert($this->prefix.'_action', $values);
            
            $new_id = $this->CI->db->insert_id();
            
            $values['Name'] = $action_name;
            $values['Descr'] = $descr;
            $values['ResourceID'] = $resource_id;
            $values['Enabled'] = $enabled;
            
            $this->CI->db->where('ID', $new_id);
            $this->CI->db->update($this->prefix.'_action', $values);
         }
      }   
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Fetch an action's id given resource and action identifier.
    *
    * @access   private
    * @param    string     Name of action
    * @param    string     Name of resource
    * @return   int        ID of resource if available, else FALSE
    *
    */
   function get_action_id($action_name, $resource_name)
   {
      $result = FALSE;
         
      $resource_id = $this->get_id('resource', $resource_name);
      if ($resource_id)
      {
         $sql = 'SELECT ID '.
                'FROM '.$this->prefix.'_action '.
                'WHERE Name = "'.$action_name.'" '.
                'AND ResourceID = '.$resource_id;
         $query = $this->CI->db->query($sql);
         $action = $query->row_array();
         
         if ($query->num_rows() == 1)
         {
            $result = $action['ID'];
         }
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Delete an action from a resource.
    *
    * @access   public
    * @param    string    Name of resource
    * @param    string    Name of action
    * @return   bool      TRUE if action no longer exists, else FALSE
    */
   function remove_action($action_name, $resource_name)
   {
      $result = FALSE;
      
      // Remove permissions related to the action
      $this->remove_permission('*', '*', $resource_name, $action_name);
      $result = $this->remove_by_id('action', $this->get_action_id($action_name, $resource_name));
         
      return $result;
   }

   // ====================================================================
   // Permission Wrapper Methods
   // ====================================================================
   
   /**
    * Set or change a permission
    *
    * @access   public
    * @param   string      Type of requestor (member or group)
    * @param   string      Name of requestor
    * @param   string      Name of action to check
    * @param   string      Name of action's resource
    * @param   string      "ALLOW" or "DENY" (future "UNSET")
    * @param   string      "ENABLED" or "DISABLED"
    */
   function add_permission($req_type, $req_name, $resource, $action, $permit = "ALLOW", $status = "ENABLED")
   {
      // TODO: Allow permission for resources (without actions)
         
      $status = (strtoupper($status) == 'ENABLED') ? 1 : 0;
      $permit = (strtoupper($permit) == 'ALLOW') ? 1 : 0;
         
      // Set the requestor info
      switch (strtoupper($req_type))
      {
         case "MEMBER":
            $group_id = 0;
            $member_id = $this->get_id('member', $req_name);
            break;
         case "GROUP":
            $group_id = $this->get_id('group', $req_name);
            $member_id = 0;
            break;
         default:
            die ("Not a valid requestor type");
            break;
      }
         
      // Set the resource and action ids
      $resource_id = $this->get_id('resource', $resource);
      $action_id = $this->get_action_id($action, $resource);
         
      if ($resource_id && $action_id)
      {
         // Find out if we need to insert or update.
         $sql = 'SELECT * FROM '.$this->prefix.'_permission '.
                'WHERE ResourceID = '.$resource_id.' '.
                'AND ActionID = '.$action_id.' '.
                'AND MemberID = '.$member_id.' '.
                'AND GroupID = '.$group_id;
         $query = $this->CI->db->query($sql);
            
         if ($query->num_rows() == 1)
         {
            // update the existing record
            $values = array();
            $values['Access'] = $permit;
            $values['Enabled'] = $status;
            
            $this->CI->db->where('ResourceID', $resource_id);
            $this->CI->db->where('ActionID', $action_id);
            $this->CI->db->where('GroupID', $group_id);
            $this->CI->db->where('MemberID', $member_id);
            $this->CI->db->update($this->prefix.'_permission', $values);
         }
         else
         {
            // insert a new record
            $values = array();
            $values['ResourceID'] = $resource_id;
            $values['ActionID'] = $action_id;
            $values['GroupID'] = $group_id;
            $values['MemberID'] = $member_id;
            $values['Access'] = $permit;
            $values['Enabled'] = $status;

            $this->CI->db->insert($this->prefix.'_permission', $values);
         }
      }
   }
      
   // --------------------------------------------------------------------

   /**
    * Change an existing permission.
    *
    * @access   public
    * @param   string      Type of requestor (member or group)
    * @param   string      Name of requestor
    * @param   string      Name of action to check
    * @param   string      Name of action's resource
    * @param   string      "ALLOW" or "DENY" (future "UNSET")
    * @param   string      "ENABLED" or "DISABLED"
    */
   function change_permission($req_type, $req_name, $resource, $action, $permit = "UNSET", $status = "DISABLED")
   {
      return $this->add_permission($req_type, $req_name, $resource, $action, $permit, $status);
   }
      
   // --------------------------------------------------------------------

   /**
    * Remove a permission
    *
    * Removes am existing permission. You can pass req_name, resource, or
    * action the string '*' to ignore that object during the delete. I.E.,
    * specifying * for everything execpt resource, will delete all permissions
    * with the specified resource. Be VERRRRRY careful here!!!! Specifying
    * a * for resource, and either req_name or req_type is enough to remove
    * ALL permissions from the table!
    *
    * @access   public
    * @param    string      Type of requestor (member or group)
    * @param    string      Name of requestor
    * @param    string      Name of action to check
    * @param    string      Name of action's resource
    */
   function remove_permission($req_type = '*', $req_name = '*', $resource = '*', $action = '*')
   {
      $result = FALSE;
      $fail = FALSE;
         
      // TODO: Make action optional: if action isn't specified, remove requestor
      // from entire resource.
         
      $sqlWhere = '';
         
      // This will properly fail if there's no valid resource for the action.
      if ($action != '*')
      {
         $action_id = $this->get_action_id($action, $resource);
         if ($action_id)
            $this->CI->db->where('ActionID', $action_id);
      }
         
      if ($resource != '*')
      {
         $resource_id = $this->get_id('resource', $resource);
         if ($resource_id)
            $this->CI->db->where('ResourceID', $resource_id);
      }
         
      if ($req_name != '*' && $req_type != '*')
      {
         switch (strtoupper($req_type))
         {
            case "MEMBER":
               $member_id = $this->get_id('member', $req_name);
               $group_id = 0;
               break;
            case "GROUP":
               $group_id = $this->get_id('group', $req_name);
               $member_id = 0;
               break;
         }
            
         if ($member_id + $group_id > 0)
         {
            $this->CI->db->where('MemberID', $member_id);
            $this->CI->db->where('GroupID', $group_id);
         }
      }

      $this->CI->db->delete($this->prefix.'_permission');
   }
      
   // --------------------------------------------------------------------

   /**
    * Check a member or group for access to the specified resource and action.
    *
    * @access   public
    * @param    string      Type of requestor (member or group)
    * @param    string      Name of requestor
    * @param    string      Name of action to check
    * @param    string      Name of action's resource
    * @return   string      ALLOW/DENY if ACL exists, else NULL
    */
   function authorized_requestor($req_type, $name, $action, $resource)
   {
      $result = FALSE;
      $req_id = $this->get_id($req_type, $name);
      $action_id = $this->get_action_id($action, $resource);
      $resource_id = $this->get_id('resource', $resource);
         
      if ($req_id && $action_id && $resource_id)
      {
         $result = $this->authorized_requestor_by_id($req_type, $req_id, $action_id, $resource_id);
      }
      return $result;
   }
      
   // --------------------------------------------------------------------

   /**
    * Check a member or group for access to the specified resource and action.
    *
    * @access   private
    * @param    string      Type of requestor (member or group)
    * @param    string      ID of requestor
    * @param    string      ID of action to check
    * @param    string      ID of action's resource
    * @return   string      ALLOW/DENY if ACL exists, else NULL
    */
   function authorized_requestor_by_id($req_type, $req_id, $action_id, $resource_id)
   {
      $result = FALSE;
         
      // Query the DB to verify that ident has resource->action permissions.
      $sql = 'SELECT p.Access '.
             'FROM '.$this->prefix.'_permission AS p, '.
                     $this->prefix.'_'.$req_type.' AS q, '.
                     $this->prefix.'_resource AS r, '.
                     $this->prefix.'_action AS a '.
             'WHERE p.ResourceID = r.ID '.
             'AND p.ActionID = a.ID '.
             'AND p.'.ucfirst($req_type).'ID = q.ID '.
             'AND q.ID = '.$req_id.' '.
             'AND r.ID = '.$resource_id.' '.
             'AND a.ID = '.$action_id.' '.
             'AND a.Enabled = 1 '.
             'AND r.Enabled = 1 '.
             'AND q.Enabled = 1 '.
             'AND p.Enabled = 1';

      $query = $this->CI->db->query($sql);
      $record = $query->row_array();

      // Requestor is only permitted if 
      // (1) we get a row back and (2) the value of Access is '1'. 
      if ($query->num_rows() == 1)
      {
         $result = ($record['Access'] == 1) ? 'ALLOW' : 'DENY';
      }
      else
      {
         // Handle recursive authorizations.
         // Patch submitted by Tom Johnson (Thanks Tom!)
         $resource_id = $this->get_parent_by_id('resource', $resource_id);
         if ($resource_id)
         {
            $auth = $this->authorized_requestor_by_id($req_type, $req_id, $action_id, $resource_id);
         }
         if (isset($auth))
         {
            $result = $auth;
         }
      }
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Get all access rights for a member or group
    *
    * @access   public
    * @param    string      Type of requestor (member or group)
    * @param    string      Name of requestor
    * @return   string      ALLOW/DENY if ACL exists, else NULL
    */
   function authorizations($req_type, $name)
   {
      $result = FALSE;
      $req_id = $this->get_id($req_type, $name);
         
      if ($req_id)
      {
         $result = $this->authorizations_by_id($req_type, $req_id);
      }
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Get all access rights for a member or group
    *
    * @access   private
    * @param    string      Type of requestor (member or group)
    * @param    string      ID of requestor
    * @return   array       list of access rights
    */
   function authorizations_by_id($req_type, $req_id)
   {
      $result = FALSE;
         
      // Query the DB to retrieve all resource->action permissions.
      $sql = 'SELECT p.ResourceID, r.Name AS ResourceName, p.ActionID, '.
               'a.Name AS ActionName, p.Access '.
             'FROM '.$this->prefix.'_permission AS p, '.
                     $this->prefix.'_'.$req_type.' AS q, '.
                     $this->prefix.'_resource AS r, '.
                     $this->prefix.'_action AS a '.
             'WHERE p.ResourceID = r.ID '.
             'AND p.ActionID = a.ID '.
             'AND p.'.ucfirst($req_type).'ID = q.ID '.
             'AND q.ID = '.$req_id.' '.
             'AND a.Enabled = 1 '.
             'AND r.Enabled = 1 '.
             'AND q.Enabled = 1 '.
             'AND p.Enabled = 1';

      $query = $this->CI->db->query($sql);
      $records = $query->result_array();
      
      for ($i=0, $cnt=count($records); $i<$cnt; $i++)
      {
         $records[$i]['Access'] = ($records[$i]['Access'] == 1) ? 'ALLOW' : 'DENY';
      }
      return $records;
   }

}  // END Tacl Class


/*
// User-space functions

function tackle_authorized_member($member, $action, $resource) {
   $check = new tackle();
   return $check->authorized_member($member, $action, $resource);
}

function tackle_authorized_group($group, $action, $resource) {
   $check = new tackle();
   return $check->authorized_group($group, $action, $resource);
}
*/

?>
