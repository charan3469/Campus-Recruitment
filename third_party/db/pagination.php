<?php
//include "init.php";

/**
 * Generates Pagination for the given input values
 * Generates the total no of records
 * Lower limit, limit and these can be later used to get the records from the database using these as limits
 * Displaying records starting and ending count
 * verified and modified current page
 * List of pages to be loaded after this
 * 
 *
 * @param  $options     array   =>  contains the list of options
 *                                      $query          string  =>  data base query 
 *                                      $bind           array   =>  array bind values 
 *                                      $count          boolean =>  if true, then count is retrieved through db_row by count keyword  
 *                                      $currentpage    int     =>  current page to be validated and verified
 *                                      $perpage        int     =>  No of records to be displayed per page 
 *                                      $display        int     =>  No of pages to be displayed per page 
 *                                      $tp             string  =>  Text to be displayed for previous  
 *                                      $tn             string  =>  Text to be displayed for next
 * 
 * @return $pagination  array   =>  Generated pagination with keys
 *                                      TR      => Total no of records
 *                                      CP      => verified and modified current page 
 *                                      PP      => verified and modified records per page value
 *                                      LL      => Database query lower limit to get current page records
 *                                      LT      => Database query limit value, this contains the no of records to be loads from the LL 
 *                                      DF      => Starting counter of the records displaying
 *                                      DT      => Ending counter of the records displaying
 *                                      pages   => list of the pages that can be displayed in the pagination
 *    
 */
/*$options = array(
    "query" => "select * from users",
    "cp" => $_GET['cp']
);

$pag = _generatePagination($options);*/

function _generatePagination($options) {

    if (!isset($options['query'])) {

        return false;
    }

    // check the fields and updating the default values if not set
    $query = $options['query'];
    $bind = isset($options['bind']) ? $options['bind'] : array();
	$count = isset($options['count']) ? $options['count'] : false;
	//print_r($count);
    $cp = isset($options['cp']) ? $options['cp'] : 1;
    $pp = isset($options['pp']) ? $options['pp'] : 10;
    $dp = isset($options['dp']) ? $options['dp'] : 5;
    $textPrev = isset($options['tp']) ? $options['tp'] : ('Previous');
    $textNext = isset($options['tn']) ? $options['tn'] : ('Next');

    // converting the current page to interger 
    // and if the value is zero then updatig the values as 1
    if ((int) $cp == 0) {

        $cp = 1;
    }

    // converting the per page to interger 
    // and if the value is zero then updatig the values as 1
    if ((int) $pp <= 0) {

        $pp = 10;
    }

    // Fetching the no of results
    if ($count) {
	
        $result = db_row($query, $bind['ab'], $bind['db']);
        $numRows = isset($result->count) ? $result->count : 0;
    } else {

        $numRows = db_count($query, $bind['ab'],$bind['db']);
    }

    // Calculating the total no of pages.
    $totalpages = ceil($numRows / $pp);

    // Validating the lower boundaries of $currentpage
    if ($cp < 1) {

        $cp = 1;
    }

    // Validating the upper boundaaries of $currentpage
    if ($cp > $totalpages) {

        $cp = $totalpages;
    }

    // Calculating the lower limit of pages to display. First page starts from $LL
    $LL = $cp - $dp;

    // Validating case where display is greater than or equal to current pages
    // Calculating the upper limit of pages to display. Last page ends at $UL
    if ($LL < 1) {

        $UL = $cp + $dp - ($cp - $dp) + 1;
        $LL = 1;
    } else {

        $UL = $cp + $dp;
    }

    // Validating case where display is greater total pages
    if ($UL > $totalpages) {

        $LL = $cp - $dp - ($cp + $dp) + $totalpages;
        $UL = $totalpages;
    }

    // Final validation of Lower Limit of pages
    if ($LL < 1) {

        $LL = 1;
    }

    // Pages generation starts here
    $pages = array();

    // Generating previous page
    if ($cp > 1) {

        $tmp_page['key'] = $cp - 1;
        $tmp_page['value'] = $textPrev;
        $pages[] = $tmp_page;
    }

    // Generating pages
    for ($i = $LL; $i <= $UL; $i++) {

        $tmp['key'] = $i;
        $tmp['value'] = $i;
        $pages[] = $tmp;
    }

    // Generating Next page
    if ($cp < $totalpages) {

        $tmp_page['key'] = $cp + 1;
        $tmp_page['value'] = $textNext;
        $pages[] = $tmp_page;
    }

    // This will be total no of pages
    $pagination['tr'] = $numRows;
    // Current page
    $pagination['cp'] = $cp;
    // No of records Per Page
    $pagination['pp'] = $pp;
    // Generated list of pages
    $pagination['dp'] = $dp;
    // Calculating Lower Limit. This will be used as the lower limit in generating database queries
    $pagination['ll'] = ($cp == 0 ) ? 0 : ( ($cp - 1) * $pp );
    // Calculating Limit. This will be used as the limit in generating database queries
    $pagination['lt'] = $pp;
    // Calculating Display From. This will be used to display the display from count
    $pagination['df'] = ($numRows == 0) ? 0 : ($pagination['ll'] + 1);
    // Calculating Display To. This will be used to display the display to count
    $pagination['dt'] = (($pagination['ll'] + $pagination['lt']) > $numRows) ? $numRows : ($pagination['ll'] + $pagination['lt']);
    // Generated list of pages
    $pagination['pages'] = $pages;

    return $pagination;
}