<?php
$host     = "localhost";
$user     = "root";
$password = "";
$database = "cocktail_website";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>