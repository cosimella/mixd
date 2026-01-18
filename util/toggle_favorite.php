<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once "dbutil.php";
include "auth_check.php";

$idUser   = $_SESSION['userid'];
$idRecipe = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idRecipe <= 0) {
    header("Location: ../index.php");
    exit;
}

$sqlCheck = "SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("ii", $idUser, $idRecipe);
$stmt->execute();
$res = $stmt->get_result();


if ($res->num_rows > 0) {
    $sqlAction = "DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?";
} else {
    $sqlAction = "INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)";
}
$stmt->close();

$stmtToggle = $conn->prepare($sqlAction);
$stmtToggle->bind_param("ii", $idUser, $idRecipe);
$stmtToggle->execute();
$stmtToggle->close();

header("Location: ../recipe.php?id=" . $idRecipe);
exit;
?>