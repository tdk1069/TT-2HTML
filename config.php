<?php

$hostname = "127.0.0.1";
$username = "dkearns";
$password = "dajediiz";

$database = "dkearns_equip";

// Connect to MySQL:
//$formdb = mysql_pconnect($hostname, $username, $password) or die(mysql_error());

$db = new mysqli($hostname, $username, $password,$database);
if ($db->connect_error) {
    die('Connect Error (' . $db->connect_errno . ') '
            . $db->connect_error);
}

if (mysqli_connect_error()) {
    die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}
