<?php
define('BASE_URL', str_replace('admin/api/api.php', '', __FILE__));

require_once BASE_URL . 'config.php';
require_once BASE_URL . 'libs/mysql.php';
require_once BASE_URL . 'admin/libs/user.php';

userQueries::initLogin();

require_once BASE_URL . 'libs/login.php';
require_once BASE_URL . 'libs/quoteme.php';
require_once BASE_URL . 'admin/libs/sqlQueries.php';

$user = new userQueries();
$userConfig = $user->config;

if (!empty($_GET)) {
    if (empty($_GET['handler'])) {
        $function = $_GET['function'];
        $type     = $_GET['type'];
        $method   = $_GET['method'];
        $request  = [$_GET['year'], $_GET['month'], $_GET['day'], $_GET['user']];
    }
    else {
        $request = explode('/', $_GET['handler']);
        if (is_array($request) && count($request) >= 3) {
            $function = array_shift($request);
            $type     = array_shift($request);
            $method   = array_shift($request);
        }
    }
    if ($function === 'stats') {
        require_once BASE_URL . 'libs/apistats.php';
        $stats = new apiStats();
        if ($type === 'delivered') {
            $result = $stats->getDelivered($request);
        }
        elseif ($type === 'posted') {
            $result = $stats->getPosted($request);
        }
    }
}
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Ven, 11 Oct 2011 23:32:00 GMT');
header('Content-type: application/json');
echo $result;
