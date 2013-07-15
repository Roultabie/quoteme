<?php
/**
 * Login lib
 */

class user
{

    private $username;
    private $mail;
    private $description;
    private $ip;
    private $userAgent;

    function __construct()
    {
        $this->setUsername('');
        $this->setMail('');
        $this->setDescription('');
        $this->setIp('');
        $this->setUserAgent('');
    }

    function getUsername()
    {
        return $this->username;
    }

    function setUsername($username)
    {
        $this->username = $username;
    }

    function getMail()
    {
        return $this->mail;
    }

    function setMail($mail)
    {
        $this->mail = $mail;
    }

    function getDescription()
    {
        return $this->description;
    }

    function setDescription($description)
    {
        $this->description = $description;
    }

    function getIp()
    {
        return $this->ip;
    }

    function setIp($ip)
    {
        $this->ip = $ip;
    }

    function getUserAgent()
    {
        return $this->userAgent;
    }

    function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }
}

class userWriter
{
    function __construct()
    {
        $this->user     = $GLOBALS['config']['user'];
        $this->password = $GLOBALS['config']['password'];
    }
    
    function loginCheck($login = "", $password = "") // pour utiliser une autre méthode (sql ...) faire hériter une nouvelle classe et redéfinir cette méthode
    {
        if (!empty($login) && !empty($password)) {
            if ($this->user === $login && $this->password === hash('sha256', $password)) {
                $user = new user();
                $user->setUsername($GLOBALS['config']['user']);
                $user->setMail($GLOBALS['config']['mail']);
                $user->setDescription($GLOBALS['config']['userInfo']);
                $user->setIp($_SERVER['REMOTE_ADDR']);
                $user->setUserAgent($_SERVER['HTTP_USER_AGENT']);
                $_SESSION['userDatas'] = serialize($user);
                $_SESSION['lastTime']  = microtime(TRUE);
                return $user;
            }
            else {
                unset($user); // On ne sait jamais ;)
                return FALSE;
            }
        }
        elseif(is_object(unserialize($_SESSION['userDatas']))) {
            $user     = unserialize($_SESSION['userDatas']);
            $lastHash = hash('sha256', $user->getIp() . $user->getUserAgent());;
            $currHash = hash('sha256', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
            if ($lastHash === $currHash) {
                $currentTime = microtime(TRUE);
                $breakTime   = $currentTime - $_SESSION['lastTime'];
                if ($breakTime < $GLOBALS['config']['sessionExpire']) {
                    $_SESSION['lastTime'] = $currentTime;
                    return $user;
                }
                else {
                    unset($user);
                    return FALSE;
                }
            }
            else {
                unset($user);
                return FALSE;
            }
        }
        else {
            unset($user);
            return FALSE;
        }
    }

    public static function initSession()
    {
        session_start();
        $_SESSION['startTime'] = microtime(TRUE);
    }

    public static function killSession()
    {
        session_destroy();
    }
}

userWriter::initSession();
$session = new userWriter();

if (!empty($_POST['login']) && !empty($_POST['pass'])) {
    $user = $_POST['login'];
    $pass = $_POST['pass'];
}

$user = $session->loginCheck($user, $pass);

if (!is_object($user) || $_POST['disconnect'] === '1' || $_GET['disconnect'] === '1') {
    if ($_GET['disconnect'] === '1') $loginAction = 'action="' . $_SERVER['SCRIPT_NAME'] . '"';
    userWriter::killSession();
    require 'loginform.php';
    exit();
}

?>