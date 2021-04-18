<?php
/**
 * helpers.php
 *
 * All functions helpers for commons, queries and core
 */


/**
 * is_page_size_valid
 *
 * This function will check if page_size is numeric and between the valid range
 * (if page_size is inside the validity range)
 * and return yes or no (true/false) depending that
 *
 * @param {*} $page_size
 *
 * @returns {boolean}
 */
function is_page_size_valid($page_size){
    global $result;

    if (is_numeric($page_size) && (int)$page_size <= 1000 && (int)$page_size >= 0)
        return True;

    // we set the appropriate error message
    $result['error'] = "Invalid page size requested";
    return False;
}


/**
 * is_page_num_valid
 *
 * This function will check if the page_num is numeric and > 0
 * (if page_num is a valid 0-based index)
 * and return yes or no (true/false) depending that
 *
 * @param {*} $page_num
 *
 * @returns {boolean}
 */
function is_page_num_valid($page_num){
    global $result;

    if (is_numeric($page_num) && (int)$page_num >= 0)
        return True;

    // we set the appropriate error message
    $result['error'] = "Invalid page number requested";
    return False;
}


/**
 * valid_pages
 *
 * This function will check all conditions relatives to
 * page_num and page_size
 *
 * @param {*} $page_size
 * @param {*} $page_num
 *
 * @returns {boolean}
 */
function valid_pages($page_size, $page_num){
    if (is_page_num_valid($page_num))
        // A ternary for the check on page_size
        return (is_page_size_valid($page_size)) ? true : false;
    else
        return false;
}


/**
 * check_required_params
 *
 * This function will check required-parameters
 * and verify if the a node_id exist
 * the return if yes or no (true/false), it's ok to proceed
 *
 * @returns {boolean}
 */
function check_required_params(){
    global $result;

    $required_params = array("node_id", "language");
    // We loop over required params and if all are present and not empty
    foreach ($required_params as &$value)
        if (!isset($_GET[$value]) || empty($_GET[$value])){
            $result['error'] = "Missing mandatory params";
            return False;
        }

    // We check the node_id in the GET
    if(check_node_id($_GET["node_id"]))
        return true;
    else{
        $result['error'] = "Invalid node id";
        return false;
    }
}