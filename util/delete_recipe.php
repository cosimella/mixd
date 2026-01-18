<?php
session_start();

// Da wir im Ordner 'util' sind, binden wir die Dateien direkt ein
include "dbutil.php";
include "auth_check.php";

// 1. ID aus der URL holen und sicherstellen, dass es eine Zahl ist
$recipeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$currentUserId = $_SESSION['userid'];

if ($recipeId > 0) {
    
    // 2. SICHERHEIT: Wir prüfen, ob das Rezept wirklich diesem User gehört
    $checkStmt = $conn->prepare("SELECT recipe_id FROM recipes WHERE recipe_id = ? AND created_by = ?");
    $checkStmt->bind_param("ii", $recipeId, $currentUserId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // 3. DATEN LÖSCHEN
        // Wir müssen die "Kinder" zuerst löschen (Zutaten, Bilder, Schritte), 
        // bevor wir das "Eltern"-Rezept löschen können (Fremdschlüssel-Logik).
        
        $conn->query("DELETE FROM recipe_ingredients WHERE recipe_id = $recipeId");
        $conn->query("DELETE FROM recipe_steps WHERE recipe_id = $recipeId");
        $conn->query("DELETE FROM recipe_categories WHERE recipe_id = $recipeId");
        $conn->query("DELETE FROM favorites WHERE recipe_id = $recipeId");
        $conn->query("DELETE FROM ratings WHERE recipe_id = $recipeId");
        
        // Bilder-Einträge löschen
        $conn->query("DELETE FROM recipe_images WHERE recipe_id = $recipeId");

        // 4. DAS REZEPT SELBST LÖSCHEN
        $deleteStmt = $conn->prepare("DELETE FROM recipes WHERE recipe_id = ? AND created_by = ?");
        $deleteStmt->bind_param("ii", $recipeId, $currentUserId);
        $deleteStmt->execute();

        // 5. ERFOLG: Zurück zur Übersicht (eine Ebene hoch mit ../)
        header("Location: ../my_recipes.php?msg=deleted");
        exit;
    } else {
        // Falls jemand versucht, ein fremdes Rezept über die URL zu löschen
        die("Du hast keine Berechtigung, dieses Rezept zu löschen.");
    }
}

// Falls keine ID gefunden wurde
header("Location: ../my_recipes.php");
exit;
?>