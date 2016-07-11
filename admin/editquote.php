<?php
$quote = new quoteQueries();

if (!empty($_POST) && $_GET['p'] === 'eq') {
     $edit = $quote->editQuote($_POST['permalink'], $_POST['text'], $_POST['author'], $_POST['source'], $_POST['tags']);
     parser::clearCache();
     header('Location: ?p=' . $_GET['o'] . '#' . $_POST['permalink']);
}

$html = new timply('editquote.html');

$editQuote     = $quote->getQuote(array('where' => 'permalink', 'whereOpt' => 'equal,' . $_GET['permalink']));

$html->setElement('quotePermalink', $editQuote[0]->getPermalink());
$html->setElement('quoteText', $editQuote[0]->getText());
$html->setElement('quoteAuthor', $editQuote[0]->getAuthor());
$html->setElement('quoteSource', $editQuote[0]->getSource());
$html->setElement('quoteTags', $editQuote[0]->getTags());

$html->setElement($_GET['o'] . 'hover', $GLOBALS['navHover']);