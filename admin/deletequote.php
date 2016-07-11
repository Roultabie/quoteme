<?php
$quote = new quoteQueries();

$del = $quote->delQuote($_GET['permalink']);
parser::clearCache();

header('Location: ?p=' . $_GET['o'] . '#' . $_GET['anchor']);