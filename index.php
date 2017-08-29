<?php 

include('SPDO.php');
include('User.php');
include('Task.php');
include('api/MyApi.php');

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

$rest = new MyApi($_REQUEST['action'], $_SERVER['HTTP_ORIGIN']);
$rest->processAPI();

?>