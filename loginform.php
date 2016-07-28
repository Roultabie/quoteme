<?php
header('Content-Type: text/html; charset=utf-8');
/**
 * Loading configuration
 */
require_once 'config.php';

/**
 * Loading libs
 */
require_once 'libs/timply.php';

timply::setUri('../admin/themes/default/');
timply::addDictionary($GLOBALS['config']['langDir'] . $GLOBALS['config']['lang'] . '.php');

$html = new timply('login.html');
$html->setElement('loginAction', $GLOBALS['loginAction']);
$html->setElement('statusClass', $statusClass);
$html->setElement('statusMessage', $statusMessage);

echo $html->returnHtml();