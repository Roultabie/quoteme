<?php
/**
 * Q.uote.me installer
 * @package quoteme
 * @author Daniel Douat <daniel.douat@aelys-info.fr>
 */

/**
 * Loading libs
 */

require '../libs/mysql.php';
require '../libs/timply.php';

function checkDbUser($user, $password)
{
    #
}

function checkConfigRights()
{
    #
}

function createTable($table)
{
    #
}

function createUser($username, $password, $email = "")
{
    #
}

function sanitizeString($string)
{
    #
}

function genSelectLang()
{

}

function install($_POST)
{
    return TRUE;
}

if (!empty($_POST)) {
    $installed = install($_POST);
    if ($installed === TRUE) {
        $infoMessage[] = "installation successful !";
        require 'admin.php'
        exit();
    }
}

timply::setUri($GLOBALS['config']['themeDir']);
timply::setFileName('admin.html');
timply::addDictionary($GLOBALS['config']['langDir'] . 'en_EN.php');
$html = new timply();

echo $html->returnHtml();

?>