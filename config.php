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
$config['langDir'] = 'lang/';
$config['siteDoc'] = 'http://q.uote.me/api.php';
$config['siteContact'] = 'contact@aelys-info.fr';
$config['user'] = 'demo';
$config['password'] = '2a97516c354b68848cdbd8f54a226a0a55b21ed138e207ad6c5cbb9c00aa5aea';
$config['sessionExpire'] = 1800;
$config['appVers'] = $system['version']; // your app version
//$config['appVers'] = 'a5e'; // (anonyme) Anonyme stat of client when check update
//$config['appVers'] = ''; // does not generate stats of client when check update
?>
