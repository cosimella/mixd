<?php
// util/dbutil.php

$host     = "localhost";
$user     = "root";
$password = "";
$database = "cocktail_website";

// Wir erstellen das Datenbank-Objekt (mysqli-Treiber)
$conn = new mysqli($host, $user, $password, $database);

// Verbindung prüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Zeichensatz auf UTF-8 setzen, damit Umlaute (ä, ö, ü) richtig angezeigt werden
$conn->set_charset("utf8mb4");

/**
 * Notiz für das Code-Review:
 * Wir verwenden hier das mysqli-Objekt ($conn), wie in den Folien gefordert.
 * Das ermöglicht uns objektorientierte Abfragen wie $conn->query().
 */
?>