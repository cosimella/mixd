<?php
// logout.php
session_start(); // Zugriff auf die aktuelle Sitzung erhalten

// 1. Alle gespeicherten Daten (z.B. userid, benutzername) im aktuellen Skript-Speicher leeren
session_unset();

// 2. Die Sitzungs-Datei auf dem Server physisch löschen
session_destroy();

// 3. Den Browser anweisen, sofort zur Startseite zurückzukehren
header("Location: index.php");

// 4. Sicherstellen, dass nach der Umleitung kein weiterer Code ausgeführt wird
exit();
?>