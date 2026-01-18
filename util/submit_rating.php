<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Da diese Datei im Ordner 'util' liegt, binden wir die anderen util-Dateien direkt ein
include "dbutil.php";
include "auth_check.php";

$currentUserId = $_SESSION['userid'];
$targetRecipeId = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : 0;
$ratingStars    = isset($_POST['stars']) ? (int)$_POST['stars'] : 0;

// 1. VALIDIERUNG
if ($targetRecipeId <= 0 || $ratingStars < 1 || $ratingStars > 5) {
    header("Location: ../recipe.php?id=$targetRecipeId");
    exit;
}

// 2. BILD-UPLOAD (Optional)
$uploadedImagePath = null;
if (isset($_FILES['rating_pic']) && $_FILES['rating_pic']['error'] == 0) {
    $uploadDirRel = 'resources/uploads/ratings/';
    $uploadDirPhys = '../' . $uploadDirRel; // Pfad vom util-Ordner aus gesehen

    // Sicherstellen, dass der Ordner existiert
    if (!is_dir($uploadDirPhys)) {
        mkdir($uploadDirPhys, 0777, true);
    }
    
    // Sicherer Dateiname: Zeitstempel + Namen säubern
    $fileExtension = pathinfo($_FILES['rating_pic']['name'], PATHINFO_EXTENSION);
    $uniqueFileName = "rate_" . $targetRecipeId . "_" . $currentUserId . "_" . time() . "." . $fileExtension;
    $finalDestination = $uploadDirPhys . $uniqueFileName;

    if (move_uploaded_file($_FILES['rating_pic']['tmp_name'], $finalDestination)) {
        // In die DB speichern wir den Pfad relativ zum Hauptverzeichnis
        $uploadedImagePath = $uploadDirRel . $uniqueFileName;
    }
}

// 3. DATENBANK-LOGIK
// Wir prüfen anhand der 'userid', ob der User den Drink schon bewertet hat
$queryCheckExisting = $conn->prepare("SELECT userid FROM ratings WHERE userid = ? AND recipe_id = ?");
$queryCheckExisting->bind_param("ii", $currentUserId, $targetRecipeId);
$queryCheckExisting->execute();
$existingRatingResult = $queryCheckExisting->get_result();
$existingRating = $existingRatingResult->fetch_assoc();

if ($existingRating) {
    // FALL A: UPDATE (Bestehende Bewertung anpassen)
    if ($uploadedImagePath) {
        $statementUpdate = $conn->prepare("UPDATE ratings SET stars = ?, rating_image = ? WHERE userid = ? AND recipe_id = ?");
        $statementUpdate->bind_param("isii", $ratingStars, $uploadedImagePath, $currentUserId, $targetRecipeId);
    } else {
        $statementUpdate = $conn->prepare("UPDATE ratings SET stars = ? WHERE userid = ? AND recipe_id = ?");
        $statementUpdate->bind_param("iii", $ratingStars, $currentUserId, $targetRecipeId);
    }
    $statementUpdate->execute();
} else {
    // FALL B: INSERT (Neue Bewertung erstellen)
    $statementInsert = $conn->prepare("INSERT INTO ratings (userid, recipe_id, stars, rating_image) VALUES (?, ?, ?, ?)");
    $statementInsert->bind_param("iiis", $currentUserId, $targetRecipeId, $ratingStars, $uploadedImagePath);
    $statementInsert->execute();
}

// Zurück zur Rezeptseite
header("Location: ../recipe.php?id=$targetRecipeId&msg=bewertet");
exit;