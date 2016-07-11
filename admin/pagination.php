<?php
function pagination($currentPage, $nbElements, $baseLink)
{
    $html = new timply('pagination.html');
    $perpage = $GLOBALS['config']['perpage'];
    $baseLink = preg_replace('/&page=\d+/', '', $baseLink);
    $nbPages = ceil($nbElements / $perpage);
    $maxElements = 13;
    $calc = 3;
    $min = $currentPage - $calc;
    $max = $currentPage + $calc + 1;
    if ($min < 2) {
        $min = 2;
        $max = $maxElements;
    }
    if ($min >= 2) {
        $max = $min + ($maxElements - 2);
    }
    if ($max > $nbPages) {
        $max = $nbPages;
        $min = $max - ($maxElements - 2);
    }
    //if ($min + $maxElements - 2 < $maxElements) $max = $maxElements;
    if ($min <= 2) $min = 2;
    $pageLink = "&page=";
    if ($nbPages > 1) {
        if ($currentPage == 1) $selected = 'button-primary';
        $html->setElement('pageLink', $baseLink . $pageLink . '1', 'Pagination');
        $html->setElement('selected', $selected, 'Pagination');
        $html->setElement('number', '1', 'Pagination');
        unset($selected);
        for ($i = $min; $i < $max ; $i++) {
            if ($currentPage == $i) $selected = 'button-primary';
            $html->setElement('pageLink', $baseLink . $pageLink . $i, 'Pagination');
            $html->setElement('number', $i, 'Pagination');
            $html->setElement('selected', $selected, 'Pagination');
            unset($selected);
        }
        if ($currentPage == $nbPages) $selected = 'button-primary';
        $html->setElement('pageLink', $baseLink . $pageLink . $nbPages, 'Pagination');
        $html->setElement('selected', $selected, 'Pagination');
        $html->setElement('number', $nbPages, 'Pagination');
    }
    return $html->returnHtml();
}
