<?php
/**
 * Loading configuration
 */
require_once 'config.php';

/**
 * Loading libs
 */
require_once 'libs/login.php';
require_once 'libs/mysql.php';
require_once 'libs/quoteme.php';
require_once 'libs/timply.php';
require_once 'libs/smartypants.php';
require_once 'parser/parser.php';

parser::$cacheState = TRUE;
parser::$cacheDir   = $GLOBALS['config']['cacheDir'];

function writeConfigFile($newOptions)
{
    $fileName = 'config.php';
    $fileUri  = '';
    $configContent = file_get_contents($fileUri . $fileName);
    foreach ($newOptions as $key => $value) {
        list($type, $option) = explode(">", $key);
        if (is_string($value)) {
            $value = "'" . str_replace("'", "\'", $value) . "'";
        }
        $pattern       = '/\$' . $type . '\[\'' . $option . '\'\]\s*=\s*[\'"]{0,1}.*[\'"]{0,1};/i';
        $replace       = '$' . $type . '[\'' . $option . '\'] = ' .$value . ';';
        $configContent = preg_replace($pattern, $replace, $configContent);
    }
    file_put_contents($fileUri . $fileName, $configContent, LOCK_EX);
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
    
    if ($GLOBALS['system']['lastUpdate'] < $currentDate) {
        $update = file_get_contents('http://q.uote.me/checkupdate.php?cliv=' . $GLOBALS['config']['appVers']);
        //writeConfigFile(array('system>lastVersion' => $update, 'system>lastUpdate' => $currentDate));
    }
    
}

getUpdate();

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
    }
}
echo $html->returnHtml();
?>
