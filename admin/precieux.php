<?php
if ($user->datas->id !== 'Roultabie' && $user->datas->id !== 'Jean-Baptise') {
    echo '<a href="/admin/">Ah ah ah! Vous n\'avez pas le mot magique ! Ah ah ah!</a>';
    exit();
}
if (!empty($_POST)) {
    if (is_array($_POST['quotes'])) {
        foreach ($_POST['quotes'] as $value) {
            $query = 'UPDATE qm_quotes
                      SET user = :user
                      WHERE permalink = :permalink';
            $stmt = dbConnexion::getInstance()->prepare($query);
            $stmt->bindValue(':user', $user->datas->id, PDO::PARAM_STR);
            $stmt->bindValue(':permalink', $value, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}
$html = new timply('precieux.html');
$html->setElement('precieuxhover', $GLOBALS['navHover']);
$query  = 'SELECT *
           FROM qm_quotes
           WHERE user IS NULL
           LIMIT 20;';
$stmt = dbConnexion::getInstance()->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_OBJ);
$stmt->closeCursor();
$stmt = NULL;
if (is_array($result)) {
    foreach ($result as $quote) {
        $shareLink = (!empty($user->datas->share_token)) ? urlencode($quote->permalink . '&' . $user->datas->share_token) : $quote->permalink;
        $html->setElement('quoteText', SmartyPants(str_replace(PHP_EOL, '<br>', $quote->quote), 'f+:+t+h+H+'), 'quote');
        $html->setElement('quoteAuthor', SmartyPants($quote->author), 'quote');
        $html->setElement('quotePermalink', $quote->permalink, 'quote');
    }
}
