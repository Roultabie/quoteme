<?php
/**
 * Loading configuration
 */
require_once 'config.php';

/**
 * Loading libs
 */
require_once 'libs/login.php';
require_once 'libs/mysql.php';
require_once 'libs/quoteme.php';
require_once 'libs/timply.php';
require_once 'libs/smartypants.php';

/*function getLink($action, $options)
{
    $url  = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '/?';
    $url .= 'action=' . $action;
    if (is_array($options)) {
        reset($options);
        while (list($key, $val) = each($options)) {
            $url .= '&' . $key . '=' . $val;
        }
     }
    return $url;
}*/

$quote = new quoteQueries();

if (!empty($_POST)) {
    if ($_POST['action'] === "edit") {
        $edit = $quote->editQuote($_POST['permalink'], $_POST['text'], $_POST['author'], $_POST['source'], $_POST['tags']);
    }
    else {
        $add = $quote->addQuote($_POST['text'], $_POST['author'], $_POST['source'], $_POST['tags']);
    }
}

if ($_GET['action'] === "edit") {
    $editQuote     = $quote->getQuote(array('where' => 'permalink', 'whereOpt' => 'equal,' . $_GET['permalink']));
    $formPermalink = $editQuote[0]->getPermalink();
    $formText      = $editQuote[0]->getText();
    $formAuthor    = $editQuote[0]->getAuthor();
    $formSource    = $editQuote[0]->getSource();
    $formTags      = $editQuote[0]->getTags();
    $formAction    = 'edit';
}
if ($_GET['action'] === "delete" && !empty($_GET['permalink'])) {
    $del = $quote->delQuote($_GET['permalink']);
}

$html = new timply('admin.html');

/* Form */
$html->setElement('formPermalink', $formPermalink);
$html->setElement('formInputText', $formText);
$html->setElement('formInputAuthor', $formAuthor);
$html->setElement('formInputSource', $formSource);
$html->setElement('formInputTags', $formTags);
$html->setElement('formAction', $formAction);

/* Quotes list */
$quotes = new quoteQueries();
$quotes = $quotes->getQuote(array('sort' => 'id,desc'));
if (is_array($quotes)) {
    foreach ($quotes as $quote) {
        $html->setElement('quoteTableText', SmartyPants($quote->getText(), 'f+:+t+h+H+'), 'quoteTable');
        $html->setElement('quoteTableAuthor', SmartyPants($quote->getAuthor()), 'quoteTable');
        $html->setElement('quoteTableSource', SmartyPants($quote->getSource()), 'quoteTable');
        $html->setElement('quoteTableTags', SmartyPants($quote->getTags()), 'quoteTable');
        $html->setElement('quoteTableDate', SmartyPants($quote->getDate()), 'quoteTable');
        $html->setElement('edit', '?' . http_build_query(array('action' => 'edit', 'permalink' => $quote->getPermalink()), '', '&'));
        $html->setElement('delete', '?' . http_build_query(array('action' => 'delete', 'permalink' => $quote->getPermalink()), '', '&'));
        //$html->setElement('edit', getLink('edit', array('permalink' => $quote->getPermalink())), 'quoteTable');
        //$html->setElement('delete', getLink('delete', array('permalink' => $quote->getPermalink())), 'quoteTable');
    }
}
//quoteQueries::execStack();
echo $html->returnHtml();
?>
