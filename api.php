<?php
// TODO: Sanitizer for $_GET, multi quotes return tag search
require_once 'libs/mysql.php';
require_once 'libs/quoteme.php';
require_once 'libs/timply.php';
require_once 'parser/parser.php';
require_once 'parser/template.php';


define('DB_HOST', 'localhost');
define('DB_NAME', 'quoteme');
define('DB_USR', 'dbuser');
define('DB_PWD', 'pass');
define('TIMPLY_DIR', 'themes/simple/');

$wParser = 'parser/' . $_GET['p'] . '.php';
$options = $_GET['o'];
$GLOBALS['class'] = $_GET['p'] . 'Parser';


function parseQuote($wParser)
{
    if (file_exists($wParser)) {
        require_once $wParser;
        if (class_exists($GLOBALS['class'])) {
            $parser = new $GLOBALS['class'];
            if ($parser instanceof parserTemplate) {
                $quote  = new quoteQueries();
                if (is_object($quote)) {
                    if (empty($options)) {
                        $quote = $quote->getQuote();
                    }
                    $result = $parser->parse($quote);
                }
                else {
                    $result = 'ERROR: No data found !';
                }
                return $result;
            }
            else {
                return 'ERROR: class ' . $GLOBALS['class'] . ' not implement parserTemplate !';
            }
        }
        else {
            return 'ERROR: class ' . $GLOBALS['class'] . ' not exist !';
        }
    }
    else {
        return 'ERROR: ' . $wParser . ' not found !';
    }
}

echo parseQuote($wParser);
?>