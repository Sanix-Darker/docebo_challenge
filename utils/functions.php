<?php

/**
 *
 */
function check_node_id($node_id){
    global $db;

    $query = "SELECT COUNT(*) as node_count FROM node_tree WHERE idNode=".$node_id;
    $req = $db->query($query);
    $row = $req->fetch();
    return ($row["node_count"] > 0) ? true: false;
}

/**
 *
 */
function is_page_size_valid($page_size){
    global $result;
    $result['nodes'] = [];

    if (is_numeric($page_size) && (int)$page_size <= 1000 && (int)$page_size >= 0)
        return True;

    $result['error'] = "Invalid page size requested";
    return False;
}

/**
 *
 */
function is_page_num_valid($page_num){
    global $result;
    $result['nodes'] = [];

    if (is_numeric($page_num) && (int)$page_num >= 0)
        return True;

    $result['error'] = "Invalid page number requested";
    return False;
}

/**
 *
 */
function check_required_params(){
    global $result;
    $result['nodes'] = [];

    $required_params = array("node_id", "language");
    foreach ($required_params as &$value) {
        if (!isset($_GET[$value]) || empty($_GET[$value])){
            $result['error'] = "Missing mandatory params";

            return False;
        }
    }

    if(check_node_id($_GET["node_id"]))
        return true;
    else{
        $result['error'] = "Invalid node id";
        return false;
    }
}

/**
 *
 */
function fetch_results($query, $search_keyword, $language){
    global $result;

    $result['nodes'] = [];

    try {
        global $db;

        $req = $db->query($query);
        while($row = $req->fetch())
        {
            foreach ($row as $key => $value) {
                if (is_int($key) || $key =="depth") {
                    unset($row[$key]);
                }
            }
            $row["children"] = count_children($row["idNode"], $search_keyword, $language);
            $row["node_id"] = $row["idNode"];
            $row["name"] = $row["nodeName"];
            unset($row["nodeName"]);
            unset($row["idNode"]);
            unset($row["language"]);

            $result['nodes'][] = $row;
        }
    } catch(PDOException $e) {
        $msg = $e->getMessage();
        $result['error'] = $msg;
    }
}