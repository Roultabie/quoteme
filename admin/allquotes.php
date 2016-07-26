<?php
$GLOBALS['config']['perpage'] = 20;
$page = (empty($_GET['page'])) ? 1 : $_GET['page'];
if (!empty($_GET['au'])) {
    $field = 'author';
    $search = $_GET['s'];
}
if (!empty($_GET['so'])) {
    $field = 'source';
    $search = $_GET['s'];
}
if (!empty($_GET['tg'])) {
    $field = 'tags';
    $search = $_GET['s'];
}
$html = new timply('allquotes.html');
$html->setElement('aqhover', $GLOBALS['navHover']);
$quotes = new dataQueries('quotes', 'id, quote, author, source, tags, permalink', 'id');
if (!empty($_GET['s'])) {
    $datas = $quotes->searchDatas($field, $search, $page);
}
else {
    $datas = $quotes->getDatas($page);
}
if (is_array($datas)) {
    foreach ($datas as $quote) {
	$taglist = new timply('taglist.html');
        $tags = explode(',', $quote->tags);
        foreach ($tags as $tag) {
            if (!empty($tag)) {
	        $taglist->setElement('tagName', $tag, 'tagList');
            }
        }
        $shareLink = (!empty($user->datas->share_token)) ? urlencode($quote->permalink . '&' . $user->datas->share_token) : $quote->permalink;
        $html->setElement('quoteText', SmartyPants(str_replace(PHP_EOL, '<br>', $quote->quote), 'f+:+t+h+H+'), 'quote');
        $html->setElement('quoteAuthor', SmartyPants($quote->author), 'quote');
        $html->setElement('quoteSource', SmartyPants($quote->source), 'quote');
        $html->setElement('quoteTags', $taglist->returnHtml(), 'quote');
        //$html->setElement('quoteDate', SmartyPants($quote->getDate()), 'quote');
        $html->setElement('edit', '?' . http_build_query(array('p' => 'eq', 'o' => $_GET['p'], 'permalink' => $quote->permalink), '', '&'), 'quote');
        $html->setElement('delete', '?' . http_build_query(array('p' => 'dq', 'o' => $_GET['p'], 'permalink' => $quote->permalink, 'anchor' => $parentPermalink), '', '&'), 'quote');
        $html->setElement('quotePermalink', $quote->permalink, 'quote');
        $html->setElement('googleShareLink', 'https://plus.google.com/share?url=http://' . $_SERVER['HTTP_HOST'] . '/?' . $shareLink, 'quote');
        $html->setElement('facebookShareLink', 'http://facebook.com/sharer.php?u=http://' . $_SERVER['HTTP_HOST'] . '/?' . $shareLink, 'quote');
        $html->setElement('twitterShareLink', 'http://twitter.com/intent/tweet?url=http://' . $_SERVER['HTTP_HOST'] . '/?' . $shareLink . '&text=' . $quote->author . ' said:', 'quote');
        if (!empty($userConfig['shaarli'])) {
            $html->setElement('ifShaarli', '<a class="icon-shaarli" href="' . rtrim($userConfig['shaarli'], '/') . '/?post=http://' . $_SERVER['HTTP_HOST'] . '/?' . $shareLink . '&source=bookmarklet" onclick="javascript:window.open(this.href,\'\',\'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;">Shaarli</a>', 'quote');
        }
        $parentPermalink = $quote->permalink;
        unset($finalTags);
    }
    $pagination = pagination($page, dataQueries::$nbResult, '?' . $_SERVER['QUERY_STRING']);
    $html->setElement('pagination', $pagination);
}
