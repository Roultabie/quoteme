<?php
/**
* 
*/
class dbConnexion
{
    private static $instance;
    private static $dbHost;
    private static $dbName;
    private static $dbUser;
    private static $dbPass;

    public static function getInstance(){
        if(!isset(self::$instance)){
            self::setDbInfos();
            self::setInstance();
        }
        return self::$instance;
    }

    private static function setInstance()
    {
        try {
            self::$instance = new PDO('mysql:host=' . self::$dbHost . ';dbname=' . self::$dbName, self::$dbUser, self::$dbPass);
            self::$instance->query("SET NAMES 'utf8'");
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    private static function setDbInfos()
    {
        self::$dbHost = $GLOBALS['config']['dbHost'];
        self::$dbName = $GLOBALS['config']['dbName'];
        self::$dbUser = $GLOBALS['config']['dbUser'];
        self::$dbPass = $GLOBALS['config']['dbPass'];
    }
}
?>