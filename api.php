<?php

// We set some header informations
// For the return
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Cache-Control: private");
header("Pragma: no-cache");
header('Content-Type:text/json; Charset=UTF-8');

// include some files
include_once("./config.php");
include_once("./utils/commons.php");


// the global variable for the result
$result = array();
$result['nodes'] = [];


/**
 * check_node_id
 * This function will check if a node_id exist in the db
 * by doing a simple select-where.
 * And return if yes or no (true/false) the node_id exist
 *
 * @param {*} $node_id
 * @returns {boolean}
 */
function main(){
    global $result;

    // security check
    if (check_required_params()){
        // required parameters
        $node_id = $_GET["node_id"];
        $language = $_GET["language"];

        // not required parameters... using ternary clause with defaults values
        $page_num = (!isset($_GET["page_num"])) ? 0 : $_GET["page_num"];
        $page_size = (!isset($_GET["page_size"])) ? 100 : $_GET["page_size"];
        $search_keyword = (!isset($_GET["search_keyword"])) ? "" : $_GET["search_keyword"];

        // We validate pages before proceed the fetch
        if (valid_pages($page_size, $page_num))
            // we fetch results
            fetch_results($node_id, $language, $search_keyword, $page_num, $page_size);
    }
    // We echo the json version of the result's array
    print(json_encode($result));
}

main();