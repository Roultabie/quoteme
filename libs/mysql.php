<?php
/**
* 
*/
class dbConnexion
{
    
    /*function __construct(argument)
    {
        # code...
    }*/

    public static function getInstance(){
        if(!isset(self::$instance)){
            try {
                self::$instance = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USR, DB_PWD);
                self::$instance->query("SET NAMES 'utf8'");
            } catch (Exception $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        }
        return self::$instance;
    }
}
?>