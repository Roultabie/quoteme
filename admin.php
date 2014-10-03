<?php
/**
 * Loading configuration
 */
require_once 'config.php';

/**
 * Loading libs
 */
if (!function_exists('password_hash')) {
    $hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
    $test = crypt("password", $hash);
    $pass = $test == $hash;
    if ($pass) {
        require 'libs/password.php';
    }
    else {
        echo '<h3>You need passowrd_compat support to continue</h3>';
        exit();
    }
}
require_once 'libs/login.php';
require_once 'libs/mysql.php';
require_once 'libs/quoteme.php';
require_once 'libs/timply.php';
require_once 'libs/smartypants.php';
require_once 'parser/parser.php';

parser::$cacheState = TRUE;
parser::$cacheDir   = $GLOBALS['config']['cacheDir'];

$userDatas  = unserialize($_SESSION['userDatas']);
$userConfig = $userDatas->getConfig();

function writeConfigFile()
{
    $fileName = 'config.php';
    $fileUri  = '';
    $config = file_get_contents($fileUri . $fileName);
    $pattern = '/\$(?:system|config) = (array \([^\);]*)/u';
    preg_match_all($pattern, $config, $toReplace);
    // Changing system options
    $newConfig = str_replace($toReplace[1][0], rtrim(var_export($GLOBALS['system'], true), ')'), $config);
    file_put_contents($fileUri . $fileName, $newConfig, LOCK_EX);
}

function editConfig($newConfig)
{
    //on récupere config.php dans un tableau
    $config = file('config.php');
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
    if (!$fd = fopen('config.php',"w+")) {
    echo "Echec de l'ouverture du fichier";
    }
    foreach($tmpConfig as $val) {
        fwrite($fd, $val);
    }
}

function getUpdate()
{
    $currentDate = date($GLOBALS['system']['dateFormat']);
    
    if ($GLOBALS['config']['appVers'] !== 'devel') {
        if ($GLOBALS['system']['lastUpdate'] < $currentDate) {
            $update = file_get_contents('http://q.uote.me/checkupdate.php?cliv=' . $GLOBALS['config']['appVers']);
            //writeConfigFile(array('system>lastVersion' => $update, 'system>lastUpdate' => $currentDate));
        }
    }
    
}

/**
* return tags in json format
*
*/
function jsonTagsByHits($string, $limit = '4', $order = 'DESC')
{
    $query   = "SELECT tag, hits FROM " . $GLOBALS['config']['tblPrefix'] . "tags WHERE tag LIKE '" . $string . "%' ORDER BY hits " . $order . " LIMIT " . $limit . ';';
    $stmt    = dbConnexion::getInstance()->prepare($query);
    $stmt->execute();
    $datas = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    if (count($datas) > 0) {
        array_walk($datas, function (&$item, $key) {
            $item = array('value' => $item);
        });
        $result['status'] = 'success';
        $result['data']   = $datas;
    }
    else {
        $result['status'] = 'error';
    }
    $stmt->closeCursor();
    $stmt = NULL;
    return json_encode($result);
}

/**
* return authors in json format
*
*/
function jsonAuthorsByHits($string, $limit = '4', $order = 'DESC')
{
    $query   = "SELECT author, hits FROM " . $GLOBALS['config']['tblPrefix'] . "authors WHERE author LIKE '" . $string . "%' ORDER BY hits " . $order . " LIMIT " . $limit . ';';
    $stmt    = dbConnexion::getInstance()->prepare($query);
    $stmt->execute();
    $datas = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    if (count($datas) > 0) {
        array_walk($datas, function (&$item, $key) {
            $item = array('value' => $item);
        });
        $result['status'] = 'success';
        $result['data']   = $datas;
    }
    else {
        $result['status'] = 'error';
    }
    $stmt->closeCursor();
    $stmt = NULL;
    return json_encode($result);
}

if ($GLOBALS['system']['version'] !== $GLOBALS['system']['lastVersion']) {
    $updateInfo = '<a href="https://github.com/Roultabie/quoteme/releases">[trad::new_update_available] : ' . $GLOBALS['system']['lastVersion'] . '</a>';
}

$quote = new quoteQueries();

if (!empty($_POST)) {
    if ($_POST['action'] === "edit") {
        $edit = $quote->editQuote($_POST['permalink'], $_POST['text'], $_POST['author'], $_POST['source'], $_POST['tags']);
    }
    else {
        $add = $quote->addQuote($_POST['text'], $_POST['author'], $_POST['source'], $_POST['tags']);
    }
    parser::clearCache();
}

if (isset($_GET['tag']) || isset($_GET['author'])) {
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Ven, 11 Oct 2011 23:32:00 GMT');
    header('Content-type: application/json');
    if (!empty($_GET['tag'])) {
        $result = jsonTagsByHits($_GET['tag']);
    }
    elseif (!empty($_GET['author'])) {
        $result = jsonAuthorsByHits($_GET['author']);
    }
    else {
        $result['status'] = 'error';
        $result = json_encode($result);
    }
    echo $result;
    exit;
}

getUpdate();

if ($_GET['action'] === "edit") {
    $editQuote     = $quote->getQuote(array('where' => 'permalink', 'whereOpt' => 'equal,' . $_GET['permalink']));
    $formPermalink = $editQuote[0]->getPermalink();
    $formText      = $editQuote[0]->getText();
    $formAuthor    = $editQuote[0]->getAuthor();
    $formSource    = $editQuote[0]->getSource();
    $formTags      = $editQuote[0]->getTags();
    $formAction    = 'edit';
}
if ($_GET['action'] === "delete" && !empty($_GET['permalink'])) {
    $del = $quote->delQuote($_GET['permalink']);
    parser::clearCache();
}

timply::setUri($GLOBALS['config']['themeDir']);
timply::setFileName('admin.html');
timply::addDictionary($GLOBALS['config']['langDir'] . 'en_EN.php');
timply::addDictionary($GLOBALS['config']['langDir'] . 'fr_FR.php');
$html = new timply();

$html->setElement('themeDir', $GLOBALS['config']['themeDir']);

/* Update link */
$html->setElement('updateInfo', $updateInfo);

/* Form */
$html->setElement('formPermalink', $formPermalink);
$html->setElement('formInputText', $formText);
$html->setElement('formInputAuthor', $formAuthor);
$html->setElement('formInputSource', $formSource);
$html->setElement('formInputTags', $formTags);
$html->setElement('formAction', $formAction);

/* Quotes list */
$quotes = new quoteQueries();
$quotes = $quotes->getQuote(array('sort' => 'id,desc'));

if (is_array($quotes)) {
    foreach ($quotes as $quote) {
        $html->setElement('quoteTableText', SmartyPants($quote->getText(), 'f+:+t+h+H+'), 'quoteTable');
        $html->setElement('quoteTableAuthor', SmartyPants($quote->getAuthor()), 'quoteTable');
        $html->setElement('quoteTableSource', SmartyPants($quote->getSource()), 'quoteTable');
        $html->setElement('quoteTableTags', SmartyPants($quote->getTags()), 'quoteTable');
        $html->setElement('quoteTableDate', SmartyPants($quote->getDate()), 'quoteTable');
        $html->setElement('edit', '?' . http_build_query(array('action' => 'edit', 'permalink' => $quote->getPermalink()), '', '&'), 'quoteTable');
        $html->setElement('delete', '?' . http_build_query(array('action' => 'delete', 'permalink' => $quote->getPermalink()), '', '&'), 'quoteTable');
        $html->setElement('permalink', $quote->getPermalink(), 'quoteTable');
        $html->setElement('googleShareLink', 'https://plus.google.com/share?url=http://' . $_SERVER['HTTP_HOST'] . '/?' . $quote->getPermalink(), 'quoteTable');
        $html->setElement('facebookShareLink', 'http://facebook.com/sharer.php?u=http://' . $_SERVER['HTTP_HOST'] . '/?' . $quote->getPermalink(), 'quoteTable');
        $html->setElement('twitterShareLink', 'http://twitter.com/intent/tweet?url=http://' . $_SERVER['HTTP_HOST'] . '/?' . $quote->getPermalink() . '&text=' . $quote->getAuthor() . ' said:', 'quoteTable');
        if (!empty($userConfig['shaarli'])) {
            $html->setElement('shaarli', '<a class="icon-shaarli" href="' . rtrim($userConfig['shaarli'], '/') . '/?post=http://' . $_SERVER['HTTP_HOST'] . '/?' . $quote->getPermalink() . '" onclick="javascript:window.open(this.href,\'\',\'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;">Shaarli</a>', 'quoteTable');
        }
        
    }
}
echo $html->returnHtml();
?>
