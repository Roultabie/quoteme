<?php
/**
 * Loading libs
 */
require_once 'libs/quoteme.php';
require_once 'libs/timply.php';
require_once 'libs/smartypants.php';


$html  = new timply('index.html');
$html->setElement('text', SmartyPants($quote->getText()));
$html->setElement('author', SmartyPants($quote->getAuthor()));
$html->setElement('source', SmartyPants($quote->getSource()));
echo $html->returnHtml();
?>