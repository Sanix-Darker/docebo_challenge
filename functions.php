<?php

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
    if(check_node_id($_GET["node_id"])){
        return true;
    }else{
        $result['error'] = "Invalid node id";
    }
}

/**
 *
 */
function get__query($language){
    $query = "
        (SELECT nodeName FROM node_tree_names
            WHERE node.idNode = node_tree_names.idNode
                    AND node_tree_names.language=\"".$language."\") AS nodeName, ";
    return $query;
}

function sub_tree_query($node_id){
    return "
        SELECT node.idNode, (COUNT(parent.idNode) - 1) AS depth
        FROM node_tree AS node, node_tree AS parent
        WHERE node.iLeft BETWEEN parent.iLeft
            AND parent.iRight
            AND node.idNode = ".$node_id."
        GROUP BY node.idNode
        ORDER BY node.iLeft
    ";
}

/**
 *
 */
function generate_query($node_id, $language, $search_keyword, $page_num, $page_size){
    global $result;

    $sql_query = "
        SELECT node.idNode,
                ".get__query($language)."
                (COUNT(parent.idNode) - (sub_tree.depth + 1)) AS depth
        FROM node_tree AS node,
                node_tree AS parent,
                node_tree AS sub_parent,
                (".sub_tree_query($node_id).") AS sub_tree
        WHERE node.iLeft BETWEEN parent.iLeft AND parent.iRight
                AND node.iLeft BETWEEN sub_parent.iLeft
                AND sub_parent.iRight
                AND sub_parent.idNode > sub_tree.idNode
        GROUP BY node.idNode
        HAVING depth>=0
                AND LOWER(nodeName) LIKE LOWER('%".$search_keyword."%')
                AND idNode != $node_id
        ORDER BY node.iLeft
        LIMIT ".$page_num.", ".$page_size."
    ";

    return $sql_query;
}

/**
 *
 */
function count_children($node_id, $search_keyword, $language){
    global $db;

    $sql_query = "
        SELECT node.idNode,
                ".get__query($language)."
                (COUNT(parent.idNode) - (sub_tree.depth + 1)) AS depth
        FROM node_tree AS node,
                node_tree AS parent,
                node_tree AS sub_parent,
                (".sub_tree_query($node_id).") AS sub_tree
        WHERE node.iLeft BETWEEN parent.iLeft AND parent.iRight
                AND node.iLeft BETWEEN sub_parent.iLeft
                AND sub_parent.iRight
                AND sub_parent.idNode > sub_tree.idNode
        GROUP BY node.idNode
        HAVING depth>=0
                AND LOWER(nodeName) LIKE LOWER('%".$search_keyword."%')
                AND idNode != $node_id
        ORDER BY node.iLeft";

        $res = $db->query($sql_query);
        $count = 0;
        while($row = $res->fetch())
            $count++;
        return $count;
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
