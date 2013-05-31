<?php
/**
 * Loading libs
 */
require_once 'libs/mysql.php';
require_once 'libs/quoteme.php';
require_once 'libs/timply.php';
require_once 'libs/smartypants.php';

define('TIMPLY_DIR', 'themes/simple/');
define('DB_HOST', 'localhost');
define('DB_NAME', 'quoteme');
define('DB_USR', 'dbuser');
define('DB_PWD', 'pass');

$html     = new timply('index.html');
$quote    = new quoteQueries();
$nbQuotes = quoteQueries::$nbQuotes;
$quote    = $quote->getQuote();
$html->setElement('text', SmartyPants($quote[0]->getText(), 'f+:+t+h+H+'), 'Content');
$html->setElement('author', SmartyPants($quote[0]->getAuthor()), 'Content');
$html->setElement('source', SmartyPants($quote[0]->getSource()), 'Content');
$html->setElement('nbQuotes', $nbQuotes, 'Footer');
echo $html->returnHtml();
?>