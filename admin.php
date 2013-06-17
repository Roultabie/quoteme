<?php
/**
 * Loading configuration
 */
require_once 'config.php';

/**
 * Loading libs
 */
require_once 'libs/mysql.php';
require_once 'libs/quoteme.php';
require_once 'libs/timply.php';
require_once 'libs/smartypants.php';

$html = new timply('admin.html');


/* Quotes list */
$quotes = new quoteQueries();
$quotes = $quotes->getQuote(array('sort' => 'id,desc'));
if (is_array($quotes)) {
    foreach ($quotes as $quote) {
        $html->setElement('quoteTableId', SmartyPants($quote->getId(), 'f+:+t+h+H+'), 'quoteTable');
        $html->setElement('quoteTableText', SmartyPants($quote->getText(), 'f+:+t+h+H+'), 'quoteTable');
        $html->setElement('quoteTableAuthor', SmartyPants($quote->getAuthor()), 'quoteTable');
        $html->setElement('quoteTableSource', SmartyPants($quote->getSource()), 'quoteTable');
        $html->setElement('quoteTableTags', SmartyPants($quote->getTags()), 'quoteTable');
        $html->setElement('quoteTableDate', SmartyPants($quote->getDate()), 'quoteTable');
        $html->setElement('quoteTablePermalink', '?' .$quote->getPermalink(), 'quoteTable');
    }
}

echo $html->returnHtml();
?>