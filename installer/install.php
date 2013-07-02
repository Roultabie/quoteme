<?php
/**
 * Q.uote.me installer
 * @package quoteme
 * @author Daniel Douat <daniel.douat@aelys-info.fr>
 */

/**
 * Loading libs
 */

require '../config.php';
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

function checkDb($dbHost, $dbUser, $dbPass, $dbName, $dbTable)
{
    if (!empty($_POST['dbname'])) {
        $checkDb = checkDbInfos($dbHost, $dbName, $dbUser, $dbPass);
        if (is_array($checkDb)) {
            if ($checkDb[0] === 2002) $sqlInfo = '[trad::sqlError2002]';
            if ($checkDb[0] === 1044) $sqlInfo = '[trad::sqlError1044]';
            if ($checkDb[0] === 1045) $sqlInfo = '[trad::sqlError1045]';
            $passed = FALSE;
        }
        else {
            $checkDbTable = checkDbTable($checkDb, $dbTable);
            if ($checkDbTable === FALSE) {
                $sqlInfos = '[trad::table_already_exist]';
                $passed = FALSE;
            }
            else {
                $sqlInfos = '[trad::db_infos_correct]';
                $GLOBALS['html']->setElement('disabled', 'disabled');
                $passed = TRUE;
            }
        }
    }
    else {
        $sqlInfo = '[trad::dbname_cant_be_empty]';
        $passed  = FALSE;
    }
    $GLOBALS['html']->setElement('sqlInfos', $sqlInfos);
    return $passed;
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

function checkConfig()
{
    if (is_writable('../config.php')) {
        $GLOBALS['html']->setElement('checked', 'checked');
        $result = TRUE;
    }
    else {
        $result = FALSE;
    }
    return $result;
}

function writeConfig($newConfig)
{
    //on récupere config.php dans un tableau
    $config = file('../config.php');
    //on crée un tableau avec comme clé les clés de $configommOrder
    // Sauf pour les balise et commentaire ou l'on garde la clé chiffrée
    foreach($config as $key => $val) {
        $elements = explode('=', $val);
        if ($val[0] === "$") {
            $key  = substr(trim($elements[0]), strpos(trim($elements[0]), "'")+1, -2);
        }
        $tmpConfig[$key] = $val;
    }
    // Ensuite on remplace les éléments du tableau temporaire avec les valeurs correspondantes aux clés
    // En ajoutant la variable $config ou autre
    foreach($newConfig as $key => $val) {
        $elements = explode('=', $tmpConfig[$key]);
        $var   = trim($elements[0]);
        $value = trim($elements[1]);
        if ($value[0] === "'") {
            $value = "'" . $val . "';";
        }
        else {
            $value = $val . ';';
        }
        $tmpConfig[$key] = $var . ' = ' . $value . PHP_EOL;
    }
    // Puis on réécrit config.php
    if (!$fd = fopen('../config.php',"w+")) {
    echo "Echec de l'ouverture du fichier";
    }
    foreach($tmpConfig as $val) {
        fwrite($fd, $val);
    }
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

function install()
{
    $password = hash('sha256', $_SESSION['password']);
    $table = 'CREATE TABLE IF NOT EXISTS `' . $_SESSION['dbTable'] . '` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote` text NOT NULL,
  `author` varchar(100) NOT NULL,
  `source` varchar(100) NOT NULL,
  `tags` text NOT NULL,
  `permalink` char(6) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;';

    $instance = checkDbInfos($_SESSION['dbHost'], $_SESSION['dbName'], $_SESSION['dbUser'], $_SESSION['dbPass']);
    $stmt = $instance->prepare($table);
    $stmt->execute();
    if ($stmt !== FALSE) {
        writeConfig(array('dbHost' => $_SESSION['dbHost'], 'dbName' => $_SESSION['dbName'],
            'dbUser' => $_SESSION['dbUser'], 'dbPass' => $_SESSION['dbPass'],
            'dbTable' => $_SESSION['dbTable'], 'lang' => $_SESSION['lang'],
            'user' => $_SESSION['user'], 'password' => $password));
    }
    return FALSE;
}

if (empty($config['password'])) {
    if (empty($_POST['lang'])) {
        $_POST['lang'] = navLang('script');
    }
    session_start();
    timply::setUri('');
    timply::setFileName('installer.html');
    timply::addDictionary('../lang/' .$_POST['lang'] . '.php');
    $html = new timply();

    if (!empty($_POST)) {
        if (empty($_SESSION['passed'])) $_POST['passed'] = '0';
        if (empty($_POST['passed']))    $_POST['passed'] = '0';
        if (empty($_POST['dbhost']))    $_POST['dbhost'] = 'localhost';
        if (empty($_POST['user']))      $_POST['user']   = $_POST['dbuser'];
        if (empty($_POST['password']))  $_POST['password']   = $_POST['dbpass'];
        $_SESSION['passed']  = $_POST['passed'];
        $_SESSION['lang']    = $_POST['lang'];
        $_SESSION['dbHost']  = $_POST['dbhost'];
        $_SESSION['dbUser']  = $_POST['dbuser'];
        $_SESSION['dbPass']  = $_POST['dbpass'];
        $_SESSION['dbName']  = $_POST['dbname'];
        $_SESSION['dbTable'] = $_POST['dbtable'];
        $_SESSION['user']    = $_POST['user'];
        $_SESSION['pass']    = $_POST['password'];
        $_SESSION['email']   = $_POST['email'];

        //$html->setElement('display', 'display: block;');
        $html->setElement('postDbHost', $_SESSION['dbHost']);
        $html->setElement('postDbUser', $_SESSION['dbUser']);
        $html->setElement('postDbPass', $_SESSION['dbPass']);
        $html->setElement('postDbName', $_SESSION['dbName']);
        $html->setElement('postDbTable', $_SESSION['dbTable']);
        $html->setElement('postUser', $_SESSION['user']);
        $html->setElement('postPass', $_SESSION['pass']);
        $html->setElement('postEmail', $_SESSION['email']);

        $db   = checkDb($_SESSION['dbHost'], $_SESSION['dbUser'], $_SESSION['dbPass'], $_SESSION['dbName'], $_SESSION['dbTable']);
        $conf = checkConfig();
        if ($db && $conf) {
            $html->setElement('install', '<input type="submit" name="install" value="[trad::install_script]">');
            $canInstall = TRUE;
        }
        if (isset($_POST['install']) && $canInstall) {
            $install = install();
        }
    }
    $html->setElement('test', '<input type="submit" name="test" value="[trad::test_datas]">');
    $html->setElement('langSelect', genSelectLang($_POST['lang']));
    echo $html->returnHtml();
}

?>