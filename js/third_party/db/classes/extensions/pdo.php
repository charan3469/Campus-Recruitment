<?php

/**
 * pdo extension class for core database class
 * this class conatins actions related to performing queries using pdos
 *
 * @version 1.0
 * @http://www.quikc.org/
 */
class DbExtensionPdo extends dbController {

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
    public function _connectDb($opts) {

		$type 	= isset($opts['type']) 	? $opts['type'] : parent::_get('db.type');
		$host 	= isset($opts['host']) 	? $opts['host'] : parent::_get('db.host');
		$user 	= isset($opts['user']) 	? $opts['user'] : parent::_get('db.user');
		$pass 	= isset($opts['pass']) 	? $opts['pass'] : parent::_get('db.pass');
		$dbName	= isset($opts['dbName'])? $opts['dbName'] : parent::_getDatabase();

        // Gathering the database variables
        $dsn = $type . ':host=' . $host . ';dbname=' . $dbName .
        //     ';port='      . Config::_get('db.port') .
        ';connect_timeout=15';

        // trying to establish new connection with the 
        try {

            $dbh = new PDO($dsn, $user, $pass);
            $dbh -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			// returning generated pdo object
            return $dbh;

        } catch (PDOException $e) {

			// returning false since connection fails
            return false;
        }
        
    }

    /** 
	 * executes given query
     *
     * @param	$query		string	=>	query to be executed
	 * 			$bind       array 	=>	array bind values
	 * 			$dbName		string 	=>	database in which the query has to be executed
	 * 
     * @return 	$return		object	=>	pdo database object of the executed query
	 * 						boolean	=>	false, if query execution fails
     */
    private function _execute($query, $bind , $dbName ) {

        // getting database instance
        $inst = parent::getObject($dbName);

        // Prepaing the query for execution
        $stmt = $inst -> prepare($query);

        if (!$stmt) {

            //parent::_logError();
            return false;

        }

		// values has to be stored array otherwise all previous bind values will be overwritten with earlier values
		$val = array();
		
        // Binding valid bind values to the database
        if ( !qc_empty_array($bind) ) {

            foreach ($bind as $k => $b) {

				if (is_array($b))
				{

					$val[$k] = $b['value'];

				}
				else
				{
					$val[$k] = $b;
				}

				if ( is_array($b) && isset($b['type']) && $b['type'] != '')
				{
					// Binding values with type. Types like int, string etc
					// pdo take arguments by reference
					// so to follow strict standards, we need to pass variable, not return values
					// here $val is saved to array variables, if stored as string, last value will be overridden in pass by reference
					$stmt -> bindParam($k, $val[$k], $b['type']);

				}
				else
				{
					// Binding values without type
					// pdo take arguments by reference
					// so to follo strict standards, we need to pass variable, not return values
					// here $val is saved to array variables, if stored as string, last value will be overridden in pass by reference
					$stmt -> bindParam($k, $val[$k]);

				}
            }

        }

        // Executing the query
        if ($stmt -> execute()) {

            // Return the statement if the query executed
            return $stmt;

        } else {

            //parent::_logError();
            // Return false if query not executed
            return false;

        }

    }

    /** 
	 * fetches results from the valid database statements
     *
     * @param	$getType	string	=>	type of the fetch like 
	 * 										fetch	=>	to fetch single row
	 * 										fetchall=>	to fetch all rows 
     * @param	$query		string	=>	query to be fetched 
     * @param	$bind	    array	=>	array of bind value
     * @param	$dbName		string	=>	database on which operation has to perform 
	 * 
     * @return	$results	boolean	=>	false, if query execution fails or no results
	 * 						object	=>	database row for fetch
	 * 						array	=>	array of database rows for fetchall
     */
    private function _getFetch($getType, $query, $bind, $dbName) {

        // Executing the database query
        $stmt = $this -> _execute($query, $bind, $dbName);

        if ($stmt) {

            // Fetcing the results for the valid statements
            $results = $stmt -> $getType(PDO::FETCH_OBJ);

        } else {

            // Returns false if not a valid statement
            $results = false;

        }

        return $results;

    }

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
    public function _getRow($query, $bind = array(), $dbName = '') {

        return $this -> _getFetch("fetch", $query, $bind, $dbName);

    }

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
    public function _getAllRows($query, $bind = array(), $dbName = '') {

        return $this -> _getFetch("fetchAll", $query, $bind, $dbName);

    }

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
    public function _getRowCount($query, $bind = array(), $dbName = '') {

        $count = 0;
        $statusCount = false;

        $query_exploded = explode(" from ", $query);

        $fromCount = count($query_exploded);

        if ( $fromCount > 1) {

            $statusCount = true;
            
            unset($query_exploded[0]);
            $query = 'select count(*) as row_count from ' . implode(" from ", $query_exploded);

            $result = $this -> _getRow($query, $bind, $dbName);

            if (isset($result -> row_count)){

                $count = $result -> row_count;
                
            }else if( $fromCount > 2 ){
                
                $statusCount = true;
            
            }

        } 
        
        if( !$statusCount ){

            $result = $this -> _getAllRows($query, $bind);
            $count  = count($result);

        }

        return $count;

    }

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
    public function _run($query, $bind = array(), $dbName = '') {

        // Executint the query
        $stmt = $this -> _execute($query, $bind, $dbName);

        if ($stmt) {

            // Returning true, if the executing completed successfully
            return true;
            //$stmt->lastInsertId();

        } else {

            // Returning false, if the executing not completed successfully
            return false;

        }

    }

    /** 
	 * returns last insert row id
     *
     * @param	$dbName		string	=>	database name
	 * 						boolean	=>	empty, to use working database

	 * @return	$return		int		=>	last insert id
	 * 						boolean	=>	false if last insert id, doesn't exists
     */
    public function _getLastInsertId($dbName = '') {

        return parent::getObject($dbName) -> lastInsertId();

    }

    /** 
     * returns last error message
     *
     * @param   $dbName     string  =>  database name
     *                      boolean =>  empty, to use working database
     * 
     * @return  $return     mixed   =>  error message
     */
    public function _getError($dbName = '') {
        
        $return = parent::getObject($dbName) -> errorInfo();
        
        return isset($return[2]) ? $return[2] : false;

    }


}
