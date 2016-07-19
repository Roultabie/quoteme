<?php
header('Content-Type: text/html; charset=utf-8');
/**
 * Construct permalink and token query
 * (if a permalink is called)
 * DO NOT REMOVE !
 */
if (is_array($_GET)) {
    $elements = array_chunk($_GET, 1, true);
    if (is_array($elements)) {
        if (is_array($elements[0])) {
            $permalink  = array_flip($elements[0]) ;
            $permalink  = array_shift($permalink);
            $_GET['w']  = 'permalink';
            $_GET['wo'] = 'equal,' . $permalink;
        }
        if (is_array($elements[1])) {
            $token         = array_flip($elements[1]);
            $token         = array_shift($token);
            $_GET['token'] = $token;
        }
    }
}
/**
 * Loading parser
 */
require_once 'api.php';

/**
 * Loading libs
 */
require_once 'libs/smartypants.php';

timply::addDictionary($GLOBALS['config']['langDir'] . $GLOBALS['config']['lang'] . '.php');

$html  = new timply('index.html');
$quote = $GLOBALS['quoteObj']['obj'][0];
$fullBase = rtrim('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PATH_INFO'], '/');
if (is_object($quote)) {
    $html->setElement('title', $quote->getAuthor());
    $html->setElement('imgLink', $fullBase . '/api.php?p=img&wi=1024&w=permalink&wo=equal,' . $quote->getPermalink());
    $html->setElement('imgWidth', '1024');
    $html->setElement('text', SmartyPants(str_replace(PHP_EOL, '<br>', $quote->getText()), 'f+:+t+h+H+'));
    $html->setElement('author', SmartyPants($quote->getAuthor()));
    $html->setElement('tagList', $quote->getTags());
    $html->setElement('searchByAuthor', $fullBase . '/api.php?p=html&w=author&wo=equal,' . $quote->getAuthor());
    $html->setElement('source', SmartyPants($quote->getSource()));
    $html->setElement('permalink', $fullBase . '/?' .$quote->getPermalink());
}
$html->setElement('pageType', 'index');
$html->setElement('nbQuotes', $GLOBALS['quoteObj']['nb']);
$html->setElement('fullBase', $fullBase);
$html->setElement('version', $GLOBALS['system']['version']);
if (!empty($_SESSION['userDatas'])) $html->setElement('adminLink', $fullBase . '/admin/', 'AdminLink');

echo $html->returnHtml();
?>
