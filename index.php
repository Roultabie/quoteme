<?php
/**
 * Construct permalink query
 * (if a permalink is called)
 * DO NOT REMOVE !
 */
$_GET      = array_flip($_GET) ;
$permalink = array_shift($_GET);
if (!empty($permalink)) {
    $_GET = array('w' => 'permalink', 'wo' => 'equal,' . $permalink);
}

/**
 * Loading parser
 */
require_once 'api.php';

/**
 * Loading libs
 */
require_once 'libs/smartypants.php';
$html  = new timply('index.html');
$quote = $GLOBALS['quoteObj']['obj'][0];
if (is_object($quote)) {
    $html->setElement('text', SmartyPants($quote->getText(), 'f+:+t+h+H+'));
    $html->setElement('author', SmartyPants($quote->getAuthor()));
    $html->setElement('source', SmartyPants($quote->getSource()));
    $html->setElement('permalink', '?' .$quote->getPermalink());
}
$html->setElement('nbQuotes', $GLOBALS['quoteObj']['nb']);
echo $html->returnHtml();
?>