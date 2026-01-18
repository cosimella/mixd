<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once "dbutil.php";
include "auth_check.php";

$idUser   = $_SESSION['userid'];
$idRecipe = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : 0;
$stars    = isset($_POST['stars']) ? (int)$_POST['stars'] : 0;

if ($idRecipe <= 0 || $stars < 1 || $stars > 5) {
    header("Location: ../recipe.php?id=$idRecipe");
    exit;
}

$pathForDb = null;
if (isset($_FILES['rating_pic']) && $_FILES['rating_pic']['error'] === 0) {
    $dirRel  = 'resources/uploads/ratings/';
    $dirPhys = '../' . $dirRel; 

    if (!is_dir($dirPhys)) {
        mkdir($dirPhys, 0777, true);
    }
    
    $ext  = pathinfo($_FILES['rating_pic']['name'], PATHINFO_EXTENSION);
    $file = "rate_" . $idRecipe . "_" . $idUser . "_" . time() . "." . $ext;
    
    if (move_uploaded_file($_FILES['rating_pic']['tmp_name'], $dirPhys . $file)) {
        $pathForDb = $dirRel . $file; 
    }
}

$sqlCheck = "SELECT userid FROM ratings WHERE userid = ? AND recipe_id = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("ii", $idUser, $idRecipe);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    if ($pathForDb) {
        $sqlUpd = "UPDATE ratings SET stars = ?, rating_image = ? WHERE userid = ? AND recipe_id = ?";
        $stmtUpd = $conn->prepare($sqlUpd);
        $stmtUpd->bind_param("isii", $stars, $pathForDb, $idUser, $idRecipe);
    } else {
        $sqlUpd = "UPDATE ratings SET stars = ? WHERE userid = ? AND recipe_id = ?";
        $stmtUpd = $conn->prepare($sqlUpd);
        $stmtUpd->bind_param("iii", $stars, $idUser, $idRecipe);
    }
    $stmtUpd->execute();
} else {
    $sqlIns = "INSERT INTO ratings (userid, recipe_id, stars, rating_image) VALUES (?, ?, ?, ?)";
    $stmtIns = $conn->prepare($sqlIns);
    $stmtIns->bind_param("iiis", $idUser, $idRecipe, $stars, $pathForDb);
    $stmtIns->execute();
}

header("Location: ../recipe.php?id=$idRecipe&msg=bewertet");
exit;
?>