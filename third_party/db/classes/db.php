<?php

/**
 * db class. This class functions are accessed to perform database related actions
 *
 * @version 1.0
 * @http://www.quikc.org/
 */
class db extends dbCore {

    /** 
     * Contains extension instances
     *
     * @var array
     */
    private static $extensions = array();
    
    /** 
     * returns database extension instances
     *
     * @param   void
     * @return  $return object  =>  extension object if extension exists
     *                  null    =>  if extension doesnot exists
     */ 
    private function _getExtension(){
        
        $ext = $this -> _get('db.ext');
        
        if( isset(self::$extensions[$ext]) ){
            
            return self::$extensions[$ext];
            
        }
        
        $class = "DbExtension".ucfirst($ext);
        
        if( !class_exists($class) ){

			$base = dirname(__FILE__);
			            
            if( file_exists($base . '/extensions/'. $ext .'.php') ){

                require $base . '/extensions/'. $ext .'.php';
                
            }
        
        }
        
        if( class_exists($class) ){

            self::$extensions[$ext] = new $class;
            
        }
        
        return isset( self::$extensions[$ext] ) ? self::$extensions[$ext] : null;
        
    }

    /** 
	 * checks weather the database connection can be established or not
     *
     * @param 	$opts	array 	=>	array of database options
	 * 								type		string	=>	database type required
	 * 														mysql by default
	 * 								host		string	=>	host
	 * 								user		string	=>	user name
	 * 								pass		string	=>	password
	 * 								dbName		string	=>	database name
	 * 
	 * @return 	$return	object	=>	pdo connection object if connection can be established
	 * 					boolean	=>	false, if connection cannot be established
     */
    public function _checkConnection($opts) {

        if ( is_null($dbName) ){

            $dbName = $this -> _getDatabase();
            
        }

        // Checking for the existing instances
        if ( !isset(self::$dbObjects[$dbName]) ) {

            self::$dbObjects[$dbName] = $this -> _connectDb( array('dbName' => $dbName) );

        }

        return self::$dbObjects[$dbName];

    }

    /** 
	 * displays database query by replacing the array bind values
     *
     * @param	$query		string	=>	query 
     * @param	$bind	    array	=>	array of bind value
	 * 								=>	empty array, if no bind values
	 * 
     * @return	$return		string	=>	query with replaced array bind values
     */
    public function _showQuery($query, $bind = array()) {
    
		if( !qc_empty_array($bind) ){

	        foreach($bind as $k => $bindVal){
	
				$val = (is_array($bindVal)?$bindVal['value']:$bindVal);
	            $query = preg_replace('/'.$k.'/',"'".$val."'",$query);
	
	        }
			
		}
            
        return $query;

    }

    /* Short query function starts from here
     <!-----------------------------------------------------------------------------------------------------------------------------------------
     */

    /** 
	 * prepares query with given table, filters and sortbys
     *
     * @param	$table		string	=>	table name
	 * 
     * @param	$filters	array	=>	array of filters 
	 * 										=> format  array(
	 * 															'field'	=>	'value,
	 * 															'field'	=>	'value,
	 * 														);
	 * 								=>	empty array, if no filters
	 * 
     * @param	$sortby		array	=>	array of sortbys
	 * 										=> format  array(
	 * 															'field'	=>	'value,
	 * 															'field'	=>	'value,
	 * 														);
	 * 								=>	empty array, if no sortbys
	 * 
     * @return	$gQuery		array	=>	generated query and array bind values
	 * 									query		string	=>	database query to be execued
	 * 									bind	array 	=>	array bind values to be binded
     */
    public function _pq($table, $filters = array(), $sortBy = array()) {
        
		// creating base query
        $query = "select * from ". $this ->  _table($table);
        
		// defining where, bind, soryby arrays
        $whereArray = $bind = $sortbyArray = array();
        
		// checking weather any filters given
        if( !qc_empty_array($filters) ){

			// for all the given filters preparing array bind and where query parts
            foreach($filters as $keyField => $valueField ){

                $whereArray[]= $keyField. " = :".$keyField;
                $bind[":".$keyField]= array("value" =>  $valueField );

            }
            
			// appending final where query to main query
            $query .= " where ".implode(' and ',$whereArray);

        }
        
		// checking whether any sortbys given
        if( !qc_empty_array($sortBy) ){

			// for all the given sortbys preparing array bind and sortby query parts
            foreach($sortBy as $keyField => $valueField ){

                if( !in_array($valueField, array('asc','desc')) ){

                    $valueField = 'asc';
                    
                } 

                $sortbyArray[]= $keyField. " ".$valueField;

            }
            
			// appending final sortby query to main query
            $query .= " order by ".implode(' , ',$sortbyArray);
        }

        $gQuery = array("query" => $query, "bind" => $bind) ;
        
        return $gQuery;

    }

    /** 
	 * returns a row with given conditions
	 * 
	 * Ex: $ob -> _gR('user',array('idUser' => 1) ) this will return user with id i
     *
     * @param	$table		string	=>	table name
	 * 
     * @param	$filters	array	=>	array of filters 
	 * 										=> format  array(
	 * 															'field'	=>	'value,
	 * 															'field'	=>	'value,
	 * 														);
	 * 								=>	empty array, if no filters
	 * 
     * @param   $sortby     array   =>  array of sortbys
     *                                      => format  array(
     *                                                          'field' =>  'order asc/desc,
     *                                                          'field' =>  'order asc/desc,
     *                                                      );
     *                              =>  empty array, if no sortbys
	 * 
     * @return	$results	object	=>	if row found for given conditions
	 * 						boolean	=>	false, if no rows found
     */
    public function _gR($table, $filters = array(), $sortBy = array()) {

        $gQuery = $this -> _pq($table, $filters, $sortBy);
        
        $results = $this -> _getRow($gQuery['query'], $gQuery['bind']);
        
        return $results;

    }

    /** 
	 * returns all rows with given conditions
	 * 
	 * Ex: $ob -> _gAR('user',array('statusUser' => 1) ) this will return all active users
     *
     * @param	$table		string	=>	table name
	 * 
     * @param	$filters	array	=>	array of filters 
	 * 										=> format  array(
	 * 															'field'	=>	'value,
	 * 															'field'	=>	'value,
	 * 														);
	 * 								=>	empty array, if no filters
	 * 
     * @param	$sortby		array	=>	array of sortbys
	 * 										=> format  array(
	 * 															'field'	=>	'order asc/desc,
	 * 															'field'	=>	'order asc/desc,
	 * 														);
	 * 								=>	empty array, if no sortbys
	 * 
     * @return	$results	array	=>	list of rows found for given conditions
	 * 						boolean	=>	false, if no rows found
     */
    public function _gAR($table, $filters = array(), $sortBy = array()) {

        $gQuery = $this -> _pq($table, $filters, $sortBy);
        
        $results = $this -> _getAllRows($gQuery['query'], $gQuery['bind']);
        
        return $results;

    }

    /** 
	 * returns row count with given conditions
	 * 
	 * Ex: $ob -> _gRC('user',array('statusUser' => 1) ) this will no of active users
     *
     * @param	$table		string	=>	table name
	 * 
     * @param	$filters	array	=>	array of filters 
	 * 										=> format  array(
	 * 															'field'	=>	'value,
	 * 															'field'	=>	'value,
	 * 														);
	 * 								=>	empty array, if no filters
	 * 
     * @return	$results	int		=>	rows count 
     */
    public function _gRC($table, $filters = array()) {

        $gQuery = $this -> _pq($table, $filters);
        
        $results = $this -> _getRowCount($gQuery['query'], $gQuery['bind']);
        
        return $results;

    }

    /** 
	 * performs update action on give table for given fileds with given conditions
	 * 
	 * Ex: $ob -> _u('user',array('statusUser' => 1),array('adminUser' => 1) ) this will update status to 1 for all adminUsers
     *
     * @param	$table		string	=>	table name 
	 * 
     * @param	$fileds		array	=>	array of fileds 
	 * 										=> format  array(
	 * 															'field'	=>	'value,
	 * 															'field'	=>	'value,
	 * 														);
	 * 
     * @param	$filters	array	=>	array of conditions
	 * 										=> format  array(
	 * 															'field'	=>	'value,
	 * 															'field'	=>	'value,
	 * 														);
	 * 								=>	empty array, if no conditions
	 * 
     * @return	$return		boolean	=>	false, if no table or fields given or updation fails 
	 * 								=>	true, if updated successfully
     */
    public function _u($table, $fields = array(), $filters = array() ) {

        $query = "update ". $this ->  _table($table). " set ";

		if( empty($table) || qc_empty_array($fields) ){
			
			return false;
			
		}
        
        $updateArray = $whereArray = array();
        
        foreach($fields as $keyField => $valueField){

            $updateArray[] = "`$keyField` = :$keyField ";
            $bind[":$keyField"]= $valueField;

        }
        
        foreach($filters as $keyField => $valueField){

            $whereArray[] = "`$keyField` = :$keyField ";
            $bind[":$keyField"]= $valueField;

        }
        $query .= " ".implode(' , ',$updateArray);
        $query .= " where ".implode(' and ',$whereArray);

        return $this -> _run($query, $bind);

    }
    
    /** 
	 * performs insert action on give table with given fileds
	 * 
	 * Ex: $ob -> _i('user',array('nameUser' => 'Quikc') this will insert new row into the table
     *
     * @param	$table		string	=>	table name 
	 * 
     * @param	$fileds		array	=>	array of fileds 
	 * 										=> format  array(
	 * 															'field'	=>	'value,
	 * 															'field'	=>	'value,
	 * 														);
	 * 
     * @return	$return		int		=>	id of the insert if inserted successfully
	 * 						boolean	=>	false, if no table given or fields given or insertion fails
     */
    public function _i($table, $fileds = array()) {

		if( empty($table) || qc_empty_array($fileds) ){
			
			return false;
			
		}
		            
        $insertKeys  = array();
        $insertValues= array();
        
        foreach($fileds as $keyField => $valueField){

            $insertKeys[] = "`$keyField`";
            $insertValues[] = ":$keyField";
            $bind[":$keyField"]= array( "value" =>  $valueField );

        }   
        
        $query = "insert into ". $this ->  _table($table)." (".implode(",",$insertKeys).") values (".implode(",",$insertValues).")";
        
        if( $this -> _run($query, $bind) ){

            return $this -> _getLastInsertId();

        }
            
        return false;

    }

    /** 
	 * performs delete row action on give table with given filters
	 * 
	 * Ex: $ob -> _d('user',array('idUser' => 1) this will delete the user with id 1
     *
     * @param	$table		string	=>	table name 
	 * 
     * @param	$filters	array	=>	array of filters
	 * 										=> format  array(
	 * 															'field'	=>	'value,
	 * 															'field'	=>	'value,
	 * 														);
	 * 								=>	empty array, if no conditions
	 * 
     * @return	$return		boolean	=>	true, if deleted successfully
	 * 								=>	false, if deletion fails
     */
    public function _d($table, $filters = array()) {

		if( empty($table) ){
			
			return false;
			
		}
            
        $query = "delete from ". $this ->  _table($table)." ";
        $bind = array();
		
		if( !qc_empty_array($filters) ){

	        $whereArray  = array();
        
	        foreach($filters as $keyField => $valueField){
	
	            $whereArray[] = "`$keyField` = :$keyField ";
	            $bind[":$keyField"]= array( "value" =>  $valueField );
	
	        }
			
            $query .= " where ".implode(" and ",$whereArray);
	        
		}
                
        if( $this ->  _run($query, $bind) ){

            return true;

        }
            
        return false;

    }

    /* Short query function ends here
     -----------------------------------------------------------------------------------------------------------------------------------------!>
     */
	 
	/* database log related function starts from here
	 <!-----------------------------------------------------------------------------------------------------------------------------------------
	 */

    /** 
     * prepares and returns the log message
     *
     * @param   $status     object      =>  object instance if query executed successfully
     *                      boolean     =>  false, if query execution fails
     * @param   $query      string      =>  query 
     *                      null        =>  if no query
     * @param   $bind       array       =>  bind values 
     *                                  =>  empty values if no bind values
     * @param   $dbname     string      =>  database name 
     *    
     * @return  $msg        object      =>  object containing database log details
     */
    public function _prepareLogMessage($status , $query = '', $bind = array(), $dbname = '') {

        $msg = new stdClass;
        $msg -> query   = $query;
        $msg -> bind    = $bind;
        $msg -> dbname  = ( $dbname == '' ) ? $this -> _getDatabase() : $dbName;
        $msg -> status  = ( $status ) ? 'success' : 'fail' ;

        return $msg;
        
    }

    /** 
     * logs last database error to the log file
     *
     * @param   $error      string      =>  manually giving the error message
     *                      false       =>  user last error database message 
     * 
     * @return  null
     */
    public function _logError($error = false) {

        if( !$error ){

            // Gets Latest Error
            $error = $this -> _getError();

        }

        error_log($error, 0);

    }
	
	/* database log related function ends here
	 -----------------------------------------------------------------------------------------------------------------------------------------!>
	 */
	 
    /* database class access interface startsd from here
     <!-----------------------------------------------------------------------------------------------------------------------------------------
     */

    /** 
     * try to connect to the database with given details. 
     *
     * @param   $opts   array   =>  array of database options
     *                              type        string  =>  database type required
     *                                                      mysql by default
     *                              host        string  =>  host
     *                              user        string  =>  user name
     *                              pass        string  =>  password
     *                              dbName      string  =>  database name
     * 
     * @return  $return object  =>  pdo connection object if connection can be established
     *                  boolean =>  false, if connection cannot be established
     */ 
    public function _connectDb($opts){
        
        $return = $this -> _getExtension() -> {__FUNCTION__}($opts);

        return $return;
        
    }

    /** 
     * fetches single row from database
     *
     * @param   $query      string  =>  query to be fetched 
     * @param   $bind       array   =>  array of bind value
     * @param   $dbName     string  =>  database on which operation has to perform 
     * 
     * @return  $return     boolean =>  false, if query execution fails or no results
     *                      object  =>  database row for fetch
     */
    public function _getRow($query, $bind = array(), $dbName = ''){
        
        $return = $this -> _getExtension() -> {__FUNCTION__}($query, $bind, $dbName);
        
        return $return;
        
    }

    /** 
     * fetches all rows from database
     *
     * @param   $query      string  =>  query to be fetched 
     * @param   $bind       array   =>  array of bind value
     *                              =>  empty array, if no bind values
     * @param   $dbName     string  =>  database on which operation has to perform 
     *                      boolean =>  empty, if no database
     * 
     * @return  $return     boolean =>  false, if query execution fails
     *                      array   =>  database row for fetch
     */
    public function _getAllRows($query, $bind = array(), $dbName = ''){
        
        $return = $this -> _getExtension() -> {__FUNCTION__}($query, $bind, $dbName);
        
        return $return;
        
    }
    
    /** 
     * returns row count for given query
     *
     * @param   $query      string  =>  query  
     * @param   $bind       array   =>  array of bind value
     *                              =>  empty array, if no bind values
     * @param   $dbName     string  =>  database on which operation has to perform 
     *                      boolean =>  empty, if no database
     * 
     * @return  $count      boolean =>  false, if query execution fails
     *                      int     =>  no of rows
     */
    public function _getRowCount($query, $bind = array(), $dbName = ''){
        
        $return = $this -> _getExtension() -> {__FUNCTION__}($query, $bind, $dbName);
        
        return $return;
        
    }

    /** 
     * executes given database query
     *
     * @param   $query      string  =>  query to be executed 
     * @param   $bind       array   =>  array of bind value
     *                              =>  empty array, if no bind values
     * @param   $dbName     string  =>  database on which operation has to perform 
     *                      boolean =>  empty, if no database
     * 
     * @return  $return     boolean =>  false, if query execution fails
     *                              =>  true, if execution success
     */ 
    public function _run($query, $bind = array(), $dbName = ''){
        
        $return = $this -> _getExtension() -> {__FUNCTION__}($query, $bind, $dbName);
        
        return $return;
        
    }

    /** 
     * returns last insert row id
     *
     * @param   $dbName     string  =>  database name
     *                      boolean =>  empty, to use working database

     * @return  $return     int     =>  last insert id
     *                      boolean =>  false if last insert id, doesn't exists
     */
     public function _getLastInsertId($dbName = ''){
         
        return  $this -> _getExtension() -> {__FUNCTION__}($dbName);

     }
        
    /** 
     * returns last error message
     *
     * @param   void
     * 
     * @return  $return     mixed   =>  error message
     */
    public function _getError($dbName = '') {

        return $this -> _getExtension() -> {__FUNCTION__}($dbName);

    }
    /* database class access interface ends here
     -----------------------------------------------------------------------------------------------------------------------------------------!>
     */


}
