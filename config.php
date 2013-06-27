<?php
/**
 * System options don't modify them
 */
$system['dateFormat'] = 'Y-m-d';
$system['version'] = '1.1b';
$system['lastUpdate'] = '0000-00-00';

/**
 * config options, put your informations here
 */
$config['dbHost'] = 'localhost';
$config['dbName'] = 'quoteme';
$config['dbUser'] = 'dbuser';
$config['dbPass'] = 'pass';
$config['lang'] = 'fr_FR';
$config['themeDir'] = 'themes/simple/';
$config['siteDoc'] = 'http://q.uote.me/api.php';
$config['siteContact'] = 'contact@aelys-info.fr';
$config['user'] = 'dd';
$config['password'] = 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1';
$config['sessionExpire'] = 1800;
$config['appVers'] = $system['version']; // your app version
//$config['appVers'] = 'a5e'; // (anonyme) Anonyme stat of client when check update
//$config['appVers'] = ''; // does not generate stats of client when check update
?>
