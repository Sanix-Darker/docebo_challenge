<?php

ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$hostname = "localhost";
$dbname = "docebo_challenge";
$user = "root";
$password = "root";

// Using the PDO, we connect to the Database:
$db = new PDO('mysql:host='.$hostname.';dbname='.$dbname, $user, $password);
