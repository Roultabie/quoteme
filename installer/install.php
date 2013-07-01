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

function checkDbInfos($host, $name, $user, $pass)
{
    try {
        $instance = new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $pass);
        $instance->query("SET NAMES 'utf8'");
        return $instance;
    } catch (Exception $e) {
        $error[] = $e->getCode();
        $error[] = $e->getMessage();
        return $error;
    }
}

function checkDbTable($instance, $table)
{
    if (is_object($instance)) {;
        $tables = $instance->prepare("SHOW TABLES LIKE '" . $table . "'");
        $tables->execute();
        $result = $tables->fetchAll(PDO::FETCH_OBJ);
        if (count($result) > 0) {
            $result = FALSE;
        }
        else {
            $result = TRUE;
        }
    }
    return $result;
}

function checkConfigRights()
{
    if (is_writable('config.php')) {
        return TRUE;
    }
    else {
        return FALSE;
    }
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

function navLang($format = '')
{
    // traitement de http_accept_language pour placer le q en avec la bonne langue.
    $bl = str_replace(',', ';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $bltable = explode(';', $bl);
    $number = count($bltable);
    for ($i = 0; $i < $number; $i++) {
        // Si la valeur est bien une langue (et pas un q=)
        if ($bltable[$i][1] !== '=') {
            // Si la valeur suivante est bien u q= (et pas une langue) elle appartient à la valeur (langue) courante.
            if ($bltable[$i + 1][1] === '=') {
                $q = substr($bltable[$i + 1], 2);
            }
            // Sinon c'est que la valeur est à 1 (explicite)
            else {
                $q = "1";
            }
            $preferedLangtable[strtolower($bltable[$i])] = $q;
        }
        unset($q);
    }
    arsort($preferedLangtable);
    $preferedLang = array_flip($preferedLangtable);
    $preferedLang = array_values($preferedLang);
    $preferedLang = $preferedLang[0];
    // Pour formater les langues courtes xx en xx-xx
    if (strlen($preferedLang) === 2) {
        $preferedLang .= '-' . $preferedLang;
    }
    if ($format = "script") {
        $table = explode('-', $preferedLang);
        $preferedLang = $table[0] . '_' . strtoupper($table[1]);
    }
    return $preferedLang;
}

function genSelectLang($defaultLang = "")
{
    $select = '<select name="lang" id="lang" onChange="javascript:this.form.submit();">';
    if (($dir = opendir('../lang'))) {
        $isSelected = FALSE;
        while (($file = readdir($dir)) !== FALSE) {
            if ($file !== '.' && $file !== '..') {
                $lang = substr($file, 0, -4);
                $selected = '';
                if ($defaultLang === $lang || ($isSelected === FALSE && navLang() === strtolower(str_replace('_', '-', $lang)))) {
                    $selected = ' selected';
                    $isSelected = TRUE;
                }
                
                $select .= '<option value="' . $lang . '"' . $selected . '>' . $lang . '</option>';
            }
            unset($selected);
        }
        closedir($dir);
    }
    $select .= '</select>';
    return $select;
}

function checkHtmlElement($state)
{
    if ($state === TRUE) {
        $element = '-';
    }
    else {
        $element = 'X';
    }
}

function install($posted)
{
    return FALSE;
}

if (empty($_GET['lang'])) {
    $_GET['lang'] = navLang('script');
}

timply::setUri($GLOBALS['config']['themeDir']);
timply::setFileName('installer.html');
timply::addDictionary('../lang/' .$_GET['lang'] . '.php');
$html = new timply();

if (!empty($_POST)) {
    if (empty($_POST['dblocation'])) {
        $_POST['dblocation'] = 'localhost';
    }

    $html->setElement('display', 'display: block;');

    $html->setElement('postDbHost', $_POST['dblocation']);
    $html->setElement('postDbUser', $_POST['dbuser']);
    $html->setElement('postDbPass', $_POST['dbpass']);
    $html->setElement('postDbName', $_POST['dbname']);
    $html->setElement('postDbTable', $_POST['dbtable']);
    if (!empty($_POST['dbname'])) {
        $checkDb = checkDbInfos($_POST['dblocation'], $_POST['dbname'], $_POST['dbuser'], $_POST['dbpass']);
        if (is_array($checkDb)) {
            if ($checkDb[0] === 2002) $sqlInfo = '[trad::sqlError2002]';
            if ($checkDb[0] === 1044) $sqlInfo = '[trad::sqlError1044]';
            if ($checkDb[0] === 1045) $sqlInfo = '[trad::sqlError1045]';
        }
        else {
            $checkDbTable = checkDbTable($checkDb, $_POST['dbtable']);
            if ($checkDbTable === FALSE) {
                $sqlInfos = '[trad::table_already_exist]';
            }
            else {
                $sqlInfos = '[trad::db_infos_correct]';
                $html->setElement('disabled', 'disabled');
            }
        }
    }
    else {
        $sqlInfo = '[trad::dbname_cant_be_empty]';
    }
    $html->setElement('sqlInfos', $sqlInfos);
    $installed = install($_POST);
    if ($installed === TRUE) {
        $infoMessage[] = "installation successful !";
        require '../admin.php';
        exit();
    }
}
$html->setElement('langSelect', genSelectLang($_GET['lang']));
$html->setElement('lang', $_GET['lang']);
echo $html->returnHtml();
?>