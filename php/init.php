<?php
#starting the users session
session_start();
//require 'connect/database.php';
require 'users.php';
require 'general.php';
 
$users = new Users();
$general = new General();
 
$errors = array();
?>
