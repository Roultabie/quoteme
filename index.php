<?php
/**
 * Loading libs
 */
require_once 'libs/quoteme.php';
require_once 'libs/timply.php';
require_once 'libs/smartypants.php';
define('TIMPLY_DIR', 'themes/simple/');

$html  = new timply('index.html');
$quote = new quoteQueries();
$quote = $quote->getQuote();
$html->setElement('text', SmartyPants($quote[0]->getText()));
$html->setElement('author', SmartyPants($quote[0]->getAuthor()));
$html->setElement('source', SmartyPants($quote[0]->getSource()));
echo $html->returnHtml();
?>