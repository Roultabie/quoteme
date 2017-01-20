<?php

# Models
/**
 *
 */
class statsQueries
{

    function __construct()
    {
        self::$tblPrefix  = $GLOBALS['config']['tblPrefix'];
    }

    function getDelivered($year = $month = $day = $user = '')
    {
        if (!empty($year)) {
            if (count($year) != 4) return 400;
            $dateSearch = $year . '-';
        }
        if (!empty($month)) {
            if (count($month) != 2) return 400;
            if (empty($year)) return 400;
            $dateSearch .= $month . '-';
        }
        if (!empty($day)) {
            if (count($day) != 2) return 400;
            if (empty($month)) return 400;
            $dateSearch .= $day;
        }
        $dateSearch .= '%'

        if (!empty($user)) {
            $query = 'SELECT ' . self::$tblPrefix . 'users.id,
                      COUNT (' . self::$tblPrefix . 'delivered.id) AS total
                      FROM ' . self::$tblPrefix . 'users AS u
                      INNER JOIN ' . self::$tblPrefix . 'delivered AS d
                      ON u.share_token = d.share_token
                      WHERE u.id = ":user"
                      AND d.date LIKE ":dateSearch";';
        }
        else {
            $query = 'SELECT COUNT (id) AS total
                      FROM ' . self::$tblPrefix . 'delivered
                      WHERE date LIKE ":dateSearch"';
        }

        $stmt = dbConnexion::getInstance()->prepare($query);
        if (!empty($user)) $stmt->bindValue(':user', $user, PDO::PARAM_STR);
        $stmt->bindValue(':dateSearch', $dateSearch, PDO::PARAM_STR);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = NULL;

        if (count($datas) > 0) {
            if $datas[0]->total !== '0') return $datas[0]->total;
        }

        return 404;
    }

    function getPosted($year = $month = $day = $user = '')
    {
        if (!empty($year)) {
            if (count($year) != 4) return 400;
            $dateSearch = $year . '-';
        }
        if (!empty($month)) {
            if (count($month) != 2) return 400;
            if (empty($year)) return 400;
            $dateSearch .= $month . '-';
        }
        if (!empty($day)) {
            if (count($day) != 2) return 400;
            if (empty($month)) return 400;
            $dateSearch .= $day;
        }
        $dateSearch .= '%'
        $user = (empty($user)) ? '%' : $user;
        $query = 'SELECT COUNT(id)
                  FROM ' . self::$tblPrefix . 'quotes
                  WHERE date LIKE ":dateSearch"
                  AND user LIKE ":user"';
        $stmt = dbConnexion::getInstance()->prepare($query);
        $stmt->bindValue(':user', $user, PDO::PARAM_STR);
        $stmt->bindValue(':dateSearch', $dateSearch, PDO::PARAM_STR);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt = NULL;

        if (count($datas) > 0) {
            if $datas[0]->total !== '0') return $datas[0]->total;
        }

        return 404;
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
    function __construct()
    {
        $this->apiVersion = '0.0';
        $this->domain     = 'statistics';
        $this->methodType = 'get';
        $this->methods    = ['quotes'];
        $urlRewrite       = (empty($GLOBALS['config']['apiUrlRewrite'])) ? true : false;
        self::$tblPrefix  = $GLOBALS['config']['tblPrefix'];
    }

    private function returnSuccess($items, $context)
    {
        switch ($code) {
            case 200:
                $message = 'Success';
                break;
            default:
                $message = 'Unknown Success';
                break;
        }
        $datas = [];
        $datas['apiVersion']      = $this->apiVersion;
        $datas['context']         = $context;
        $datas['data']['code']    = $code;
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

    // private function setTable()
    // {
    //     if ($this->method['method'] === 'quotes') {
    //         if ($this->method['params']['type'] === 'posted') {
    //             $table = self::$tblPrefix .'quotes';
    //         }
    //         elseif ($this->method['params']['type'] === 'delivered') {
    //             $table = self::$tblPrefix .'';
    //         }
    //     }
    // }
}
