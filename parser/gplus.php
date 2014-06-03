<?php
class gplusParser
extends parser
implements parserTemplate
{

    private $timply;

    function __construct()
    {
        timply::setUri($GLOBALS['config']['themeDir']);
        timply::setFileName('gplus.html');
        $this->timply = new timply();
        $this->width  = 1024;
    }

    public function parse($elements)
    {
        $this->timply->setElement('imglink', '//' .$this->returnScriptUri() . '?p=img&wi=' . $this->width . '&w=permalink&wo=equal,' . $elements[0]->getPermalink());
        $this->timply->setElement('imgwidth', $this->width);
        $this->timply->setElement('text', $elements[0]->getText());
        $this->timply->setElement('author', $elements[0]->getAuthor());
        $this->timply->setElement('source', $elements[0]->getSource());
        $this->timply->setElement('permalink', '//' .$this->returnPermalink($elements[0]->getPermalink()));
        return $this->timply->returnHtml();
    }

    public static function loadHeader()
    {
        header('Content-Type: text/html; charset=utf-8');
    }
}
?>