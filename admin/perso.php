<?php
if (!empty($_POST)) {
    if (!empty($_POST['username'])) $user->updateUsername($_POST['username'], $user->datas->id);
    if (!empty($_POST['password'])) $user->updatePassword($_POST['oldpassword'], $_POST['password'], $_POST['confirmPassword'], $user->datas->id);
    if (!empty($_POST['privatetoken'])) $user->updateToken('private', $user->datas->id);
    if (!empty($_POST['sharetoken'])) $user->updateToken('share', $user->datas->id);
    if (!empty($_POST['level'])) $user->updateLevel($_POST['level'], $user->datas->id);
    if (!empty($_POST['email'])) $user->updateEmail($_POST['email'], $user->datas->id);
    if (!empty($_POST['shaarli'])) $user->updateShaarli($_POST['shaarli'], $user->datas->id);
    header("Refresh:0");
}
$html = new timply('perso.html');

$html->setElement('peid', $user->datas->id);
$html->setElement('peusername', $user->datas->username);
$html->setElement('peemail', $user->datas->email);
$html->setElement('peshaarli', $user->datas->shaarli);
$html->setElement('peprivatetoken', $user->datas->private_token);
$html->setElement('pesharetoken', $user->datas->share_token);

$html->setElement('pehover', $GLOBALS['navHover']);