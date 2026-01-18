<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include "util/dbutil.php";
include "util/auth_check.php";

$currentUserId = $_SESSION['userid'];
$isUploadSuccessful = false;
$errorMessage = "";

$queryCategories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$categoryOptions = [];
while($categoryRow = $queryCategories->fetch_assoc()) {
    $categoryOptions[] = $categoryRow;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipeTitle       = $_POST['recipe_name'];
    $recipeDescription = $_POST['description'];
    $rawInstructions   = $_POST['steps'];
    $selectedCategories = $_POST['categories'] ?? [];

    $insertRecipeQuery = "INSERT INTO recipes (recipe_name, beschreibung, anleitung, created_by) VALUES (?, ?, ?, ?)";
    $statementRecipe = $conn->prepare($insertRecipeQuery);
    $statementRecipe->bind_param("sssi", $recipeTitle, $recipeDescription, $rawInstructions, $currentUserId);
    
    if ($statementRecipe->execute()) {
        $newRecipeId = $conn->insert_id;
        $submittedIngredientNames = $_POST['ing_name'] ?? [];
        $submittedIngredientAmounts = $_POST['ing_amount'] ?? [];
        $submittedIngredientUnits = $_POST['ing_unit'] ?? [];

        $stmtInsertIng = $conn->prepare("INSERT IGNORE INTO ingredients (ingredient_name) VALUES (?)");
        $stmtCheckIng = $conn->prepare("SELECT ingredient_id FROM ingredients WHERE ingredient_name = ?");
        $stmtLinkIng = $conn->prepare("INSERT IGNORE INTO recipe_ingredients (recipe_id, ingredient_id, amount, unit) VALUES (?, ?, ?, ?)");

        foreach ($submittedIngredientNames as $index => $ingredientName) {
            if (!empty(trim($ingredientName))) {
                $stmtInsertIng->bind_param("s", $ingredientName);
                $stmtInsertIng->execute();
                $stmtCheckIng->bind_param("s", $ingredientName);
                $stmtCheckIng->execute();
                $ingredientId = $stmtCheckIng->get_result()->fetch_assoc()['ingredient_id'];

                $amountValue = floatval($submittedIngredientAmounts[$index]);
                $unitValue = $submittedIngredientUnits[$index];
                $stmtLinkIng->bind_param("iids", $newRecipeId, $ingredientId, $amountValue, $unitValue);
                $stmtLinkIng->execute();
            }
        }

        $instructionSteps = explode("\n", $rawInstructions);
        $stmtStep = $conn->prepare("INSERT INTO recipe_steps (recipe_id, step_number, instruction) VALUES (?, ?, ?)");
        foreach ($instructionSteps as $index => $stepText) {
            $stepText = trim($stepText);
            if (!empty($stepText)) {
                $stepNumber = $index + 1;
                $stmtStep->bind_param("iis", $newRecipeId, $stepNumber, $stepText);
                $stmtStep->execute();
            }
        }

        foreach ($selectedCategories as $categoryId) {
            $stmtCat = $conn->prepare("INSERT INTO recipe_categories (recipe_id, category_id) VALUES (?, ?)");
            $stmtCat->bind_param("ii", $newRecipeId, $categoryId);
            $stmtCat->execute();
        }

        if (isset($_FILES['image']) && !empty($_FILES['image']['name'][0])) {
            $uploadPath = 'resources/uploads/recipes/';
            if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }
            foreach ($_FILES['image']['tmp_name'] as $index => $temporaryLocation) {
                if ($_FILES['image']['error'][$index] === 0) {
                    $ext = pathinfo($_FILES['image']['name'][$index], PATHINFO_EXTENSION);
                    $newName = "recipe_" . $newRecipeId . "_" . time() . "_" . $index . "." . $ext;
                    if (move_uploaded_file($temporaryLocation, $uploadPath . $newName)) {
                        $conn->query("INSERT INTO recipe_images (recipe_id, image_path) VALUES ($newRecipeId, '$uploadPath$newName')");
                    }
                }
            }
        }
        $isUploadSuccessful = true;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Neues Rezept - MIXD</title>
</head>
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if ($isUploadSuccessful): ?>
                    <div class="card shadow-sm border-0 rounded-4 p-5 text-center">
                        <i class="bi bi-stars text-warning display-1"></i>
                        <h2 class="fw-bold mt-3">Abgeschickt!</h2>
                        <p class="text-muted">Dein Rezept ist nun online.</p>
                        <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-3">Zur Startseite</a>
                        <script>localStorage.removeItem('recipeDraft');</script>
                    </div>
                <?php else: ?>
                <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                    <h2 class="fw-bold mb-4">Neuer Drink</h2>
                    <form id="recipeForm" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="small fw-bold text-muted">NAME DES DRINKS</label>
                            <input type="text" name="recipe_name" id="recipe_name" class="form-control border-0 bg-light py-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold text-muted">STORY / BESCHREIBUNG</label>
                            <textarea name="description" id="description" class="form-control border-0 bg-light" rows="2"></textarea>
                        </div>
                        <div class="mb-4 p-3 bg-white border rounded-4">
                            <label class="small fw-bold text-muted mb-3 d-block">ZUTATEN</label>
                            <div id="ingredient-input-container"></div>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none" onclick="addIngredientRow()">+ Weitere Zutat</button>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold text-muted">ZUBEREITUNG</label>
                            <textarea name="steps" id="steps" class="form-control border-0 bg-light" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">VERÖFFENTLICHEN</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php include "includes/footer.php"; ?>
</body>
</html>