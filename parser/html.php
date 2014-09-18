<?php
class htmlParser
extends parser
implements parserTemplate
{

    private $timply;

    function __construct()
    {
        timply::setUri($GLOBALS['config']['themeDir']);
        timply::setFileName('html.html');
        timply::addDictionary($GLOBALS['config']['langDir'] . $GLOBALS['config']['lang'] . '.php');
        $this->timply = new timply();
    }

    public function parse($elements)
    {
        $title = (!empty($GLOBALS['opt']['where'])) ? '[trad::search-result-by::F] [trad::' . $GLOBALS['opt']['where'] . ']' : '[trad::latest-quotes::F]';
        $this->timply->setElement('title', $title);
        $this->timply->setElement('nbQuotes', quoteQueries::$nbQuotes);
        if (is_array($elements)) {
            foreach ($elements as $value) {
                $this->timply->setElement('text', $value->getText(), 'Quote');
                $this->timply->setElement('permalink', $this->returnPermalink($value->getPermalink()), 'Quote');
                $this->timply->setElement('description', $value->getText(), 'Quote');
                $this->timply->setElement('author', $value->getAuthor(), 'Quote');
            }
        }
        return $this->timply->returnHtml();
    }

    public function loadHeader($elements = '')
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Ven, 07 Oct 2011 23:32:00 GMT');
        header('Content-Type: text/html; charset=utf-8');
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