<?php
/**
* return tags in json format
*
*/
function jsonTagsByHits($string, $not, $limit = '4', $order = 'DESC')
{
    $not = (!empty($not)) ? " AND tag NOT REGEXP '" . str_replace(',', '|', $not) . "'" : ' ';
    $query   = "SELECT tag, hits FROM " . $GLOBALS['config']['tblPrefix'] . 
               "tags WHERE tag LIKE '" . $string . "%'" . $not . " ORDER BY hits " . $order . " LIMIT " . $limit . ';';
    $stmt    = dbConnexion::getInstance()->prepare($query);
    $stmt->execute();
    $datas = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    if (count($datas) > 0) {
        array_walk($datas, function (&$item, $key) {
            $item = array('value' => $item);
        });
        $result['status'] = 'success';
        $result['data']   = $datas;
    }
    else {
        $result['status'] = 'error';
    }
    $stmt->closeCursor();
    $stmt = NULL;
    return json_encode($result);
}

/**
* return authors in json format
*
*/
function jsonAuthorsByHits($string, $limit = '4', $order = 'DESC')
{
    $query   = "SELECT author, hits FROM " . $GLOBALS['config']['tblPrefix'] . "authors WHERE author LIKE '" . $string . "%' ORDER BY hits " . $order . " LIMIT " . $limit . ';';
    $stmt    = dbConnexion::getInstance()->prepare($query);
    $stmt->execute();
    $datas = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    if (count($datas) > 0) {
        array_walk($datas, function (&$item, $key) {
            $item = array('value' => $item);
        });
        $result['status'] = 'success';
        $result['data']   = $datas;
    }
    else {
        $result['status'] = 'error';
    }
    $stmt->closeCursor();
    $stmt = NULL;
    return json_encode($result);
}