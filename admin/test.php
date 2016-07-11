<?php
$perpage = 7;
$page = (empty($_GET['page'])) ? 1 : $_GET['page'];
$offset = ($page - 1) * $perpage;
$query ='SELECT id, quote, author, source, tags, permalink, date
FROM qm_quotes INNER JOIN (
   SELECT id
   FROM qm_quotes WHERE author LIKE "%ou%"
   ORDER BY id DESC
   LIMIT ' . $perpage . '
   OFFSET ' . $offset . '
)
AS result USING(id)';

$query2='EXPLAIN SELECT *
   FROM qm_quotes
   ORDER BY id DESC LIMIT 20;';

$stmt = dbConnexion::getInstance()->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();
$stmt = NULL;
echo '<pre>';
var_dump($result);
echo '</pre>';

$html = new timply('allquotes.html');
$html->setElement('aqhover', $GLOBALS['navHover']);
$quotes = new quoteQueries();
// $quotes = $quotes->getQuote(array('sort' => 'id,desc'));
// if (is_array($quotes)) {
//     foreach ($quotes as $quote) {
//         $tags = explode(',', $quote->getTags());
//         foreach ($tags as $tag) {
//             if (!empty($tag)) {
//                 $finalTags .= '<span class="button tag">' . $tag . '</span>';
//             }
//         }
//         $html->setElement('quoteText', SmartyPants(str_replace(PHP_EOL, '<br>', $quote->getText()), 'f+:+t+h+H+'), 'quote');
//         $html->setElement('quoteAuthor', SmartyPants($quote->getAuthor()), 'quote');
//         $html->setElement('quoteSource', SmartyPants($quote->getSource()), 'quote');
//         $html->setElement('quoteTags', $finalTags, 'quote');
//         $html->setElement('quoteDate', SmartyPants($quote->getDate()), 'quote');
//         $html->setElement('edit', '?' . http_build_query(array('p' => 'eq', 'o' => $_GET['p'], 'permalink' => $quote->getPermalink()), '', '&'), 'quote');
//         $html->setElement('delete', '?' . http_build_query(array('p' => 'dq', 'o' => $_GET['p'], 'permalink' => $quote->getPermalink(), 'anchor' => $parentPermalink), '', '&'), 'quote');
//         $html->setElement('quotePermalink', $quote->getPermalink(), 'quote');
//         $html->setElement('googleShareLink', 'https://plus.google.com/share?url=http://' . $_SERVER['HTTP_HOST'] . '/?' . $quote->getPermalink(), 'quote');
//         $html->setElement('facebookShareLink', 'http://facebook.com/sharer.php?u=http://' . $_SERVER['HTTP_HOST'] . '/?' . $quote->getPermalink(), 'quote');
//         $html->setElement('twitterShareLink', 'http://twitter.com/intent/tweet?url=http://' . $_SERVER['HTTP_HOST'] . '/?' . $quote->getPermalink() . '&text=' . $quote->getAuthor() . ' said:', 'quote');
//         if (!empty($userConfig['shaarli'])) {
//             $html->setElement('ifShaarli', '<a class="icon-shaarli" href="' . rtrim($userConfig['shaarli'], '/') . '/?post=http://' . $_SERVER['HTTP_HOST'] . '/?' . $quote->getPermalink() . '&source=bookmarklet" onclick="javascript:window.open(this.href,\'\',\'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;">Shaarli</a>', 'quote');
//         }
//         $parentPermalink = $quote->getPermalink();
//         unset($finalTags);
//     }
// }