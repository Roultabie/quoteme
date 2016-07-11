<?php
if (!empty($_POST)) {
     $query = 'UPDATE ' . $GLOBALS['config']['tblPrefix'] . 'users 
               SET username = :username, email = :email,
               shaarli = :shaarli WHERE id = :id';
    $stmt = dbConnexion::getInstance()->prepare($query);
    $stmt->bindValue(':username', trim($_POST['username']), PDO::PARAM_STR);
    $stmt->bindValue(':email', trim($_POST['email']), PDO::PARAM_STR);
    $stmt->bindValue(':shaarli', trim($_POST['shaarli']), PDO::PARAM_STR);
    $stmt->bindValue(':id', $userConfig['id'], PDO::PARAM_STR);
    $stmt->execute();
    $stmt = NULL;

    $query = 'SELECT id, hash, username, email, shaarli, type
              FROM ' . $GLOBALS['config']['tblPrefix'] .'users
              WHERE id = :id';
    $stmt  = dbConnexion::getInstance()->prepare($query);
    $stmt->bindValue(':id', $userConfig['id'], PDO::PARAM_STR);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = NULL;
}
$html = new timply('perso.html');

$html->setElement('peusername', $userConfig['username']);
$html->setElement('peemail', $userConfig['email']);
$html->setElement('peshaarli', $userConfig['shaarli']);

$html->setElement('pehover', $GLOBALS['navHover']);