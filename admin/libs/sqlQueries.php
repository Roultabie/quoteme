<?php

class dataQueries
{
    private static $table;
    private static $tblPrefix;
    private static $perPage;
    private static $offset;
    private static $fields;
    private static $id;
    public static $nbResult;

    function __construct($table, $fields, $id)
    {
        if (is_string($table)) {
            self::$table   = $GLOBALS['config']['tblPrefix'] . $table;
        }
        $this->tblPrefix = $GLOBALS['config']['tblPrefix'];
        self::$perPage   = (is_int($GLOBALS['config']['perpage'])) ? $GLOBALS['config']['perpage'] : 10;
        self::$fields    = $fields;
        self::$id        = $id;
        $this->countElements();
    }

    public function getDatas($page = '', $order = '')
    {
        if (!in_array($order, array('ASC', 'DESC'))) $order = 'DESC';
        $page   = (!empty($page)) ? $page : 1;
        $offset = $this->getOffset($page);
        $query  = 'SELECT ' . self::$fields . '
                   FROM ' . self::$table . '
                   INNER JOIN (
                      SELECT ' . self::$id . '
                      FROM ' . self::$table . '
                      ORDER BY ' . self::$id . ' ' . $order . '
                      LIMIT ' . self::$perPage . '
                      OFFSET ' . $offset .'
                   )
                   AS result USING(' . self::$id . ');';
        $stmt = dbConnexion::getInstance()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return $result;
    }

    public function searchDatas($field, $search, $page = '', $order = '')
    {
        $this->countElements($field, $search);
        if (!in_array($order, array('ASC', 'DESC'))) $order = 'DESC';
        $page    = (!empty($page)) ? $page : 0;
        $offset  = $this->getOffset($page);
        $query   = 'SELECT ' . self::$fields . '
                    FROM ' . self::$table . '
                    INNER JOIN (
                       SELECT ' . self::$id . '
                       FROM ' . self::$table . '
                       WHERE ' . $field . '
                       LIKE ?
                       ORDER BY ' . self::$id . ' ' . $order . '
                       LIMIT ' . self::$perPage . '
                       OFFSET ' . $offset . '
                    )
                    AS result USING(' . self::$id . ');';
        $stmt = dbConnexion::getInstance()->prepare($query);
        $stmt->execute(array('%' . $search . '%'));
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return $result;
    }

    private function getOffset($page)
    {
        return ($page - 1) * self::$perPage;
    }

    private function countElements($field = '', $search = '')
    {
        if (!empty($search) && !empty($field)) $where = ' WHERE ' . $field . ' LIKE ?';
        $query = 'SELECT COUNT(*) AS nb FROM ' . self::$table . $where . ';';
        $stmt = dbConnexion::getInstance()->prepare($query);
        $stmt->execute(array('%' . $search . '%'));
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        self::$nbResult = $result[0]->nb;
    }
}

function getDatas($table, $page, $like = '')
{
    $perpage = (!empty($GLOBALS['config']['perpage'])) ? $GLOBALS['config']['perpage'] : 10;

    $query = 'SELECT id, quote, author, source, tags, permalink, date
              FROM ' . $this->$tblPrefix . $table . ' INNER JOIN (
                   SELECT id
                   FROM ' . $this->$tblPrefix . $table . '
                   ORDER BY id DESC
                   LIMIT ' . $perpage . '
                   OFFSET ' . $offset . '
              )
              AS result USING(id)';
    $stmt  = dbConnexion::getInstance()->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt->closeCursor();
    $stmt = NULL;
    return $result;
}
