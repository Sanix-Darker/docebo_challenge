<?php
/**
 * queries.php
 *
 * All methods here will generate queries to be execute on the database
 */


/**
 * node_name_query
 *
 * This function will return a small query for getting a nodeName
 * with a specific language and a idNode.
 *
 * @param {*} $language
 *
 * @returns {string}
 */
function node_name_query($language){
    $query = "(SELECT nodeName FROM node_tree_names
            WHERE node.idNode = node_tree_names.idNode
                    AND node_tree_names.language=\"".$language."\") AS nodeName ";
    return $query;
}

/**
 * sub_tree_query
 *
 * This function will build the sub-query for fetching the sub_tree
 * from a node_id.
 *
 * @param {*} $node_id
 *
 * @returns {string}
 */
function sub_tree_query($node_id){
    return "SELECT node.idNode, (COUNT(parent.idNode) - 1) AS depth
        FROM node_tree AS node, node_tree AS parent
        WHERE node.iLeft BETWEEN parent.iLeft
            AND parent.iRight
            AND node.idNode = ".$node_id."
        GROUP BY node.idNode
        ORDER BY node.iLeft";
}

/**
 * generate_query
 *
 * This function will build the sql query string that is going to be execute to get all
 * children nodes.
 *
 * @param {*} $node_id
 * @param {*} $language
 * @param {*} $search_keyword
 * @param {*} $page_num
 * @param {*} $page_size
 *
 * @returns {boolean}
 */
function generate_query($node_id, $language, $search_keyword, $page_num, $page_size){
    global $result;

    $sql_query = "SELECT node.idNode, ".node_name_query($language).",
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
 * count_children
 *
 * This function will return the appropriate sql query string
 * to be run to get all children and we're going to count that.
 *
 * @param {*} $node_id
 * @param {*} $search_keyword
 * @param {*} $language
 *
 * @returns {int} - the children count for the node_id
 */
function count_children($node_id, $search_keyword, $language){
    global $db;

    $sql_query = "SELECT node.idNode, ".node_name_query($language).",
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

        // we make the query here
        $res = $db->query($sql_query);
        $count = 0;
        while($row = $res->fetch())
            $count++;

        return $count;
}
