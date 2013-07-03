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

function dbConnexion($host, $name, $user, $pass)
{
    try {
        $instance = new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $pass);
        $instance->query("SET NAMES 'utf8'");
        $result = $instance;
    } catch (Exception $e) {
        $error[] = $e->getCode();
        $error[] = $e->getMessage();
        $result = $error;
    }
    return $result;
}

function checkDbIds($host, $user, $password, $dbName, $tableName)
{
    if (!empty($dbName)) {
        $connexion = dbConnexion($host, $dbName, $user, $password);
        if (is_array($connexion)) {
            if ($connexion[0] === 2002) $sqlInfo = '[trad::sqlError2002]';
            if ($connexion[0] === 1044) $sqlInfo = '[trad::sqlError1044]';
            if ($connexion[0] === 1045) $sqlInfo = '[trad::sqlError1045]';
            $passed = FALSE;
        }
        else {
            $ifTableExist = ifDbTableExist($connexion, $tableName);
            if ($ifTableExist === FALSE) {
                $message = '[trad::table_already_exist]';
                $passed  = FALSE;
            }
            else {
                $GLOBALS['html']->setElement('disabled', 'disabled');
                $message = '[trad::db_infos_correct]';
                $passed = TRUE;
            }
        }
    }
    else {
        $message = '[trad::dbname_cant_be_empty]';
        $passed  = FALSE;
    }
    $GLOBALS['html']->setElement('sqlInfos', $message);
    return $passed;
}

function ifDbTableExist($instance, $table)
{
    if (is_object($instance)) {;
        $tables = $instance->prepare("SHOW TABLES LIKE '" . $table . "'");
        $tables->execute();
        $result = $tables->fetchAll(PDO::FETCH_OBJ);
        if (count($result) > 0) {
            $tableExist = FALSE;
        }
        else {
            $tableExist = TRUE;
        }
    }
    return $tableExist;
}

function checkConfigFile()
{
    $fileName = 'config.php';
    $fileUri  = '../';
    return is_writable($fileUri . $fileName);
}

function writeConfigFile($newOptions)
{
    $fileName       = 'config.php';
    $fileUri        = '../';
    $originalConfig = file($fileName . $fileUri);
    foreach($originalConfig as $key => $line) {
        $firstChar = $line[0];
        if ($firstChar === "$") {
            $arrayNameEnd    = strpos($line, '=') - 1;
            $arrayName       = trim(substr($line, 0, $arrayNameEnd)); // $config['optionName']
            $optionNameStart = strpos($arrayName, "'") + 1;
            $optionName      = substr($arrayName, $optionNameStart, -2);
            $key             = $optionName;
        }
        $tempConfig[$key] = $line;
    }
    // Ensuite on remplace les éléments du tableau temporaire avec les valeurs correspondantes aux clés
    // En ajoutant la variable $config ou autre
    foreach($newOptions as $key => $value) {
        if (is_string($value)) {
            $newValue = "'" . str_replace("'", "\'", $value) . "'";
        }
        else {
            $newValue = $value;
        }
        $tempConfig[$key] = $key . ' = ' . $newValue . PHP_EOL;
    }
    // Puis on réécrit config.php
    if ($fd = fopen($fileName . $fileUri,"w+")) {
        foreach($tempConfig as $val) {
            fwrite($fd, $val);
        }
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

function install($resetPassword = FALSE)
{
    $password = hash('sha256', $_SESSION['password']);
    if ($resetPassword === FALSE) {
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

        $instance = dbConnexion($_SESSION['dbHost'], $_SESSION['dbName'], $_SESSION['dbUser'], $_SESSION['dbPass']);
        $stmt = $instance->prepare($table);
        $stmt->execute();
        if ($stmt !== FALSE) {
            writeConfigFile(array('dbHost' => $_SESSION['dbHost'], 'dbName' => $_SESSION['dbName'],
                'dbUser' => $_SESSION['dbUser'], 'dbPass' => $_SESSION['dbPass'],
                'dbTable' => $_SESSION['dbTable'], 'lang' => $_SESSION['lang'],
                'user' => $_SESSION['user'], 'password' => $password, 'email' => $_SESSION['email']));
            $install = TRUE;
        }
    }
    else {
        writeConfigFile(array('user' => $_SESSION['user'], 'password' => $password, 'email' => $_SESSION['email']));
        $install = TRUE;
    }
    return $install;
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
        if (empty($GLOBALS['config']['dbName'])) {
            if (empty($_POST['dbhost']))    $_POST['dbhost'] = 'localhost';
            if (empty($_POST['user']))      $_POST['user']   = $_POST['dbuser'];
            if (empty($_POST['password']))  $_POST['password']   = $_POST['dbpass'];
            $_SESSION['lang']    = $_POST['lang'];
            $_SESSION['dbHost']  = $_POST['dbhost'];
            $_SESSION['dbUser']  = $_POST['dbuser'];
            $_SESSION['dbPass']  = $_POST['dbpass'];
            $_SESSION['dbName']  = $_POST['dbname'];
            $_SESSION['dbTable'] = $_POST['dbtable'];
            $_SESSION['user']    = $_POST['user'];
            $_SESSION['pass']    = $_POST['password'];
            $_SESSION['email']   = $_POST['email'];
            $db                  = checkDbIds($_SESSION['dbHost'], $_SESSION['dbUser'], $_SESSION['dbPass'], $_SESSION['dbName'], $_SESSION['dbTable']);
        }
        else {
            $_SESSION['lang']    = $GLOBALS['config']['lang'];
            $_SESSION['dbHost']  = $GLOBALS['config']['dbHost'];
            $_SESSION['dbUser']  = $GLOBALS['config']['dbUser'];
            $_SESSION['dbPass']  = $GLOBALS['config']['dbPass'];
            $_SESSION['dbName']  = $GLOBALS['config']['dbName'];
            $_SESSION['dbTable'] = $GLOBALS['config']['dbTable'];
            $_SESSION['user']    = $GLOBALS['config']['user'];
            $_SESSION['email']   = $GLOBALS['config']['email'];
            $_SESSION['pass']    = $_POST['password'];
            $db                  = TRUE;
            $resetPassword       = TRUE;
        }
        //$html->setElement('display', 'display: block;');
        $html->setElement('postDbHost', $_SESSION['dbHost']);
        $html->setElement('postDbUser', $_SESSION['dbUser']);
        $html->setElement('postDbPass', $_SESSION['dbPass']);
        $html->setElement('postDbName', $_SESSION['dbName']);
        $html->setElement('postDbTable', $_SESSION['dbTable']);
        $html->setElement('postUser', $_SESSION['user']);
        $html->setElement('postPass', $_SESSION['pass']);
        $html->setElement('postEmail', $_SESSION['email']);

        $conf = checkConfigFile();
        if ($db && $conf) {
            if ($resetPassword) {
                $value = '[trad::update_datas]';
            }
            else {
                $value = '[trad::install_script]';
            }
            $GLOBALS['html']->setElement('checked', 'checked');
            $html->setElement('install', '<input type="submit" name="install" value="' . $value . '">');
            $canInstall = TRUE;
        }
        if (isset($_POST['install']) && $canInstall) {
            $install = install($resetPassword);
        }
    }
    $html->setElement('test', '<input type="submit" name="test" value="[trad::test_datas]">');
    $html->setElement('langSelect', genSelectLang($_POST['lang']));
    echo $html->returnHtml();
}

?>