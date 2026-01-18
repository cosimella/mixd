<?php
// Fehleranzeige zum Debuggen einschalten
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// KORREKTUR: Da die Datei bereits im Ordner 'util' liegt, 
// binden wir die Dateien direkt ein (ohne util/ davor).
include "dbutil.php";
include "auth_check.php";

$currentUserId = $_SESSION['userid'];

// WICHTIG: Prüfen, ob eine ID per GET übergeben wurde
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$targetRecipeId = (int)$_GET['id'];

// 2. STATUS-CHECK
// Achte darauf: In deinem SQL-Dump hieß die Spalte 'user_id' mit Unterstrich!
$queryCheckFavorite = "SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?";
$statementCheck = $conn->prepare($queryCheckFavorite);
$statementCheck->bind_param("ii", $currentUserId, $targetRecipeId);
$statementCheck->execute();
$favoriteLookupResult = $statementCheck->get_result();

if ($favoriteLookupResult->num_rows > 0) {
    // FALL A: Löschen
    $sqlToggleAction = "DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?";
} else {
    // FALL B: Einfügen
    $sqlToggleAction = "INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)";
}
$statementCheck->close();

// 3. AKTION AUSFÜHREN
$statementToggle = $conn->prepare($sqlToggleAction);
$statementToggle->bind_param("ii", $currentUserId, $targetRecipeId);
$statementToggle->execute();
$statementToggle->close();

// 4. WEITERLEITUNG
header("Location: ../recipe.php?id=" . $targetRecipeId);
exit;
?>