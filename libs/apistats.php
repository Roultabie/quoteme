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
    }

    function getDelivered($year = '', $month = '', $day = '', $user = '')
    {
        $dateSearch = $this->returnDateSearch($year, $month, $day);
        if ($dateSearch === false) return 400;

        if (!empty($user)) {
            $query = 'SELECT ' . self::$tblPrefix . 'users.id,
                      COUNT(' . self::$tblPrefix . 'delivered.id) AS total
                      FROM ' . self::$tblPrefix . 'users AS u
                      INNER JOIN ' . self::$tblPrefix . 'delivered AS d
                      ON u.share_token = d.share_token
                      WHERE u.id = :user
                      AND d.date LIKE :dateSearch;';
        }
        else {
            $query = 'SELECT COUNT(id) AS total
                      FROM ' . self::$tblPrefix . 'delivered
                      WHERE date LIKE :dateSearch';
        }
        $stmt = dbConnexion::getInstance()->prepare($query);
        if (!empty($user)) $stmt->bindValue(':user', $user, PDO::PARAM_STR);
        $stmt->bindValue(':dateSearch', $dateSearch, PDO::PARAM_STR);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_OBJ);
        var_dump($datas);
        $stmt = NULL;

        if (is_array($datas)) {
            if ($datas[0]->total !== '0') return $datas[0]->total;
        }

        return false;
    }

    function getPosted($year = '', $month = '', $day = '', $user = '')
    {
        $dateSearch = $this->returnDateSearch($year, $month, $day);
        if ($dateSearch === false) return 400;

        $user = (empty($user)) ? '%' : $user;
        $query = 'SELECT COUNT(id) AS total
                  FROM ' . self::$tblPrefix . 'quotes
                  WHERE date LIKE :dateSearch
                  AND user LIKE :user';
        $stmt = dbConnexion::getInstance()->prepare($query);
        $stmt->bindValue(':user', $user, PDO::PARAM_STR);
        $stmt->bindValue(':dateSearch', $dateSearch, PDO::PARAM_STR);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = NULL;

        if (count($datas) > 0) {
            if ($datas[0]->total !== '0') return $datas[0]->total;
        }

        return false;
    }

    private function returnDateSearch($year = '', $month = '', $day = '')
    {
        if (!empty($year)) {
            if (count($year) != 4) return false;
            $dateSearch = $year . '-';
        }
        if (!empty($month)) {
            if (count($month) != 2) return false;
            if (empty($year)) return false;
            $dateSearch .= $month . '-';
        }
        if (!empty($day)) {
            if (count($day) != 2) return false;
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
        if ($result = $this->queries->getDelivered($year, $month, $day, $user)) {
            return $this->returnSuccess(['total' => $result], 'delivered');
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
        if ($result = $this->queries->getPosted($year, $month, $day, $user) !== 404) {
            return $this->returnSuccess(['total' => $result], 'posted');
        }
        else {
            return $this->returnError($result, 'posted');
        }
    }

    private function arrayToElements($datas)
    {
        if (is_array($datas)) {
            if (isset($datas['shortcut'])) {
                if ($array = $this->shortcutToDate($shortcut)) {
                    list($year, $month, $day) = $array;
                }
                else {
                    $year = $month = $day = false;
                }
            }
            elseif (!empty($datas['year'])) {
                $year = (count($datas['year']) === 4) ? $datas['year'] : false;
                if (!empty($datas['month'])) {
                    $month = (count($datas['month']) === 2) ? $datas['month'] : false;
                    if (!empty($datas['day'])) {
                        $day = ($datas['day'] === 2) ? $datas['day'] : false;
                    }
                }
            }
            if (!$year || !$month || !$day) {
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
        $datas['data']['message'] = $message;

        $datas['items'] = [];
        if (is_array($items)) $data['items'] = $items;

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
