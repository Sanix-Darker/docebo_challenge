<?php
/**
 *
 *
 */
include_once("./utils/helpers.php");
include_once("./utils/queries.php");


/**
 * check_node_id
 * This function will check if a node_id exist in the db
 * by doing a simple select-where.
 * And return if yes or no (true/false) the node_id exist
 *
 * @param {*} $node_id
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
 * check_node_id
 * This function will check if a node_id exist in the db
 * by doing a simple select-where.
 * And return if yes or no (true/false) the node_id exist
 *
 * @param {*} $node_id
 * @returns {boolean}
 */
function format_row($row, $search_keyword, $language){
    foreach ($row as $key => $value)
        if (is_int($key) || $key =="depth")
            unset($row[$key]);

    $row["children"] = count_children($row["idNode"], $search_keyword, $language);
    $row["node_id"] = (int)$row["idNode"];
    $row["name"] = $row["nodeName"];
    unset($row["nodeName"]);
    unset($row["idNode"]);
    unset($row["language"]);

    return $row;
}


/**
 * check_node_id
 * This function will check if a node_id exist in the db
 * by doing a simple select-where.
 * And return if yes or no (true/false) the node_id exist
 *
 * @param {*} $node_id
 * @returns {boolean}
 */
function fetch_results($node_id, $language, $search_keyword, $page_num, $page_size){
    global $result, $db;

    try {
        // We build the query as a string
        $query = generate_query($node_id, $language, $search_keyword, $page_num, $page_size);
        // we make the query
        $req = $db->query($query);
        while($row = $req->fetch())
            $result['nodes'][] = format_row($row, $search_keyword, $language);

    } catch(PDOException $e) {
        $msg = $e->getMessage();
        $result['error'] = $msg;
    }
}
