<?php
// Adding new quote
if (!empty($_POST) && !isset($_POST['edit'])) {
     $quote = new quoteQueries();
     $add   = $quote->addQuote($_POST['text'], $_POST['author'], $_POST['source'], $_POST['tags']);
     parser::clearCache();
}
$html = new timply('newquote.html');
$html->setElement('nqhover', $GLOBALS['navHover']);