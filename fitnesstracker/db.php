<?php
$host = 
$user = 
$pass = 
$dbname = 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
