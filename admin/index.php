<?php
define('BASE_URL', str_replace('admin/index.php', '', __FILE__));

require_once BASE_URL . 'config.php';
require_once BASE_URL . 'libs/mysql.php';
require_once 'libs/user.php';

if (!function_exists('password_hash')) {
    $hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
    $test = crypt("password", $hash);
    $pass = $test == $hash;
    if ($pass) {
        require BASE_URL . 'libs/password.php';
    }
    else {
        echo '<h3>You need passowrd_compat support to continue</h3>';
        exit();
    }
}

userQueries::initLogin();

require_once BASE_URL . 'libs/login.php';
require_once BASE_URL . 'libs/timply.php';
require_once BASE_URL . 'libs/quoteme.php';
require_once 'libs/sqlQueries.php';
require_once BASE_URL . 'libs/smartypants.php';
require_once BASE_URL . 'parser/parser.php';
require_once 'pagination.php';

parser::$cacheState = false;
parser::$cacheDir   = $GLOBALS['config']['cacheDir'];

$user = new userQueries();
$userConfig = $user->config;

// Adding new quote
if (!empty($_POST) && isset($_POST['add'])) {
     $quote = new quoteQueries();
     $add   = $quote->addQuote($_POST['text'], $_POST['author'], $_POST['source'], $_POST['tags'], $userConfig['id']);
     parser::clearCache();
}

// System declaration
$navHover = 'hover';

timply::setUri(BASE_URL . 'admin/themes/default');
timply::addDictionary('fr_FR.php');

// AJAX
if (isset($_GET['tag']) || isset($_GET['author'])) {
    require 'ajax.php';
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Ven, 07 Oct 2011 23:32:00 GMT');
    header('Content-type: application/json');
    if (!empty($_GET['tag'])) {
        $result = jsonTagsByHits($_GET['tag'], $_GET['not']);
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

if (empty($_GET['p'])) require 'start.php';
if ($_GET['p'] === 'nq') require 'newquote.php';
if ($_GET['p'] === 'aq') require 'allquotes.php';
if ($_GET['p'] === 'md') require 'metadatas.php';
if ($_GET['p'] === 'eq') require 'editquote.php';
if ($_GET['p'] === 'dq') require 'deletequote.php';
if ($_GET['p'] === 'pe') require 'perso.php';

$html->setElement('username', $user->datas->username);

echo $html->returnHtml();
