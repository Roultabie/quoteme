<?php

class rss2Parser
extends parser
implements parserTemplate
{

    private $timply;

    function __construct()
    {
        $this->timply = new timply('rss2.rss');
    }

    public function parse($elements)
    {
        $this->timply->setElement('title', 'Q.uote.me RSS feed');
        $this->timply->setElement('description', 'Q.uote.me is a quote random generator');
        if (is_array($elements)) {
            foreach ($elements as $value) {
                $this->timply->setElement('itemTitle', $value->getAuthor() . ' said', 'Item');
                $this->timply->setElement('itemLink', '', 'Item');
                $this->timply->setElement('itemDescription', $value->getText(), 'Item');
                $this->timply->setElement('nbQuotes', $nbQuotes);
            }
        }
        return $this->timply->returnHtml();
    }
}
?>