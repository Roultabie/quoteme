<?php
/**
 * Loading configuration
 */
require_once 'config.php';

/**
 * Loading libs
 */
require_once 'libs/mysql.php';
require_once 'libs/quoteme.php';
require_once 'libs/timply.php';

/**
 * Loading parser class & interface
 */
require_once 'parser/parser.php';
require_once 'parser/template.php';

timply::setUri($GLOBALS['config']['themeDir']);
parser::$cacheState = $GLOBALS['config']['cacheState'];
parser::$cacheDir   = $GLOBALS['config']['cacheDir'];

function parseQuote($parser = '', $options = '')
{
    if ($parser !== 'php') {
        $parserUri = 'parser/' . $parser . '.php';
        if (file_exists($parserUri)) {
            require_once $parserUri;
            $class = $parser . 'Parser';
            if (class_exists($class)) {
                $quote = new quoteQueries();
                if (is_object($quote)) {
                    $quotes  = $quote->getQuote($options);
                    if (is_array($quotes)) {
                        $parser = new $class;
                        if ($parser instanceof parserTemplate) {
                            $parser->loadHeader($quotes);
                            $parser::startCache();
                            $result = $parser->parse($quotes);
                            return $result;
                        }
                        else {
                            return 'ERROR: class ' . $class . ' does not implement parserTemplate!';
                        }
                    }
                    else {
                        return 'ERROR: no data found!';
                    }
                }
                
            }
            else {
                return 'ERROR: class ' . $class . ' not found!';
            }
        }
        else {
            return 'ERROR: ' . $parserUri . ' not found!';
        }
    }
    else {
        $quote = new quoteQueries();
        return array('obj' => $quote->getQuote($options), 'nb' => quoteQueries::$nbQuotes);
    }
}

function testGet($var, $pattern)
{
    $regexp = array('options' => array('regexp' => $pattern));
    if (filter_var($var, FILTER_VALIDATE_REGEXP, $regexp) !== FALSE) {
        return $var;
    }
    else {
        return FALSE;
    }
}

// p=json&s=random&l=1,10&w=tag&ow=like,toto&a=id&oa=minus,10
$parser = testGet($_GET['p'], '/^[\w\d_-]{2,5}$/');

if (($get = testGet($_GET['s'], '/^[\w\d_-]+,asc$|^[\w\d_-]+,desc$|^random$/'))) $opt['sort'] = $get;
if (($get = testGet($_GET['l'], '/^\d+,{0,}\d{0,}$/'))) $opt['limit'] = $get;
if (($get = testGet($_GET['w'], '/^[\w\d_-]+$/'))) $opt['where'] = $get ;
if (($get = testGet($_GET['wo'], '/^[\w\d_-]+,{0,}[\.\s\w\d_-]{0,}$/'))) $opt['whereOpt'] = $get;
if (($get = testGet($_GET['a'], '/^[\w\d_-]+$/'))) $opt['and'] = $get;
if (($get = testGet($_GET['ao'], '/^[\w\d_-]+,{0,}[\.\s\w\d_-]{0,}$/'))) $opt['andOpt'] = $get;

$currentScript = str_replace('/', '', $_SERVER['SCRIPT_NAME']);

if ($currentScript !== 'api.php') {
    $parser              = 'php';
    $GLOBALS['quoteObj'] = parseQuote($parser, $opt); // permet de générer une quote en php, retour : la quote et le nb de quotes. Pour utiliser les options, charger $_GET
}
else {
    if (!empty($_GET['p'])) {
        if ($parser !== FALSE) {
            echo parseQuote($parser, $opt);
        }
        else {
            echo 'ERROR: parser is not valid!';
        }
    }
    else {
        $html = new timply('api.html');
        echo $html->returnHtml();
    }
}
if (is_array($opt)) {
    if ($opt['sort'] !== 'random' && $parser !== 'php') {
        parser::endCache();
    }
}
?>
