<?php
interface parserTemplate
{
    public function parse($elements);

    public function loadHeader($elements = '');
}
?>