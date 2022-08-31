<?php

/*
This is a schema based on the Ruby On Rails migration stuff

NOTES:

table:options -- allows you to pass raw SQL that will be appended to the 
create table instruction. It is this way that you can set things like 
character encoding, or table types.

column:null -- "null => false” implies NOT NULL
*/

$schema['tables'][] = array(
   'name' => '/table name/',
   'id' => TRUE,  // Whether to automatically add a primary key column.
   'primary_key' => '',  // The name of the primary key, if one is to be added automatically. Defaults to id.
   'options' => '',
   'force' => FALSE,  // Set to true to drop the table before creating it.
   'columns' => array(
      0 => array(
         'name' => ''
         'type' => 'integer|float|decimal|datetime|date|timestamp|time|text|string|binary|boolean',
         'limit' => '',      // maximum column length for string, text, binary or integer columns
         'default' => '',    // The column‘s default value. Use nil for NULL.
         'null' => FALSE,    // false implies NOT NULL
         'precision' => '',  // Specifies the precision for a :decimal column.
         'scale' => '',      // Specifies the scale for a :decimal column.
      ),
   ),
   'indexes' => array(
      0 => array(
         'name' => '',
         'columns' => array('/ColumnName1/', '/ColumnName2/'),
         'unique' => FALSE,
      ),
   ),
);

?>