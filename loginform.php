<?php
/**
 * Loading libs
 */
require_once 'libs/timply.php';
$html = new timply('loginform.html');
$html->setElement('pagetitle', 'Administration - Q.uote.me');
$html->setElement('formaction', $GLOBALS['loginAction']);
echo $html->returnHtml();
?>