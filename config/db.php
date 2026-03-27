<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "cci_ims";

$conn = mysqli_connect($host, $user, $pass, $db);
if(!$conn) {
    die("Db connection failed: " . mysqli_connect_error());
}