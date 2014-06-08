<?php
class rss2Parser
extends parser
implements parserTemplate
{

    private $timply;

    function __construct()
    {
        timply::setUri($GLOBALS['config']['themeDir']);
        timply::setFileName('rss2.rss');
        $this->timply = new timply();
    }

    public function parse($elements)
    {
        $this->timply->setElement('title', 'Q.uote.me RSS feed');
        $this->timply->setElement('link', $this->returnScriptUri()); // WIP
        $this->timply->setElement('description', 'Q.uote.me is a quote random generator');
        $this->timply->setElement('language', $GLOBALS['config']['lang']);
        $this->timply->setElement('docs', $GLOBALS['config']['siteDoc']);
        if (is_array($elements)) {
            foreach ($elements as $value) {
                $this->timply->setElement('itemTitle', $value->getAuthor() . ' said', 'Item');
                $this->timply->setElement('itemLink', $this->returnPermalink($value->getPermalink()), 'Item');
                $this->timply->setElement('itemDescription', $value->getText(), 'Item');
                $this->timply->setElement('itemPubDate', $this->formatDate($value->getDate()), 'Item');
                $this->timply->setElement('itemGuid', $this->returnPermalink($value->getPermalink()), 'Item');
                // we take last date for pubdate
                $dates[] = $value->getDate();
            }
            $this->timply->setElement('pubDate', $this->formatDate(max($dates))); //WIP
        }
        return $this->timply->returnHtml();
    }

    public function loadHeader($elements = '')
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Ven, 07 Oct 2011 23:32:00 GMT');
        header('Content-type: application/rss+xml');
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