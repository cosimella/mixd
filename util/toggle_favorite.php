<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();


include "dbutil.php";
include "auth_check.php";

$currentUserId = $_SESSION['userid'];


if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$targetRecipeId = (int)$_GET['id'];

$queryCheckFavorite = "SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?";
$statementCheck = $conn->prepare($queryCheckFavorite);
$statementCheck->bind_param("ii", $currentUserId, $targetRecipeId);
$statementCheck->execute();
$favoriteLookupResult = $statementCheck->get_result();

if ($favoriteLookupResult->num_rows > 0) {
   
    $sqlToggleAction = "DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?";
} else {
    
    $sqlToggleAction = "INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)";
}
$statementCheck->close();

$statementToggle = $conn->prepare($sqlToggleAction);
$statementToggle->bind_param("ii", $currentUserId, $targetRecipeId);
$statementToggle->execute();
$statementToggle->close();

header("Location: ../recipe.php?id=" . $targetRecipeId);
exit;
?>