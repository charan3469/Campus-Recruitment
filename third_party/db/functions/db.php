<?php

/* default database functions starts from here
 <!-----------------------------------------------------------------------------------------------------------------------------------------
 */

/** 
 * returns database object
 *
 * @param   void
 * 
 * @return  $db     object  =>  database objec
 */
function get_db() {
	
	global $db;
	
    return $db;

}
 
/**
 * returns table by prepending prefix to it
 *
 * @param   $table  string  =>  table name
 *  
 * @return  $return string  =>  table name with prefix
 */
function _table($table) {

    return get_db() -> _table($table);

}

/**
 * returns last insert id
 *
 * @param   $db     string  =>  database in which query has to execute
 *                  empty   =>  to user working database
 *  
 * @return  $return int     =>  returns inserted row auto increment value if exists
 *                          =>  0 if no auto increment field exists
 *                  boolean =>  false if query execution fails
 */
function db_last_insert($db = '') {

    return get_db() -> _getLastInsertId($db);

}

/**
 * calls run function and returns the results
 *
 * @param   $q      string  =>  database query
 * @param   $ab     array   =>  array bind values
 *                  empty   =>  if no array bind
 * @param   $db     string  =>  database in which query has to execute
 *                  empty   =>  to user working database
 * 
 * @return  $return boolean =>  true, if executed successfully
 *                          =>  false, if failed  
 */
function db_run($q, $ab = '', $db = '') {

    return get_db() -> _run($q,$ab,$db);

}

/**
 * calls get row function and returns the results
 *
 * @param   $q      string  =>  database query
 * @param   $ab     array   =>  array bind values
 *                  empty   =>  if no array bind
 * @param   $db     string  =>  database in which query has to execute
 *                  empty   =>  to user working database
 *  
 * @return  $return object  =>  database row fetched
 *                  boolean =>  false, if failes or now rows
 */
function db_row($q, $ab = '', $db = '') {

    return get_db() -> _getRow($q,$ab,$db);

}

/**
 * calls get all rows function and returns the results
 *
 * @param   $q      string  =>  database query
 * @param   $ab     array   =>  array bind values
 *                  empty   =>  if no array bind
 * @param   $db     string  =>  database in which query has to execute
 *                  empty   =>  to user working database
 *  
 * @return  $return array   =>  array of database rows
 *                  boolean =>  false, if failes or now rows
 */
function db_rows($q, $ab = '', $db = '') {

    return get_db() -> _getAllRows($q,$ab,$db);

}

/**
 * calls get row count function and returns the no of rows
 *
 * @param   $q      string  =>  database query
 * @param   $ab     array   =>  array bind values
 *                  empty   =>  if no array bind
 * @param   $db     string  =>  database in which query has to execute
 *                  empty   =>  to user working database
 *  
 * @return  $return int     =>  no of entries 
 */
function db_count($q, $ab = '', $db = '') {

    return get_db() -> _getRowCount($q,$ab,$db);

}

/* default database functions ends here
 -----------------------------------------------------------------------------------------------------------------------------------------!>
 */
 
/* short database functions starts from here
 <!-----------------------------------------------------------------------------------------------------------------------------------------
 */

 /**
 * calls db short insert function and returns autoincrement value
 *
 * @param   $table      string  =>  table name
 * @param   $fields     array   =>  array of fileds 
 *                                      => format  array(
 *                                                          'field' =>  'value,
 *                                                          'field' =>  'value,
 *                                                      );
 *  
 * @return  $return     int     =>  returns inserted row auto increment value if exists
 *                              =>  0 if no auto increment field exists
 *                      boolean =>  false if query execution fails
 */
function sdb_i($table, $fields = array()) {

    return get_db() -> _i($table,$fields);

}

 /**
 * calls db short update function 
 *
 * @param   $table      string  =>  table name
 * @param   $fields     array   =>  array of fileds 
 *                                      => format  array(
 *                                                          'field' =>  'value,
 *                                                          'field' =>  'value,
 *                                                      );
 * @param   $filters    array   =>  array of conditions
 *                                      => format  array(
 *                                                          'field' =>  'value,
 *                                                          'field' =>  'value,
 *                                                      );
 *                              =>  empty array, if no conditions
 *  
 * @return  $return     boolean =>  true if query executed
 *                              =>  false if query execution fails    
 */
function sdb_u($table, $fields = array(), $filters = array()) {

    return get_db() -> _u($table,$fields,$filters);

}

 /**
 * calls db short delete function
 *
 * @param   $table      string  =>  table name
 * @param   $filters    array   =>  array of conditions 
 *                                      => format  array(
 *                                                          'field' =>  'value,
 *                                                          'field' =>  'value,
 *                                                      );
 *  
 * @return  $return     boolean =>  true if rows delted
 *                              =>  false if deletion fails    
 */
function sdb_d($table, $filters = array()) {

    return get_db() -> _d($table,$filters);

}

 /**
 * calls db short get row function 
 *
 * @param   $table      string  =>  table name
 * @param   $fields     array   =>  array of fileds 
 *                                      => format  array(
 *                                                          'field' =>  'value,
 *                                                          'field' =>  'value,
 *                                                      );
 * @param   $filters    array   =>  array of conditions
 *                                      => format  array(
 *                                                          'field' =>  'value,
 *                                                          'field' =>  'value,
 *                                                      );
 *                              =>  empty array, if no conditions
 *  
 * @return  $return     object  =>  database row
 *                      boolean =>  false if query execution fails    
 */
function sdb_row($table, $filters = array(), $sortbys = array()) {

    return get_db() -> _gR($table,$filters,$sortbys);

}

 /**
 * calls db short get all rows function 
 *
 * @param   $table      string  =>  table name
 * @param   $filters    array   =>  array of conditions
 *                                      => format  array(
 *                                                          'field' =>  'value,
 *                                                          'field' =>  'value,
 *                                                      );
 *                              =>  empty array, if no conditions
 * @param   $sortby     array   =>  array of sortbys
 *                                      => format  array(
 *                                                          'field' =>  'order asc/desc,
 *                                                          'field' =>  'order asc/desc,
 *                                                      );
 *                              =>  empty array, if no sortbys
 *  
 * @return  $return     object  =>  database row
 *                      boolean =>  false if query execution fails    
 */
function sdb_rows($table, $filters = array(), $sortbys = array()) {

    return get_db() -> _gAR($table,$filters,$sortbys);

}

 /**
 * calls db short row count function 
 *
 * @param   $table      string  =>  table name
 * @param   $filters    array   =>  array of conditions
 *                                      => format  array(
 *                                                          'field' =>  'value,
 *                                                          'field' =>  'value,
 *                                                      );
 *                              =>  empty array, if no conditions
 *  
 * @return  $results    int     =>  rows count 
 */
function sdb_count($table, $filters = array()) {

    return get_db() -> _gRC($table,$filters);

}

/** 
 * calls database show query function
 *
 * @param   $query      string  =>  query 
 * @param   $bind       array   =>  array of bind value
 *                              =>  empty array, if no bind values
 * 
 * @return  $return     string  =>  query with replaced array bind values
 */
function db_show($query, $bind = array()) {
        
    return get_db() -> _showQuery($query,$bind);

}

/** 
 * call last database error function
 *
 * @param   void
 * 
 * @return  $return     mixed  =>  error
 */
function db_error() {
        
    return get_db() -> _getError();

}
