<?php
/**
 *
 *
 *
 */



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

/**
 *
 */
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
                AND (node.iLeft BETWEEN sub_parent.iLeft AND sub_parent.iRight)
                AND sub_parent.idNode = sub_tree.idNode
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
                AND (node.iLeft BETWEEN sub_parent.iLeft AND sub_parent.iRight)
                AND sub_parent.idNode = sub_tree.idNode
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
