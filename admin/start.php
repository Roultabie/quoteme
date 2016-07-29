<?php
$html = new timply('start.html');

require BASE_URL . 'libs/stats.php';

$statsObj = new stats();
$stats    = $statsObj->getStats();


$allQuotes = new quoteQueries();
$quotes = $allQuotes->getQuote(array('sort' => 'id,desc', 'limit'=> '5'));

if (is_array($quotes)) {
    foreach ($quotes as $quote) {
        $html->setElement('quote', SmartyPants($quote->getText(), 'f+:+t+h+H+'), 'allQuotes');
        $html->setElement('author', SmartyPants($quote->getAuthor()), 'allQuotes');
        $html->setElement('source', SmartyPants($quote->getSource()), 'allQuotes');
        $html->setElement('tags', $finalTags, 'allQuotes');
        $html->setElement('permalink', $quote->getPermalink(), 'allQuotes');
    }
}

$myQuotes = $allQuotes->getQuote(array('sort' => 'id,desc', 'limit'=> '5', 'where' => 'user', 'whereOpt' => 'equal,' . $userConfig['id']));
if (is_array($myQuotes)) {
    foreach ($myQuotes as $quote) {
        $html->setElement('mQuote', SmartyPants($quote->getText(), 'f+:+t+h+H+'), 'userQuotes');
        $html->setElement('mAuthor', SmartyPants($quote->getAuthor()), 'userQuotes');
        $html->setElement('mSource', SmartyPants($quote->getSource()), 'userQuotes');
        $html->setElement('mTags', $finalTags, 'userQuotes');
        $html->setElement('mPermalink', $quote->getPermalink(), 'userQuotes');
    }
}

/* Stats */
//$stats = getStats();
//$date = new DateTime($stats[0]['date']);
//$html->setElement('lastGen', date_format($date, 'd-m-Y H:i'));
$html->setElement('nbQuotes', $statsObj->getLiveStat('quotes'));
$html->setElement('nbDelivered', $stats[0]->delivered);
$html->setElement('nbDeliveredToday', $stats[0]->delivered_today);
$html->setElement('nbUsers', $statsObj->getLiveStat('allUsers'));
//$html->setElement('nbContributors', $stats[0]->contributors);
//$html->setElement('nbEditors', $stats[0]->editors);
//$html->setElement('nbParsers', $stats[0]['parsers']);
//$html->setElement('nbActiveParsers', $stats[0]['parsers_active']);
