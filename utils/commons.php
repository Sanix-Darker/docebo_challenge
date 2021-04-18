<?php
/**
 * commons functions
 *
 * All common functions that will be used for the core
 */

 // We include some necessary functions
include_once("./utils/helpers.php");
include_once("./utils/queries.php");


/**
 * check_node_id
 *
 * This function will check if a node_id exist in the db
 * by doing a simple select-where.
 * And return if yes or no (true/false) the node_id exist
 *
 * @param {*} $node_id
 *
 * @returns {boolean}
 */
function check_node_id($node_id){
    global $db;

    $query = "SELECT COUNT(*) as node_count FROM node_tree WHERE idNode=".$node_id;
    $req = $db->query($query);
    $row = $req->fetch();

    return ($row["node_count"] > 0) ? true: false;
}


/**
 * format_row
 *
 * This function will format the row input and return
 * a nice row as we wanted with keys we want
 *
 * @param {*} $row
 * @param {*} $search_keyword
 * @param {*} $language
 *
 * @returns {array} - the formatted row
 */
function format_row($row, $search_keyword, $language){
    // just to remove numeric index
    foreach ($row as $key => $value)
        if (is_int($key) || $key =="depth")
            unset($row[$key]);

    // we count children
    $row["children"] = count_children($row["idNode"], $search_keyword, $language);

    // we format stuffs and remove all keys
    $row["node_id"] = (int)$row["idNode"];
    $row["name"] = $row["nodeName"];
    unset($row["nodeName"]);
    unset($row["idNode"]);
    unset($row["language"]);

    return $row;
}


/**
 * fetch_results
 *
 * In a try-catch, this function will generate the appropriate sql-query
 * and then fetch results from the database
 *
 * @param {*} $node_id - the node id, where we at
 * @param {*} $language - the language we requesting for
 * @param {*} $search_keyword - the search key
 * @param {*} $page_num - the page number
 * @param {*} $page_size - the page size
 *
 * @returns {void}
 */
function fetch_results($node_id, $language, $search_keyword, $page_num, $page_size){
    global $result, $db;

    try {
        // We build the query as a string
        $query = generate_query($node_id, $language, $search_keyword, $page_num, $page_size);
        // we make the query
        $req = $db->query($query);
        while($row = $req->fetch())
            // We format the row before appending to node array
            $result['nodes'][] = format_row($row, $search_keyword, $language);

    } catch(PDOException $e) {
        $msg = $e->getMessage();
        $result['error'] = $msg;
    }
}
