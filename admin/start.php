<?php
$html = new timply('start.html');

require BASE_URL . 'libs/stats.php';

$temp = new stats();

function genStats()
{
    $globalCount = 'SELECT
             (SELECT COUNT(*) FROM ' . $GLOBALS['config']['tblPrefix'] . 'quotes) as nbQuotes,
             (SELECT COUNT(*) FROM ' . $GLOBALS['config']['tblPrefix'] . 'authors) as nbAuthors,
             (SELECT COUNT(*) FROM ' . $GLOBALS['config']['tblPrefix'] . 'tags) as nbTags,
             (SELECT COUNT(*) FROM ' . $GLOBALS['config']['tblPrefix'] . 'access) as nbAccess';
    $stmt    = dbConnexion::getInstance()->prepare($globalCount);
    $stmt->execute();
    $datas['glob'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = NULL;

    // https://stackoverflow.com/questions/12789396/how-to-get-multiple-counts-with-one-sql-query
    $membersCount = 'SELECT count(*) nbTotal,
                      sum(case when type = "0" then 1 else 0 end) nbAdmins,
                      sum(case when type = "1" then 1 else 0 end) nbEditors,
                      sum(case when type = "2" then 1 else 0 end) nbContributors
                      FROM ' . $GLOBALS['config']['tblPrefix'] . 'users';
    $stmt    = dbConnexion::getInstance()->prepare($membersCount);
    $stmt->execute();
    $datas['users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = NULL;

    if (is_dir(BASE_URL .'parser')) {
        $list = scandir(BASE_URL .'parser');
        $list = array_diff($list, array('.', '..', 'parser.php', 'template.php'));
        $datas['parser']['total']  = count($list);
    }
    $query = 'INSERT INTO ' . $GLOBALS['config']['tblPrefix'] . 'stats
             (date, access, quotes, authors, tags, parsers, contributors, editors, administrators)
             VALUES (NOW(), :access, :quotes, :authors, :tags, :parsers, :contributors, :editors, :administrators)';
    $stmt = dbConnexion::getInstance()->prepare($query);
    $stmt->bindValue(':access', $datas['glob'][0]['nbAccess'], PDO::PARAM_INT);
    $stmt->bindValue(':quotes', $datas['glob'][0]['nbQuotes'], PDO::PARAM_INT);
    $stmt->bindValue(':authors', $datas['glob'][0]['nbAuthors'], PDO::PARAM_INT);
    $stmt->bindValue(':tags', $datas['glob'][0]['nbTags'], PDO::PARAM_INT);
    $stmt->bindValue(':parsers', $datas['parser']['total'], PDO::PARAM_INT);
    $stmt->bindValue(':contributors', $datas['users'][0]['nbContributors'], PDO::PARAM_INT);
    $stmt->bindValue(':editors', $datas['users'][0]['nbEditors'], PDO::PARAM_INT);
    $stmt->bindValue(':administrators', $datas['users'][0]['nbAdmins'], PDO::PARAM_INT);
    $stmt->execute();
    $stmt = NULL;
}

function getStats()
{
    if (isset($GLOBALS['config']['statsUpdate'])) {
        $toUpdate = (is_int($GLOBALS['config']['statsUpdate'])) ? $GLOBALS['config']['statsUpdate'] : 86400;
        $date     = new DateTime();
        $now      = $date->getTimestamp();
        $expire   = $now - $toUpdate;
        $query    = 'SELECT date, access, quotes, authors, tags, parsers, parsers_active, contributors, editors, administrators
                  FROM ' . $GLOBALS['config']['tblPrefix'] .'stats ORDER BY date DESC LIMIT 1';
        $stmt    = dbConnexion::getInstance()->prepare($query);
        $stmt->execute();
        $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($datas) === 0) {
            genStats();
        }
        $lastGenStats = strtotime($datas[0]['date']);
        if ($lastGenStats <= $expire) {
            genStats();
        }
        return $datas;
        $stmt = NULL;
    }
}

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
$stats = getStats();
$date = new DateTime($stats[0]['date']);
$html->setElement('lastGen', date_format($date, 'd-m-Y H:i'));
$html->setElement('nbQuotes', $stats[0]['quotes']);
$html->setElement('nbAccess', $stats[0]['access']);
$html->setElement('nbContributors', $stats[0]['contributors']);
$html->setElement('nbEditors', $stats[0]['editors']);
$html->setElement('nbAdministrators', $stats[0]['administrators']);
$html->setElement('nbParsers', $stats[0]['parsers']);
$html->setElement('nbActiveParsers', $stats[0]['parsers_active']);
