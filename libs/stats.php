<?php
/**
 * Quoteme stats class
 *
 * Genarate webpage from html sources
 *
 * @package     quoteme
 * @author      Daniel Douat <daniel@gorgones.net>
 * @link        http://daniel.douat.fr
 */
class stats
{
    static public $tblPrefix;
    function __construct()
    {
        self::$tblPrefix = $GLOBALS['config']['tblPrefix'];
        if ($this->getStats() === false) {
            $this->initStats();
        }
    }

    function getLiveStat($stat)
    {
        if ($stat === 'quotes')         $result = $this->countQuotes();
        if ($stat === 'authors')        $result = $this->countAuthors();
        if ($stat === 'tags')           $result = $this->countTags();
        if ($stat === 'contributors')   $result = $this->countAccounts(2);
        if ($stat === 'editors')        $result = $this->countAccounts(1);
        if ($stat === 'administrators') $result = $this->countAccounts(0);
        if ($stat === 'allUsers') {
            $result  = $this->countAccounts(2);
            $result += $this->countAccounts(1);
            $result += $this->countAccounts(0);
        }
        return $result;
    }


    function getStats($wanted = '')
    {
        if (empty($wanted)) {
            $date = new DateTime();
            $wanted = $date->format('Y-m-d');
        }
        $query = 'SELECT date, delivered, delivered_today, quotes, authors, tags, contributors, editors
                  FROM ' . $GLOBALS['config']['tblPrefix'] .'stats WHERE date=:date ORDER BY date DESC LIMIT 1';
        $stmt  = dbConnexion::getInstance()->prepare($query);
        $stmt->bindValue(':date', $wanted, PDO::PARAM_STR);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $datas = (count($datas) !== 0) ? $datas : false;
        $stmt = NULL;
        return $datas;
    }

    function checkDelivered($permalink, $parser, $token = '')
    {
        if (!is_array($_SESSION['delivered']) || !in_array($permalink, $_SESSION['delivered'])) {
            $this->hitDelivered();
            if (!empty($token)) {
                if (self::checkToken($token)) {
                    $datas['token'] = $token;
                    $datas['permalink'] = $permalink;
                    // if direct access, source empty and deliver not registered, actually it's good
                    $datas['source'] = $_SERVER['HTTP_REFERER'];
                    $datas['parser'] = $parser;
                    $this->addDelivered($datas);
                }
            }
            $_SESSION['delivered'][] = $permalink;
        }
    }

    function initStats()
    {
        $query = 'SELECT date, delivered, quotes, authors, tags, contributors, editors, administrators
                  FROM ' . $GLOBALS['config']['tblPrefix'] .'stats ORDER BY date DESC LIMIT 1';
        $stmt  = dbConnexion::getInstance()->prepare($query);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = NULL;
        $query = 'INSERT INTO ' . $GLOBALS['config']['tblPrefix'] .'stats
                  (date, delivered, quotes, authors, tags,
                   contributors, editors, administrators)
                  VALUES (NOW(), :delivered, :quotes, :authors, :tags, :contributors, :editors, :administrators)';
        $stmt  = dbConnexion::getInstance()->prepare($query);
        if (count($datas) > 0) {
            $stmt->bindValue(':delivered', $datas[0]->delivered, PDO::PARAM_INT);
            $stmt->bindValue(':quotes', $this->countQuotes(), PDO::PARAM_INT);
            $stmt->bindValue(':authors', $datas[0]->authors, PDO::PARAM_INT);
            $stmt->bindValue(':tags', $datas[0]->tags, PDO::PARAM_INT);
            $stmt->bindValue(':contributors', $datas[0]->contributors, PDO::PARAM_INT);
            $stmt->bindValue(':editors', $datas[0]->editors, PDO::PARAM_INT);
            $stmt->bindValue(':administrators', $datas[0]->administrators, PDO::PARAM_INT);
        }
        else {
            $stmt->bindValue(':delivered', 0, PDO::PARAM_INT);
            $stmt->bindValue(':quotes', $this->countQuotes(), PDO::PARAM_INT);
            $stmt->bindValue(':authors', $this->countAuthors(), PDO::PARAM_INT);
            $stmt->bindValue(':tags', $this->countTags(), PDO::PARAM_INT);
            $stmt->bindValue(':contributors', $this->countAccounts(2), PDO::PARAM_INT);
            $stmt->bindValue(':editors', $this->countAccounts(1), PDO::PARAM_INT);
            $stmt->bindValue(':administrators', $this->countAccounts(0), PDO::PARAM_INT);
        }
            $stmt->execute();
            $stmt = NULL;
    }

    /**
     * Return nb of all quotes
     * @return array array[0]->nb
     */
    private function countQuotes($user = '')
    {
        if (!empty($user)) {
            $query = 'SELECT COUNT(*) AS nb FROM ' . self::$tblPrefix . 'quotes WHERE user = :user';
            $stmt  = dbConnexion::getInstance()->prepare($query);
            $stmt->bindValue(':user', trim($user), PDO::PARAM_STR);
        }
        else {
            $query = 'SELECT COUNT(*) AS nb FROM ' . self::$tblPrefix . 'quotes';
            $stmt  = dbConnexion::getInstance()->prepare($query);
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return (int) $result[0]->nb;
    }

    private function countAuthors($user = '')
    {
        $query = 'SELECT COUNT(*) AS nb FROM ' . self::$tblPrefix . 'authors';
        $stmt  = dbConnexion::getInstance()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return (int) $result[0]->nb;
    }

    private function countTags($user = '')
    {
        $query = 'SELECT COUNT(*) AS nb FROM ' . self::$tblPrefix . 'tags';
        $stmt  = dbConnexion::getInstance()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return (int) $result[0]->nb;
    }

    private function countDelivered($user = '')
    {
        $query = 'SELECT COUNT(*) AS nb FROM ' . self::$tblPrefix . 'delivered';
        $stmt  = dbConnexion::getInstance()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return (int) $result[0]->nb;
    }

    private function countAccounts($type)
    {
        $query = 'SELECT COUNT(*) AS nb FROM ' . self::$tblPrefix . 'users' .' WHERE type = :type';
        $stmt  = dbConnexion::getInstance()->prepare($query);
        $stmt->bindValue(':type', $type, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return (int) $result[0]->nb;
    }

    private function addDelivered($datas)
    {
        if (!empty($datas['token'])) {
            $query = 'INSERT INTO ' . self::$tblPrefix . 'delivered (date, permalink, share_token, source, parser)
                      VALUES (NOW(), :permalink, :share_token, :source, :parser);';
            $stmt  = dbConnexion::getInstance()->prepare($query);
            $stmt->bindValue(':permalink', $datas['permalink'], PDO::PARAM_STR);
            $stmt->bindValue(':share_token',$datas['token'], PDO::PARAM_STR);
            $stmt->bindValue(':source', $this->getHost($datas['source']), PDO::PARAM_STR);
            $stmt->bindValue(':parser', $datas['parser'], PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    private function hitDelivered()
    {
        $date = new DateTime();
        $wanted = $date->format('Y-m-d');
        $query = 'UPDATE ' . self::$tblPrefix . 'stats' .'
                  SET delivered = delivered+1, delivered_today = delivered_today+1 WHERE date = :date';
        $stmt  = dbConnexion::getInstance()->prepare($query);
        $stmt->bindValue(':date', $wanted, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function getHost($source)
    {
        $elements = parse_url($source);
        if (is_array($elements)) {
	    if (!empty($elements['host'])) {
                return $elements['host'];
	    }
	    else {
	        return 'Unknown source';
	    }
        }
        else {
            return 'Unknown source';
        }
    }

    public static function checkToken($token)
    {
        $query = 'SELECT COUNT(*) AS nb FROM ' . self::$tblPrefix . 'users' .' WHERE share_token = :token';
        $stmt  = dbConnexion::getInstance()->prepare($query);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return ($result[0]->nb === '1') ? true : false;
    }
}
?>
