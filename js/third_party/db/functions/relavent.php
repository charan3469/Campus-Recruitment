<?php

/**
 * checks weather given input is array or not, and is empty or not
 *
 * @param   $array 	mixed  	=>	input which has to be checekd
 * 
 * @return  $return	boolean =>	true, if not an array or empty	
 *							=>	false, if array and not empty  			
 */
 //TODO has to replace with validator
function qc_empty_array($array){

	$return = true;

	if( is_array($array) && count($array) > 0){
		
		$return = false;
		
	}
	
    return $return;
}

/**
 * returns last key of given array
 *
 * @param   $array  array   =>  input array
 * 
 * @return  $return mixed   =>  int or string.. the key of last element
 *                  boolean =>  false, if not array or empty            
 */
function qc_array_first_key($array){

    $return = false;

    if( !qc_empty_array($array) ){

        reset($array);
        $return = key($array);
        
    }

    return $return;
}

/**
 * returns last value of given array
 *
 * @param   $array  array   =>  input array 
 * 
 * @return  $return mixed   =>  int or string.. the key of last element
 *                  boolean =>  false, if not array or empty            
 */
function qc_array_first_val($array){

    $return = false;

    if( !qc_empty_array($array) ){

        $return = reset($array);
        
    }
    
    return $return;
}

/**
 * returns last key of given array
 *
 * @param   $array  array   =>  input array
 * 
 * @return  $return mixed   =>  int or string.. the key of last element
 *                  boolean =>  false, if not array or empty            
 */
function qc_array_last_key($array){

    $return = false;

    if( !qc_empty_array($array) ){

        end($array);
        $return = key($array);
        
    }

    return $return;
}

/**
 * returns last value of given array
 *
 * @param   $array 	array  	=>	input array 
 * 
 * @return  $return	mixed 	=>	int or string.. the key of last element
 *					boolean	=>	false, if not array or empty  			
 */
function qc_array_last_val($array){

	$return = false;

	if( !qc_empty_array($array) ){

		$return = end($array);
		
	}
	
    return $return;
}

/**
 * replaces prefix in given string
 *
 * @param 	$str	string	=>	string which we have to repalce
 * @param 	$pre	string	=>	string to be replaced
 * 
 * @return	$str	string	=>	replaced string
 */
function qc_replace_prefix($str, $pre) {

	$len = strlen($pre);

	if( substr($str, 0, $len) == $pre ) {
		
	    $str = substr($str, $len);
		
	} 
	
    return $str;

}
