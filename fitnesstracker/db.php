<?php
$host = "localhost";
$user = "d04212b7";
$pass = "Artus.2008";
$dbname = "d04212b7";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}