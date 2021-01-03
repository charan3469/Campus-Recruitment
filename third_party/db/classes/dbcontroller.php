<?php

/**
 * abstract db controller class. 
 * contains function definitions for database extensions 
 *
 * @version 1.0
 * @http://www.quikc.org/
 */
abstract class dbController extends dbCore {

    /** 
     * Contains active database name
     *
     * @var string
     */
    private static $activeDb = null;
    
    /** 
     * Contains list of database objects
     *
     * @var array of objects
     */
    private static $dbObjects = array();

    /** 
     * updates given database as active database
     *
     * @param   $dbName string  =>  database name
     *                  empty   =>  sets default database as active database
     * @return  null
     */
    public function _setDatabase($dbName = '') {

        if ( empty($dbName) ){

            $dbName = $this->_get('db.name');
            
        }

        self::$activeDb = $dbName;

    }

    /** 
     * returns active database
     *
     * @param   void
     * 
     * @return  $return     string  =>  active database name 
     */
    public function _getDatabase() {

        if ( is_null(self::$activeDb) ){

            self::_setDatabase();
            
        }

        return self::$activeDb;

    }

    /** 
     * returns object of the given database
     *
     * @param   $dbName     string  =>  database name
     *                      empty   =>  uses active database
     * 
     * @return  $return     object  =>  database instance
     */
    protected function getObject($dbName = '') {

        if ( empty($dbName) ){

            $dbName = self::_getDatabase();
            
        }

        // Checking for the existing instances
        if ( !isset(self::$dbObjects[$dbName]) ) {

            $inst = $this -> _connectDb( array('dbName' => $dbName) );
            
            if( $inst ){
                
                self::$dbObjects[$dbName] = $inst;
                
            }else{
                
                die("Failed to establish database connection $dbName");
                
            }

        }

        return self::$dbObjects[$dbName];

    }
    
    /** 
	 * try to connect to the database with given details. 
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
    abstract public function _connectDb($opts);

    /** 
	 * fetches single row from database
     *
     * @param	$query		string	=>	query to be fetched 
     * @param	$bind	    array	=>	array of bind value
     * @param	$dbName		string	=>	database on which operation has to perform 
	 * 
     * @return	$return		boolean	=>	false, if query execution fails or no results
	 * 						object	=>	database row for fetch
     */
    abstract public function _getRow($query, $bind = array(), $dbName = '');

    /** 
	 * fetches all rows from database
     *
     * @param	$query		string	=>	query to be fetched 
     * @param	$bind	    array	=>	array of bind value
	 * 								=>	empty array, if no bind values
     * @param	$dbName		string	=>	database on which operation has to perform 
	 * 						boolean	=>	empty, if no database
	 * 
     * @return	$return		boolean	=>	false, if query execution fails
	 * 						array	=>	database row for fetch
     */
    abstract public function _getAllRows($query, $bind = array(), $dbName = '');
	
    /** 
	 * returns row count for given query
     *
     * @param	$query		string	=>	query  
     * @param	$bind	    array	=>	array of bind value
	 * 								=>	empty array, if no bind values
     * @param	$dbName		string	=>	database on which operation has to perform 
	 * 						boolean	=>	empty, if no database
	 * 
     * @return	$count		boolean	=>	false, if query execution fails
	 * 						int		=>	no of rows
     */
    abstract public function _getRowCount($query, $bind = array(), $dbName = '');

    /** 
	 * executes given database query
     *
     * @param	$query		string	=>	query to be executed 
     * @param	$bind	    array	=>	array of bind value
	 * 								=>	empty array, if no bind values
     * @param	$dbName		string	=>	database on which operation has to perform 
	 * 						boolean	=>	empty, if no database
	 * 
     * @return	$return		boolean	=>	false, if query execution fails
	 * 								=>	true, if execution success
     */	
    abstract public function _run($query, $bind = array(), $dbName = '');

    /** 
	 * returns last insert row id
     *
     * @param	$dbName		string	=>	database name
	 * 						empty	=>	to use working database
     * 
	 * @return	$return		int		=>	last insert id
	 * 						boolean	=>	false if last insert id, doesn't exists
     */
     abstract public function _getLastInsertId($dbName = '');

    /** 
     * returns last error message
     *
     * @param   $dbName     string  =>  database name
     *                      empty   =>  to use working database
     * 
     * @return  $return     mixed   =>  error message
     */
    abstract public function _getError($dbName = '');
		
}


