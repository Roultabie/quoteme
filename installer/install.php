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

/**
 * Loading config file if exist
 */

if (file_exists($_SERVER['DOCUMENT_ROOT'] . 'config.php')) {
    require $_SERVER['DOCUMENT_ROOT'] . 'config.php';
}

/**
 * $configTemplate
 * template of config file
 */

$comment1 = <<<'EOC'
/**
 * System options don't modify them
 */

date_default_timezone_set('UTC');

EOC;

$system['dateFormat']  = 'Y-m-d';
$system['version']     = 'devel';
$system['lastUpdate']  = '0000-00-00';
$system['lastVersion'] = '';

$comment2 = <<<'EOC'
/**
 * config options, put your informations here
 */
EOC;

$config['dbHost'] = '';
$config['dbName'] = '';
$config['dbUser'] = '';
$config['dbPass'] = '';
$config['tblPrefix'] = 'qm_';
$config['lang'] = '';
$config['themeDir'] = 'themes/simple/';
$config['langDir'] = 'lang/';
$config['siteDoc'] = 'http://q.uote.me/api.php';
$config['users'] = '';
$config['sessionExpire'] = 1800;
$config['cacheState'] = TRUE;
$config['cacheDir'] = 'cache';
$config['appVers'] = ''; // your app version

$comment3 = <<<'EOC'
/**
 * When checking updates :
 * To send anonymous stats of your client change 'appVers' => $system['version']; by 'appVers' => 'a5e';
 * To send anything change 'appVers' => $system['version']; by 'appVers' => '';
 */
EOC;

function dbConnexion($host, $name, $user, $pass)
{
    try {
        $instance = new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $pass);
        $instance->query("SET NAMES 'utf8'");
        $result = $instance;
    } catch (Exception $e) {
        $error = $e->getCode();
        $result = $error;
    }
    return $result;
}

function checkDbIds($host, $user, $password, $dbName, $tblPrefix)
{
    $connexion = dbConnexion($host, $dbName, $user, $password);
    $state     = array('dbHost' => TRUE, 'dbIds' => TRUE, 'dbName' => TRUE, 'tblPrefix' => TRUE);
    $ctrl      = TRUE;
    if (!is_object($connexion)) {
        if ($connexion === 2002) {
            $state['dbHost'] = 10;
            $ctrl = FALSE;
        }
        if ($connexion === 1045) {
            $state['dbIds'] = 20;
            $ctrl = FALSE;
        }
        if ($connexion === 1044) {
            $state['dbName'] = 30;
            $ctrl = FALSE;
        }
    }
    if (empty($dbName)) {
        $state['dbName'] = 31;
        $ctrl = FALSE;
    }
    if (!empty($tblPrefix)) {
        if (preg_match('/([^A-Za-z])/', $tblPrefix)) {
            $state['tblPrefix'] = 40;
            $ctrl = FALSE;
        }
    }
    if ($ctrl === TRUE) {
        if (ifDbTableExist($connexion, $tblPrefix) === FALSE) {
            $state['tblPrefix'] = 41;
        }
        else {
            $state = TRUE;
        }
    }
    return $state;
}

function ifDbTableExist($instance, $tblPrefix)
{
    if (is_object($instance)) {
        $tables = $instance->prepare("SHOW TABLES LIKE '" . $tblPrefix . "%'");
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

function checkScriptRights()
{
    return is_writable($_SERVER['DOCUMENT_ROOT']);
}

function writeConfigFile()
{
    $fileName = 'config.php';
    $fileUri  = '../';
    
    $configContent  = '<?php' . PHP_EOL;
    $configContent .= $GLOBALS['comment1'] . PHP_EOL;
    $configContent .= '$system = ' . var_export($GLOBALS['system'], true) . ';' . PHP_EOL;
    $configContent .= PHP_EOL;
    $configContent .= $GLOBALS['comment2'] . PHP_EOL;
    $configContent .= '$config = ' . var_export($GLOBALS['config'], true) . ';' . PHP_EOL;
    $configContent .= PHP_EOL;
    $configContent .= $GLOBALS['comment3'] . PHP_EOL;
    $configContent .= '?>';

    $configContent = str_replace("  'appVers' => ''", "  'appVers' => \$system['version']", $configContent);
    $configContent = str_replace('  ', '    ', $configContent);

    file_put_contents($fileUri . $fileName, $configContent, LOCK_EX);
}

function httpAcceptLanguageToArray()
{
    $string   = str_replace(',', ';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $elements = explode(';', $string);
    $nb       = count($elements);
    $q        = "1";
    for ($i = 0; $i < $nb; $i++) {
        if (strpos($elements[$i], 'q=') === FALSE) {
            $n = $i + 1;
            if (strpos($elements[$n], 'q=') !== FALSE) {
                $q = substr($elements[$n], 2);
            }
            $languages[strtolower($elements[$i])] = $q;
        }
        unset($q);
    }
    arsort($languages);
    return $languages;
}

function getUserLanguage()
{
    $languages = array_flip(httpAcceptLanguageToArray());
    $languages = array_values($languages);
    $favorite  = $languages[0];
    if (strlen($favorite) === 2) {
        $favorite .= '-' . $favorite;
    }
    $favorite = preg_replace_callback('/(\w+)-(\w+)/i',
        function($matches) {
            return $matches[1] . "_" . strtoupper($matches[2]);
        }, $favorite);
    return $favorite;
}

function arrayToSelect($languages, $post)
{
    if (is_array($languages)) {
        $select = '<select name="lang" id="lang" onChange="javascript:this.form.submit();">';
        foreach ($languages as $option) {
            if ($post === $option || (getUserLanguage() === $option && $selected !== 'selected')) {
                $selected = 'selected';
            }
            else {
                unset($selected);
            }
            $select .= '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
        }
        $select .= '</select>';
    }
    return $select;
}

function listAvailableLanguages()
{
    if (($dir = opendir('../lang'))) {
        while (($file = readdir($dir)) !== FALSE) {
            if ($file !== '.' && $file !== '..') {
                $languages[] = substr($file, 0, -4);
            }
            unset($selected);
        }
        closedir($dir);
    }
    return $languages;
}

function install($resetPassword = FALSE)
{
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 10));

    if ($resetPassword !== TRUE) {
        if (file_exists('schema.sql'))
        {
            $schema = file_get_contents('schema.sql');
            if (!empty($_SESSION['tblPrefix'])) {
                $tblPrefix = $_SESSION['tblPrefix'] . '_';
                $schema = str_replace($GLOBALS['config']['tblPrefix'], $tblPrefix, $schema);
            }
            $instance = dbConnexion($_SESSION['dbHost'], $_SESSION['dbName'], $_SESSION['dbUser'], $_SESSION['dbPass']);
            $stmt     = $instance->prepare($schema);
            $stmt->execute();
            if ($stmt !== FALSE) {
                $GLOBALS['config']['dbHost']     = $_SESSION['dbHost'];
                $GLOBALS['config']['dbName']     = $_SESSION['dbName'];
                $GLOBALS['config']['dbUser']     = $_SESSION['dbUser'];
                $GLOBALS['config']['dbPass']     = $_SESSION['dbPass'];
                $GLOBALS['config']['tblPrefix']  = $tblPrefix;
                $GLOBALS['config']['lang']       = $_SESSION['lang'];
                $GLOBALS['config']['users']      = array($_SESSION['user'] => array('hash' => $password, 'email' => $_SESSION['email']));
                writeConfigFile();
                $install = TRUE;
            }
        }
    }
    else {
        writeConfigFile(array('config>users>' . $_SESSION['user'] => 'array(\'hash\' => \'' . $password . '\', \'email\' => \'' .$_SESSION['email'] . '\');'));
        $install = TRUE;
    }
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . 'cache')) {
        if (mkdir($_SERVER['DOCUMENT_ROOT'] . 'cache')) {
            $install = TRUE;
        }
    }
    return $install;
}

if (!file_exists('../config.php')) {
    session_start();
    $lang = $_SESSION['lang'];
    if (empty($_POST['lang']) && empty($lang)) {
        $lang = getUserLanguage();
    }
    elseif (!empty($_POST['lang'])) {
        $lang = $_POST['lang'];
    }

    timply::setUri('');
    timply::setFileName('installer.html');
    timply::addDictionary('../lang/' .$lang . '.php');
    $html = new timply();

    $html->setElement('scriptDir', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));
    $html->setElement('defaultTblPrefix', rtrim($GLOBALS['config']['tblPrefix'], '_'));

    if (!empty($_POST) && implode('', $_POST) !== $lang) {
        if (empty($GLOBALS['config']['dbName'])) {
            if (empty($_POST['dbhost']))    $_POST['dbhost']   = 'localhost';
            if (empty($_POST['user']))      $_POST['user']     = $_POST['dbuser'];
            if (empty($_POST['password']))  $_POST['password'] = $_POST['dbpass'];
            if (empty($_POST['tblprefix'])) $_POST['tblprefix'] = rtrim($GLOBALS['config']['tblPrefix'], '_');
            $_SESSION['lang']      = $_POST['lang'];
            $_SESSION['dbHost']    = $_POST['dbhost'];
            $_SESSION['dbUser']    = $_POST['dbuser'];
            $_SESSION['dbPass']    = $_POST['dbpass'];
            $_SESSION['dbName']    = $_POST['dbname'];
            $_SESSION['tblPrefix'] = $_POST['tblprefix'];
            $_SESSION['user']      = $_POST['user'];
            $_SESSION['pass']      = $_POST['password'];
            $_SESSION['email']     = $_POST['email'];
            $dbState               = checkDbIds($_SESSION['dbHost'], $_SESSION['dbUser'], $_SESSION['dbPass'], $_SESSION['dbName'], $_SESSION['tblPrefix']);
        }
        else {
            $_SESSION['lang']      = $GLOBALS['config']['lang'];
            $_SESSION['dbHost']    = $GLOBALS['config']['dbHost'];
            $_SESSION['dbUser']    = $GLOBALS['config']['dbUser'];
            $_SESSION['dbPass']    = $GLOBALS['config']['dbPass'];
            $_SESSION['dbName']    = $GLOBALS['config']['dbName'];
            $_SESSION['tblPrefix'] = $GLOBALS['config']['tblPrefix'];
            $_SESSION['user']      = $GLOBALS['config']['user'];
            $_SESSION['email']     = $GLOBALS['config']['email'];
            $_SESSION['pass']      = $_POST['password'];
            $dbState               = TRUE;
            $resetPassword         = TRUE;
        }
        $html->setElement('postDbHost', $_SESSION['dbHost']);
        $html->setElement('postDbUser', $_SESSION['dbUser']);
        $html->setElement('postDbPass', $_SESSION['dbPass']);
        $html->setElement('postDbName', $_SESSION['dbName']);
        $html->setElement('postTblPrefix', $_SESSION['tblPrefix']);
        $html->setElement('postUser', $_SESSION['user']);
        $html->setElement('postPass', $_SESSION['pass']);
        $html->setElement('postEmail', $_SESSION['email']);

        $chmodState = checkScriptRights();

        if ($dbState !== TRUE) {
            if (is_array($dbState)) {
                foreach ($dbState as $key => $value) {
                    if (is_int($value)) {
                        $html->setElement('notChecked' .  $key, 'visible');
                        $html->setElement('dbError', '[trad::db_error_' . $value . ']', 'dbErrors');
                    }
                    elseif ($value === TRUE) {
                        $html->setElement('checked' .  $key, 'visible');
                    }
                }
            }
        }
        else {
            $html->setElement('checkedDb', 'visible');
            $html->setElement('dbSuccess', '[trad::db_infos_correct]');
        }
        if ($chmodState === FALSE) {
            $html->setElement('notCheckedConfigFile', 'visible');
            $html->setElement('configFileError', '[trad::script_dir_is_not_writable]');
        }
        else {
            $html->setElement('checkedConfigFile', 'visible');
            $html->setElement('configFileSuccess', '[trad::script_dir_is_writable]');
        }
        if ($dbState === TRUE && $chmodState === TRUE) {
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
            if ($install === true) {
                echo '<h2>Installation OK</h2>';
                echo '<p><a href="/admin.php">Click here to add your first quote :-)</a></p>';
                exit();
            }
            else {
                echo '<h2>Installation FAIL</h2>';
                echo '<p>Please, remove your config file and retry</p>';
                exit();
            }
        }
    }
    $html->setElement('test', '<input type="submit" name="test" value="[trad::test_datas]">');
    $html->setElement('langSelect', arrayToSelect(listAvailableLanguages(), $lang));
    echo $html->returnHtml();
}
else {
    header('Status: 301 Moved Permanently', false, 301);
    header('Location: /');
    exit();
}
?>
