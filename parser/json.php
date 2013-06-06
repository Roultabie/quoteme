<?php

class jsonParser
extends parser
implements parserTemplate
{

    //private $object;

    function __construct()
    {
       // $this->object = $object;
    }

    public function parse($elements)
    {
        $i = 0;
        foreach ($elements as $value) {
            $quote[$i]['text']   = $value->getText();
            $quote[$i]['author'] = $value->getAuthor();
            $quote[$i]['source'] = $value->getSource();
            $i++;
        }
        $result = json_encode($quote);
        return $result;
    }
}
?>