<?php
/**
 * Loading parser
 */
require_once 'api.php';

/**
 * Loading libs
 */
require_once 'libs/smartypants.php';

define('TIMPLY_DIR', 'themes/simple/');

$html     = new timply('index.html');
$quote = $GLOBALS['quoteObj']['obj'][0];
if (is_object($quote)) {
    $html->setElement('text', SmartyPants($quote->getText(), 'f+:+t+h+H+'));
    $html->setElement('author', SmartyPants($quote->getAuthor()));
    $html->setElement('source', SmartyPants($quote->getSource()));
}
$html->setElement('nbQuotes', $GLOBALS['quoteObj']['nb']);
echo $html->returnHtml();
?>