<?php
session_start();

require_once "dbutil.php";
include "auth_check.php";

$idRecipe = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$idUser   = $_SESSION['userid'];

if ($idRecipe > 0) {
    
    $sqlCheck = "SELECT recipe_id FROM recipes WHERE recipe_id = ? AND created_by = ?";
    $stmt = $conn->prepare($sqlCheck);
    $stmt->bind_param("ii", $idRecipe, $idUser);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        
        $conn->query("DELETE FROM recipe_ingredients WHERE recipe_id = $idRecipe");
        $conn->query("DELETE FROM recipe_steps WHERE recipe_id = $idRecipe");
        $conn->query("DELETE FROM recipe_categories WHERE recipe_id = $idRecipe");
        $conn->query("DELETE FROM favorites WHERE recipe_id = $idRecipe");
        $conn->query("DELETE FROM ratings WHERE recipe_id = $idRecipe");
        $conn->query("DELETE FROM recipe_images WHERE recipe_id = $idRecipe");

        $sqlDel = "DELETE FROM recipes WHERE recipe_id = ? AND created_by = ?";
        $stmtDel = $conn->prepare($sqlDel);
        $stmtDel->bind_param("ii", $idRecipe, $idUser);
        $stmtDel->execute();

        header("Location: ../my_recipes.php?msg=deleted");
        exit;
        
    } else {
        die("Unbefugter Zugriff oder Rezept nicht gefunden.");
    }
}

header("Location: ../my_recipes.php");
exit;
?>