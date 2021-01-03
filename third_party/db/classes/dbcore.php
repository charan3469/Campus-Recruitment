<?php

/**
 * db core abstract class, contains basic database related functions 
 *
 * @version 1.0
 * @http://www.quikc.org/
 */
abstract class dbCore {

    /** 
	 * Contains active database name
     *
     * @var string
     */
    private static $activeDb = null;

    /** 
	 * returns the value of the given key
     *
     * @param	$key	string	=>	key for which value has to be return
	 *
     * @return 	$val	string	=>	if key exists
	 * 					boolean	=>	false if key doesnot exists
     */
    public function _get($key) {
    	
		global $db_config;
        
        $keys = array('db.type', 'db.host', 'db.user', 'db.pass', 'db.name','db.ext','table.prefix');

        if ( in_array($key, $keys) ){

            $val = $db_config[$key];
            
        }else{

            $val = false;
            
        }

        return $val;

    }

	/** 
	 * updates given database as active database
     *
     * @param	$dbName	string	=>	database name
	 * 					empty	=>	sets default database as active database
     * @return 	null
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
     * @param	void
	 * 
     * @return	$return		string	=>	active database name 
     */
    public function _getDatabase() {

        if ( is_null(self::$activeDb) ){

            self::_setDatabase();
            
        }

        return self::$activeDb;

    }

	/* table related functions like get tables, check tables, check field, system tables starts from here
	 <!-----------------------------------------------------------------------------------------------------------------------------------------
	 */

    /** 
	 * Returns list of system tables
     *
     * @param	void
	 * 
     * @return	$return		array 	=>	list of system tables
     */
    public function _systemTables() {

    	$tabs = array(   
                    );

        return $tabs;

    }
	
    /** 
	 * returns system tables
     *
     * @param	$prefix		boolean		=>	if true, returns tables with table prefix
	 * 									=>	if false, returns tables without table prefix 
	 * 
     * @return	$tables		array 		=>	list of system tables
     */
    public function _getSystemTables( $prefix = true ) {

        $tables = $this -> _systemTables();
        
        if( $prefix ){
            
            foreach ($tables as $key => $table ) {
    
                $tables[$key] = $this -> _table($table);
    
            }
            
        }

        return $tables;

    }
	
    /** 
	 * checks weather given table is system table or not
     *
     * @param	$table		string		=>	name of the table to verify
	 * 
     * @return	$return		boolean 	=>	true, if given table is a system table
	 * 										false, if given table is not a system table
     */
    public function _isSystemTable( $table ) {

        $tables = $this -> _getSystemTables( false );
        
        if( in_array( $table, $tables ) ){

			return true;
            
        }

        return false;

    }
	
	/** 
	 * returns list of tables current database have
     *
     * @param	void
	 * 
     * @return	$tables		array 	=>	array of tables
     */
    public  function _getTables($includeSystem = false) {

        $query = "show tables from " . $this -> _getDatabase();
        $list = $this ->_getAllRows($query);

        foreach ($list as $tmp) {

            $table = 'Tables_in_' . $this -> _getDatabase();
			
			if ( $includeSystem || !$this -> _isSystemTable($tmp -> $table)) {

	        	$tables[] = $tmp -> $table;

			}

        }

        return $tables;

    }

    /** 
	 * checks weather given table exists in the database or not
     *
     * @param	$table	string	=>	table to verify
	 * 
     * @return	$return	boolean	=>	true if table exists
	 * 								false if table doesn't exists
     */
    public function _checkTable($table) {

		$table = $this -> _table($table);

        $query = "show tables like '$table'";

        if ( $this -> _getRow($query) ) {

            return true;

        } else {

            return false;

        }

    }

    /** 
	 * returns list of columns of a table or details of a particular column
     *
     * @param	$table	string	=>	table in which colum has to get
     * @param	$column	boolean	=>	if false returns details of all columns
	 * 					string	=>	column name
	 *  
     * @return	$list	array	=>	if $column is false, returns list of column details
	 * 					object	=>	if $column is given, returns details of the column
     */
    public function _getColumns($table, $column = false) {

		$table = $this -> _table($table);

        $query = "show columns from `" . $table . "`";
        
        if($column){

            $query .= " like '$column'";

            $list = $this -> _getRow($query);

        }else{

            $list = $this -> _getAllRows($query);

        }

        return $list;

    }

    /** 
	 * checks weather a column exists in table or not 
     *
     * @param	$table	string	=>	table in which colum has to verify
     * @param	$column	string	=>	column name to verify
	 *  
     * @return	$return	boolean	=>	false, if columns does not exists
	 * 								true, if columns does not exists
     */
    public function _checkColumn($table, $column) {

        // Checking weather the table exists or not
        if ( $this -> _getColumns($table, $column) ) {

            return true;

        }
        
        return false;
        
    }

    /**
    * returns primary field of the table
    *
    * @param	$table	string	=>	table in which primary key has to return
	* 
    * @return 	$return	string	=>	name of the primary field if exists	
	* 					null	=>	if primary field doesnot exists
    */
    public function _getPrimary($table){

        $query = "show index from ". $this ->  _table($table);
        $keys  = $this -> _getAllRows($query);

        foreach($keys as $key){

            if( strtolower($key->Key_name) == 'primary' ){

                return $key->Column_name;

            }

        }       
        
        return null;
        
    }
	
    /**
    * adds prefix to the field
    *
    * @param	$field	string	=>	field name
    * @param	$prefix	string	=>	prefix to be added
	* 					boolean	=>	false, if no prefix to be added
	*  
    * @return 	$return	string	=>	if $prefix is given, prefix added field
	* 							=>	if #prefix is false, unmodified field
    */
    public function _addFieldPrefix($field, $prefix = false) {

        if( $prefix ){
        	
			$field = "$prefix.$field";
			
        }

        return $field;

    }

    /**
    * returns prefix from given  string
    *
    * @param	$field	string	=>	string for which prefix has to return
	* 
    * @return 	$return	string	=>	prefix of the string	
	* 					boolean	=>	false if no prefix exists
    */
    public function _getFieldPrefix($field) {

        $parts = explode(".", $field);
		
		$return = false;
		
		if( count($parts) > 1 ){
			
			$return = $parts[0];
		}

        return $return;

    }

    /**
    * returns field name from given string
    *
    * @param	$field	string	=>	string for which field has to return
	* 
    * @return 	$return	string	=>	field of the string
    */
    public function _getField($field) {

        $parts = explode(".", $field);
        array_shift($parts);

        return implode(".", $parts);

    }

    /**
	 * returns table prefix
     *
     * @param	void
	 * 
     * @return	$return		string		=>	table prefix
     */
    public function _getTableprefix() {

        return $this -> _get('table.prefix');

    }

    /**
	 * returns table prefix prepended table name
     *
     * @param	$table		string		=>	table name
	 * 
     * @return	$return		string		=>	table prefix prepended table name
     */
	public function _table($table) {

		return $this -> _getTableprefix() . $table;

	}
	
    /**
	 * returns table without prefix in the given string
     *
     * @param	$str		string		=>	table name with prefix
	 * 
     * @return	$return		string		=>	table name without prefix
     */
	public function _removePrefix($str) {

		return qc_replace_prefix( $str , $this -> _getTableprefix() );

	}
	

}
