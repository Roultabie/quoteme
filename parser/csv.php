<?php

class csvParser
extends parser
implements parserTemplate
{

    private $left;
    private $right;
    private $coma;

    function __construct()
    {
       $this->left  = '"';
       $this->right = $this->left;
       $this->coma  = ',';
    }

    public function parse($elements)
    {
        $col1   = $this->enclose('text');
        $col2   = $this->enclose('author');
        $col3   = rtrim($this->enclose('source'), ',');
        $result = $col1 . $col2 . $col3 . PHP_EOL;
        if (is_array($elements)) {
            foreach ($elements as $value) {
                $text   = str_replace('"', '""', $value->getText());
                $text   = $this->enclose($text);
                $author = str_replace('"', '""', $value->getAuthor());
                $author = $this->enclose($author);
                $source = str_replace('"', '""', $value->getSource());
                $source = $this->enclose($source);
                $source = rtrim($source, ',');
                $result .= $text . $author . $source . PHP_EOL;
            }
        }
        return trim($result);
    }

    private function enclose($text)
    {
        return $this->left . $text . $this->right . $this->coma;
    }
}
?>