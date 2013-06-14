<?php
abstract class parser
{
    function __construct()
    {
        
    }

    final protected function isUTF8($text)
    {
        #
    }

    final protected function toUTF8($text)
    {
        #
    }

    final protected function returnUri()
    {
        return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    }

    final protected function returnScriptUri()
    {
        return $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    }

    final protected function returnPermalink($permalink)
    {
        return $_SERVER['HTTP_HOST'] . '/?' . $permalink;
    }
}
?>