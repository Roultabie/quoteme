<?php
class htmlParser
extends parser
implements parserTemplate
{

    private $timply;

    function __construct()
    {
        timply::setUri($GLOBALS['config']['themeDir']);
        timply::addDictionary($GLOBALS['config']['langDir'] . $GLOBALS['config']['lang'] . '.php');
        $this->timply = new timply('html.html');
    }

    public function parse($elements)
    {
        $title = (!empty($GLOBALS['opt']['where'])) ? '[trad::search-result-by::F] [trad::' . $GLOBALS['opt']['where'] . ']' : '[trad::latest-quotes::F]';
        $this->timply->setElement('title', $title);
        $this->timply->setElement('nbQuotes', quoteQueries::$nbQuotes);
        $this->timply->setElement('fullBase', FULL_BASE);
        if (!empty($_SESSION['userDatas'])) $this->timply->setElement('adminLink', $fullBase . '/admin/', 'AdminLink');
        if (is_array($elements)) {
            foreach ($elements as $value) {
                $this->timply->setElement('text', str_replace(PHP_EOL, '<br>', $value->getText()), 'Quote');
                $this->timply->setElement('permalink', FULL_BASE . '/?' . $value->getPermalink(), 'Quote');
                $this->timply->setElement('description', $value->getText(), 'Quote');
                $this->timply->setElement('author', $value->getAuthor(), 'Quote');
                $this->timply->setElement('searchByAuthor', FULL_BASE . '/api.php?p=html&w=author&wo=equal,' . $value->getAuthor(), 'Quote');
            }
        }
        return $this->timply->returnHtml();
    }

    public function loadHeader($elements = '')
    {
        //header('Cache-Control: no-cache, must-revalidate');
        //header('Expires: Ven, 07 Oct 2011 23:32:00 GMT');
        //header('Content-Type: text/html; charset=utf-8');
    }

    private function formatDate($sqlDate)
    {
        $dateTime  = explode(' ', $sqlDate);
        $date      = explode('-', $dateTime[0]);
        $time      = explode('-', $dateTime[1]);
        $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
        $rssDate   = date(DATE_RSS, $timestamp);
        return $rssDate;
    }
}
?>
