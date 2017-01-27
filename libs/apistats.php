<?php

# Models
/**
 *
 */
class statsQueries
{
    private static $tblPrefix;
    function __construct()
    {
        self::$tblPrefix  = $GLOBALS['config']['tblPrefix'];
        $user = new userQueries();
        $this->userConfig = $user->config;
    }

    function getDelivered($year = '', $month = '', $day = '', $user = '')
    {
        $dateSearch = $this->returnDateSearch($year, $month, $day);
        if ($dateSearch === false) return 400;

        if ($this->userConfig['type'] == '2') {
            $user = $this->userConfig['id'];
        }
        if (empty($user)) {
            if ($this->userConfig['type'] > '1') {
                $query = 'SELECT (
                              SELECT COUNT(*)
                              FROM ' . self::$tblPrefix . 'delivered AS d
                              LEFT JOIN qm_users AS u
                              ON d.share_token = u.share_token
                              WHERE d.date LIKE "%"
                          ) AS user, (
                              SELECT COUNT(*) FROM qm_quotes
                          ) AS total';
            }
            else {
                $query = 'SELECT u.username, COUNT(d.id) AS count
                          FROM ' . self::$tblPrefix . 'delivered AS d
                          LEFT JOIN ' . self::$tblPrefix . 'users AS u
                          ON d.share_token = u.share_token
                          WHERE d.date LIKE :dateSearch
                          GROUP BY u.username WITH ROLLUP';
            }
        }
        else {
            $query = 'SELECT u.username, COUNT(d.id) AS count
                      FROM ' . self::$tblPrefix . 'delivered AS d
                      LEFT JOIN ' . self::$tblPrefix . 'users AS u
                      ON d.share_token = u.share_token
                      WHERE d.date LIKE :dateSearch
                      AND u.id LIKE :user';
        }
        $stmt = dbConnexion::getInstance()->prepare($query);
        if (!empty($user)) $stmt->bindValue(':user', $user, PDO::PARAM_STR);
        $stmt->bindValue(':dateSearch', $dateSearch, PDO::PARAM_STR);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = NULL;
        if (is_array($datas)) {
            if ($datas[0]->count !== '0') return $datas;
        }

        return false;
    }

    function getPosted($year = '', $month = '', $day = '', $user = '')
    {
        $dateSearch = $this->returnDateSearch($year, $month, $day);
        if ($dateSearch === false) return 400;

        if (empty($user)) {
            if ($this->userConfig['type'] > '1') {
                $query = 'SELECT (
                              SELECT COUNT(*)
                              FROM ' . self::$tblPrefix . 'quotes AS q
                              LEFT JOIN qm_users AS u
                              ON q.user = u.id
                              WHERE q.date LIKE "%"
                          ) AS user, (
                              SELECT COUNT(*) FROM qm_quotes
                          ) AS total';
            }
            else {
                $query = 'SELECT u.username, COUNT(q.id) AS count
                          FROM ' . self::$tblPrefix . 'quotes AS q
                          LEFT JOIN ' . self::$tblPrefix . 'users AS u
                          ON q.user = u.id
                          WHERE q.date LIKE :dateSearch
                          GROUP BY u.username WITH ROLLUP';
            }
        }
        else {
            $query = 'SELECT u.username, COUNT(q.id) AS count
                      FROM ' . self::$tblPrefix . 'quotes AS q
                      LEFT JOIN '. self::$tblPrefix . 'users AS u
                      ON q.user = u.id
                      WHERE date LIKE :dateSearch
                      AND user LIKE :user';
        }
        $stmt = dbConnexion::getInstance()->prepare($query);
        if (!empty($user)) $stmt->bindValue(':user', $user, PDO::PARAM_STR);
        $stmt->bindValue(':dateSearch', $dateSearch, PDO::PARAM_STR);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = NULL;
        if (count($datas) > 0) {
            if ($datas[0]->count !== '0') return $datas;
        }

        return false;
    }

    private function returnDateSearch($year = '', $month = '', $day = '')
    {
        if (!empty($year)) {
            if (strlen($year) != 4) return false;
            $dateSearch = $year . '-';
        }
        if (!empty($month)) {
            if (strlen($month) != 2) return false;
            if (empty($year)) return false;
            $dateSearch .= $month . '-';
        }
        if (!empty($day)) {
            if (strlen($day) != 2) return false;
            if (empty($month)) return false;
            $dateSearch .= $day;
        }
        $dateSearch .= '%';

        return $dateSearch;
    }
}


# Controllers
/**
 * Quoteme api stats class
 *
 * Give RESful API for q.uote.me stats
 *
 * @package     quoteme
 * @author      Daniel Douat <daniel@gorgones.net>
 * @link        http://daniel.douat.fr
 * WIP:https://google.github.io/styleguide/jsoncstyleguide.xml#JSON_Structure_&_Reserved_Property_Names
 */
class apiStats
{
    private static $tblPrefix;
    private $apiVersion;
    private $domain;
    private $methodType;
    private $methods;
    private $queries;
    function __construct()
    {
        $this->apiVersion = '0.0';
        $this->domain     = 'statistics';
        $this->methodType = 'get';
        $this->methods    = ['quotes'];
        self::$tblPrefix  = $GLOBALS['config']['tblPrefix'];
        $this->queries    = new statsQueries();
        $user = new userQueries();
        $this->userConfig = $user->config;
    }

    public function getDelivered($datas)
    {
        if (is_array($datas)) {
            if ($elements = $this->arrayToElements($datas)) {
                list($year, $month, $day, $user) = $elements;
            }
            else {
                $this->returnError(400, 'delivered');
            }
        }
        if ($elements = $this->queries->getDelivered($year, $month, $day, $user)) {
            if ($this->userConfig['type'] > '1') {
                $result[0]['username'] = $this->userConfig['username'];
                $result[0]['count']    = $elements[0]->user;
                $result[1]['username'] = 'null';
                $result[1]['count']    = $elements[0]->total;
            }
            else {
                $result = $elements;
            }
            return $this->returnSuccess($result, 'delivered');
        }
        else {
            return $this->returnError(404, 'delivered');
        }
    }

    public function getPosted($datas)
    {
        if (is_array($datas)) {
            if ($elements = $this->arrayToElements($datas)) {
                list($year, $month, $day, $user) = $elements;
            }
            else {
                $this->returnError(400, 'posted');
            }
        }
        if ($elements = $this->queries->getPosted($year, $month, $day, $user)) {
            if ($this->userConfig['type'] > '1') {
                $result[0]['username'] = $this->userConfig['username'];
                $result[0]['count']    = $elements[0]->user;
                $result[1]['username'] = 'null';
                $result[1]['count']    = $elements[0]->total;
            }
            else {
                $result = $elements;
            }
            return $this->returnSuccess($result, 'posted');
        }
        else {
            return $this->returnError(404, 'posted');
        }
    }

    private function arrayToElements($datas)
    {
        if (is_array($datas)) {
            if (strlen($datas['year']) > 4) {
                if ($array = $this->shortcutToDate($datas['year'])) {
                    list($year, $month, $day) = $array;
                }
                else {
                    $year = $month = $day = false;
                }
            }
            else {
                if (!empty($datas['year'])) {
                    $year = (strlen($datas['year']) === 4) ? $datas['year'] : false;
                    if (!empty($datas['month'])) {
                        $month = (strlen($datas['month']) === 2) ? $datas['month'] : false;
                        if (!empty($datas['day'])) {
                            $day = (strlen($datas['day']) === 2) ? $datas['day'] : false;
                        }
                    }
                }
            }
            if ($year === false || $month === false || $day === false) {
                return false;
            }
            if (isset($datas['user'])) $user = $datas['user'];
            return [$year, $month, $day, $user];
        }
    }

    private function shortcutToDate($shortcut)
    {
        $date = new DateTime('now');
        switch ($shortcut) {
            case 'lastyear':
                $date->modify('-1 year');
                $year = $date->format('Y');
                var_dump($year);
                break;
            case 'lastmonth':
                $date->modify('-1 month');
                $year  = $date->format('Y');
                $month = $date->format('m');
                break;
            case 'lastweek':
                $date->modify('-1 week');
                $year  = $date->format('Y');
                $month = $date->format('m');
                $day   = $date->format('d');
                break;
            case 'yesterday':
                $date->modify('yesterday');
                $year  = $date->format('Y');
                $month = $date->format('m');
                $day   = $date->format('d');
                break;
            default:
                return false;
                break;
        }
        return [$year, $month, $day];
    }

    private function returnSuccess($items, $context)
    {
        $datas = [];
        $datas['apiVersion']      = $this->apiVersion;
        $datas['context']         = $context;
        $datas['data']['code']    = 200;

        $datas['data']['items'] = [];
        if (is_array($items)) $datas['data']['items'] = $items;

        return json_encode($datas);
    }

    private function returnError($code, $context, $reason = '')
    {
        switch ($code) {
            case 400:
                $message = 'Bad Request';
                break;
            case 401:
                $message = 'Unauthorized';
                break;
            case 404:
                $message = 'Not Found';
                break;
            case 405:
                $message = 'Method Not Allowed';
                break;
            default:
                $message = 'Unknown Error';
                break;
        }
        $datas = [];
        $datas['apiVersion'] = $this->apiVersion;
        $datas['context']    = $context;
        $datas['error']['code']    = $code;
        $datas['error']['message'] = $message;

        $datas['error']['errors'] = [];
        $datas['error']['errors']['domain']  = $this->domain;
        $datas['error']['errors']['message'] = $message;
        if (false && !empty($reason)) { // WIP: if user is dev or admin
            $datas['error']['errors']['reason'] = $reason;
        }

        return json_encode($datas);
    }

    private function returnMethod($method)
    {
        parse_str($_SERVER["QUERY_STRING"], $parameters);
        $datas = [];
        //$datas['method'] = $parameters['method'] . '.' . $this->methodType;
        $datas['method'] = $parameters['method'];
        unset($parameters['method']);
        $datas['params'] = $parameters;
        $this->method = $datas;
    }
}
