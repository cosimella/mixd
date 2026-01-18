<?php
session_start();

include "dbutil.php";
include "auth_check.php";

$recipeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$currentUserId = $_SESSION['userid'];

if ($recipeId > 0) {
    
    $checkStmt = $conn->prepare("SELECT recipe_id FROM recipes WHERE recipe_id = ? AND created_by = ?");
    $checkStmt->bind_param("ii", $recipeId, $currentUserId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        
        $conn->query("DELETE FROM recipe_ingredients WHERE recipe_id = $recipeId");
        $conn->query("DELETE FROM recipe_steps WHERE recipe_id = $recipeId");
        $conn->query("DELETE FROM recipe_categories WHERE recipe_id = $recipeId");
        $conn->query("DELETE FROM favorites WHERE recipe_id = $recipeId");
        $conn->query("DELETE FROM ratings WHERE recipe_id = $recipeId");
        $conn->query("DELETE FROM recipe_images WHERE recipe_id = $recipeId");

        $deleteStmt = $conn->prepare("DELETE FROM recipes WHERE recipe_id = ? AND created_by = ?");
        $deleteStmt->bind_param("ii", $recipeId, $currentUserId);
        $deleteStmt->execute();

        header("Location: ../my_recipes.php?msg=deleted");
        exit;
    } else {
        
        die("Du hast keine Berechtigung, dieses Rezept zu löschen.");
    }
}

header("Location: ../my_recipes.php");
exit;
?>