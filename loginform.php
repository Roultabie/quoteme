<?php
/**
 * Loading libs
 */
require_once 'libs/timply.php';

timply::setUri($GLOBALS['config']['themeDir']);
timply::setFileName('loginform.html');
timply::addDictionary($GLOBALS['config']['langDir'] . 'en_EN.php');

$html = new timply();
$html->setElement('formaction', $GLOBALS['loginAction']);
echo $html->returnHtml();
?>