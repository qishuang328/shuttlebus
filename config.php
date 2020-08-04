<?php
ini_set('display_errors', 1);

session_start();
// use only https
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

// check if cookies are enabled

function checkCookies() {
  setcookie('test', 1);
  if(!isset($_GET['cookies'])){
    if (sizeof($_GET)) {
      header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&cookies', TRUE, 301);
    } else {
      header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?cookies', TRUE, 301);
    }
    die();
  }
  if(count($_COOKIE) > 0){
  	
  } else {
    die('<h1 style="color:#fff;background-color:#f44336">you must enable cookies to view this site</h1>');
  }

}

if (!isset($_COOKIE['test'])) {
  checkCookies();
}
	

// database

//local

$host = 'localhost';
$user = 'root';
$pwd = '';
$db = 'shuttlebooking';

/*
$host = 'localhost';
$user = 's233329';
$pwd = 'nglecroq';
$db = 's233329';*/


$title = 'Booking appointments';

?>
