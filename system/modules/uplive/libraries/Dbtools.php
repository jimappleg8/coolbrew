<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Based on dbUtils 1.1  by Jonathan Hilgeman
// Released: 8-16-2001
// Homepage: http://www.SiteCreative.com
// Location: http://www.SiteCreative.com/projects/dbUtils.php
// Description: A free collection of odd functions to manage mySQL databases.

/*

I'm trying to rethink this so it will be the most flexible way of moving data 
from one database to another. In order to do that, I am planning to have the 
main logic reside in models. That way, I can construct very specific SQL for 
uploading any table according to whatever the needs are.

So, this class needs to be more general.

	- I need to be able to pass in the database connections. It looks like I tried 
	to allow for non-coolbrew connections by making sure I included the local 
	database config file, but I think it makes more sense to define the database 
	connections in the models. That seems more encapsulated.
	
	- I need to be able to define the delete and insert SQL in the model rather 
	than just sending a "where" statement to the method. I think I can use the 
	CodeIgniter Active Record class to help construct those in many cases.

*/

class Dbtools {

   function Dbtools()
   {
   }
   
   // --------------------------------------------------------------------

   /**
    * Copies table records according to the supplied SQL SELECT statement.  
    *
    * @param   obj      source database object
    * @param   obj      target database object
    * @param   string   table name
    * @param   string   the SELECT statement
    * @return  bool     TRUE if successful, FALSE if not
    *
    */
   function db_table_copy_sql(&$src_db, &$tgt_db, $table, $sql)
   {
      // Construct Query to Send to Receiving Server
                        
      // Make sure the database and table exist on the target server
      $query_array[] = 'CREATE DATABASE IF NOT EXISTS '.$tgt_db->database.';';
      $query_array[] = 'USE '.$tgt_db->database.';';
      $query_array[] = $this->create_table_string($src_db, $table, $tgt_db->hostname);

      // Delete the records matching the $sql statement
      $deletes = $this->delete_array($tgt_db, $table, $sql);
      
      // Data Inserts            
      $inserts = $this->insert_array($src_db, $table, $sql);
                
      // Send All Queries to Receiving Server
      foreach ($query_array as $query)
      {
         $result = $tgt_db->query($query);
      }
      foreach ($deletes as $delete)
      {
         $result = $tgt_db->query($delete);
      }
      foreach ($inserts as $insert)
      {
         $result = $tgt_db->query($insert);
      }
        
      // Success!
      return 1;
   }

   // --------------------------------------------------------------------

   /**
    * Copies a table by dropping the existing table and rebuilding it.  
    *
    * @param   obj      source database object
    * @param   obj      target database object
    * @param   string   table name
    * @return  bool     TRUE if successful, FALSE if not
    *
    */
   function db_table_copy_simple(&$src_db, &$tgt_db, $table)
   {
      // Construct Query to Send to Receiving Server

      // Make sure the database and table exist on the target server
      $query_array[] = 'CREATE DATABASE IF NOT EXISTS '.$tgt_db->database.';';
      $query_array[] = 'USE '.$tgt_db->database.';';

      // Table Definitions
      $query_array[] = 'DROP TABLE IF EXISTS '.$table.';';
      $query_array[] = $this->create_table_string($src_db, $table);
      
      // Data Inserts
      $sql = 'SELECT * FROM '.$table;
      $inserts = $this->insert_array($src_db, $table, $sql);

      // Send All Queries to Receiving Server
      foreach ($query_array as $query)
      {
         $result = $tgt_db->query($query);
      }
      foreach ($inserts as $insert)
      {
         $result = $tgt_db->query($insert);
      }
        
      // Success!
      return 1;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a CREATE TABLE statement for the table in question.
    *
    * $param   obj      source database object
    * @param   string   table name
    * @return  string   CREATE TABLE statement
    */
   function create_table_string(&$src_db, $table)
   {
      $sql = 'SHOW CREATE TABLE '.$table;
      $query = $src_db->query($sql);
      $create_table = $query->row_array();
      
      $definition = $create_table['Create Table'];
      
      $definition = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $definition);

      return $definition;
   }

   // --------------------------------------------------------------------

   /**
    * Drops the table in question.
    *
    * $param   obj      source database object
    * @param   string   table name
    * @return  boolean
    */
   function drop_table(&$src_db, $table)
   {
      $sql = 'DROP TABLE IF EXISTS '.$table;
      $src_db->query($sql);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Builds an array of insert statements based on the supplied database
    *   object and SQL select statement.
    * 
    * @param   obj      database object
    * @param   string   table name
    * @param   string   the SELECT statement
    * @return  array    insert statements
    */
   function insert_array(&$src_db, $table, $sql)
   {
      $query = $src_db->query($sql);
      $result = $query->result_array();

      $insert_array = array();
      foreach ($result AS $row)
      {
         $insert_array[] = $src_db->insert_string($table, $row);
      }
      return $insert_array;
   }

   // --------------------------------------------------------------------

   /**
    * Builds an array of delete statements based on the supplied database
    *   object and SQL select statement.
    * 
    * @param   obj      database object
    * @param   string   table name
    * @param   string   the SELECT statement
    * @return  array    insert statements
    */
   function delete_array(&$src_db, $table, $sql)
   {
      $query = $src_db->query($sql);
      $result = $query->result_array();

      // Figure out the primary key    
      $sql = 'SHOW KEYS FROM '.$table;
      $query = $src_db->query($sql);
      $keys = $query->result_array();
      
      $column = array();
      foreach ($keys AS $key)
      {
         if ($key['Key_name'] == 'PRIMARY')
         {
            $column[] = $key['Column_name'];
         }
      }
      
      if (empty($column))
      {
         echo 'Error: table ('.$table.') must have a primary key.';
         exit;
      }
  
      $col_count = count($column);

      $delete_array = array();
      foreach ($result AS $row)
      {
         // Note: all values are quoted
         // I don't think this is a problem in this context
         // as we are not using the numbers to do any calculations
         $where = 'WHERE ';
         for ($i=0; $i<$col_count; $i++)
         {
            $where .= $column[$i].' = "'.$row[$column[$i]].'"';
            if ($i < $col_count - 1)
            {
               $where .= ' AND ';
            }
         }
         $delete = 'DELETE FROM '.$table.' '.$where;
         $delete_array[] = $delete;
      }
      return $delete_array;
   }

   // --------------------------------------------------------------------

   /**
    * Builds a WHERE statement from site_id and additional Where.
    *
    * It does not put "WHERE" at the beginning of the string in case you
    *  want to add the resulting string to a larger WHERE statement.
    * 
    * @param   string   the name of the SiteID column
    * @param   string   the SiteID
    * @param   string   the WHERE statement
    * @return  array    modified WHERE statements
    */
   function fix_where($site_field, $site_id, $where)
   {
      $mywhere = '';
      if ($site_id != "all")
      {
         $mywhere = $site_field.' = "'.$site_id.'"';
         if ($where != "")
         {
            $mywhere .= " AND ".$where;
         }
      }
      elseif ($where != "")
      {
         $mywhere = $where;
      }
      return $mywhere;
   }

   // --------------------------------------------------------------------

   /**
    * Builds a list of tables
    *
    * @param   obj      database object
    * @return  array    table names
    */
   function list_tables(&$src_db)
   {
      $database = $src_db->database;
      
      $sql = 'SHOW TABLES FROM '.$database;
      $query = $src_db->query($sql);
      $table_names = $query->result_array();
      
      $tables = array();
      foreach ($table_names AS $myname)
      {
         $tables[] = $myname['Tables_in_'.$database];
      }
      return $tables;
   }


   // ====================================================================
   //  The methods below have not yet been converted to the class
   // ====================================================================

   /**
    * Copies a single database from one server to another
    * Example:
    *   dbSpecificCopy("ServerOne","ServerTwo","LocalDatabase","RemoteDatabase");
    */
   function dbSpecificCopy($FromHost,$ToHost,$FromDatabaseName,$ToDatabaseName)
   {
      // Connect to Databases
      $dbLinkOne = dbConnect($FromHost);
      $dbLinkTwo = dbConnect($ToHost);

      // Get all Table Names
      $dbList = mysql_list_tables($FromDatabaseName,$dbLinkOne);

      while ($dbRow = mysql_fetch_array($dbList)) {
         $TableName = $dbRow[0];
         $dbTableNames[] = $TableName;
      }

      // Construct Query to Send to Receiving Server

      // Create Databases
      $SendQuery[] = "CREATE DATABASE IF NOT EXISTS $ToDatabaseName;";

      // Table Definitions
      foreach ($dbTableNames as $dbTableName) {
         $SendQuery[] = "DROP TABLE IF EXISTS $dbTableName;";
         $SendQuery[] = ReturnCreateTable($FromDatabaseName, $dbTableName, $dbLinkOne);
      }

      // Data Inserts            
      foreach ($dbTableNames as $dbTableName) {
         $TableInserts = ReturnTableInserts($FromDatabaseName, $dbTableName, $dbLinkOne);

         if (count($TableInserts)) {
            foreach ($TableInserts as $InsertString) {
               $SendQuery[] = $InsertString;
            }
         }
      }

      // Send All Queries to Receiving Server

      foreach ($SendQuery as $Query) {
         if (substr($Query,0,15) == "CREATE DATABASE") {
            $dbResult = mysql_query($Query,$dbLinkTwo) or die(mysql_error() . " - $Query");
         } else {
            $dbResult = mysql_db_query($ToDatabaseName,$Query,$dbLinkTwo) or die(mysql_error() . " - $Query");
         }
      }

      // Success!
      return 1;
   }

   // --------------------------------------------------------------------
   /**
    * This function will copy multiple tables (based on a regular expression)
    * between databases on two different servers
    */
   function dbCopyRegEx($FromHost, $ToHost, $FromDb, $ToDb, $TableRegEx)
   {
      // Connect to Databases
      $dbLinkOne = dbConnect($FromHost);
      $dbLinkTwo = dbConnect($ToHost);

      // Get all tables matching regular expression
      $tableList = mysql_list_tables($FromDb, $dbLinkOne);
      while ($tableRow = mysql_fetch_array($tableList)) {
         $tableName = $tableRow[0];
         if (preg_replace($TableRegEx, "", $tableName) != $tableName) {
            $tablesToTransfer[] = $tableName;
         }
      }

      // Construct Query to Send to Receiving Server

      // Make sure the database and table exist on the target server
      $SendQuery[] = "CREATE DATABASE IF NOT EXISTS $ToDb;";
      $SendQuery[] = "USE $ToDb;";

      // Table Definitions
      foreach ($tablesToTransfer as $dbTableName) {
         $SendQuery[] = "DROP TABLE IF EXISTS $dbTableName;";
         $SendQuery[] = ReturnCreateTable($FromDb, $dbTableName, $dbLinkOne, $ToHost);
      }

      // Data Inserts            
      foreach ($tablesToTransfer as $dbTableName) {
         $TableInserts = ReturnTableInserts($FromDb, $dbTableName, $dbLinkOne);
         if (count($TableInserts)) {
            foreach ($TableInserts as $InsertString) {
               $SendQuery[] = $InsertString;
            }
         }               
      }

      // Send All Queries to Receiving Server
      foreach ($SendQuery as $Query) {
         if (substr($Query,0,15) == "CREATE DATABASE") {
            $dbResult = mysql_query($Query, $dbLinkTwo) or die(mysql_error() . " - Line 354 - $Query");
         } else {
            $dbResult = mysql_db_query($ToDb, $Query, $dbLinkTwo) or die(mysql_error() . " - Line 355 - $Query");
         }
      }
   
      // Success!
      return 1;
   }

}
