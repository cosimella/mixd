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

// 1. Kategorien für das Formular laden
$queryCategories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$categoryOptions = [];
while($categoryRow = $queryCategories->fetch_assoc()) {
    $categoryOptions[] = $categoryRow;
}

// 2. SPEICHER-LOGIK
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipeTitle       = $_POST['recipe_name'];
    $recipeDescription = $_POST['description'];
    $rawInstructions   = $_POST['steps'];
    $selectedCategories = $_POST['categories'] ?? [];

    // A. HAUPTREZEPT SPEICHERN
    $insertRecipeQuery = "INSERT INTO recipes (recipe_name, beschreibung, anleitung, created_by) VALUES (?, ?, ?, ?)";
    $statementRecipe = $conn->prepare($insertRecipeQuery);
    $statementRecipe->bind_param("sssi", $recipeTitle, $recipeDescription, $rawInstructions, $currentUserId);
    
    if ($statementRecipe->execute()) {
        $newRecipeId = $conn->insert_id;

        // B. ZUTATEN SPEICHERN
        $submittedIngredientNames   = $_POST['ing_name'] ?? [];
        $submittedIngredientAmounts = $_POST['ing_amount'] ?? [];
        $submittedIngredientUnits   = $_POST['ing_unit'] ?? [];

        $stmtInsertIng = $conn->prepare("INSERT IGNORE INTO ingredients (ingredient_name) VALUES (?)");
        $stmtCheckIng = $conn->prepare("SELECT ingredient_id FROM ingredients WHERE ingredient_name = ?");
        $stmtLinkIng = $conn->prepare("INSERT IGNORE INTO recipe_ingredients (recipe_id, ingredient_id, amount, unit) VALUES (?, ?, ?, ?)");

        foreach ($submittedIngredientNames as $index => $ingredientName) {
            if (!empty(trim($ingredientName))) {
                $stmtInsertIng->bind_param("s", $ingredientName);
                $stmtInsertIng->execute();
                
                $stmtCheckIng->bind_param("s", $ingredientName);
                $stmtCheckIng->execute();
                $res = $stmtCheckIng->get_result();
                $ingredientData = $res->fetch_assoc();
                $ingredientId = $ingredientData['ingredient_id'];

                $amountValue = floatval($submittedIngredientAmounts[$index]);
                $unitValue   = $submittedIngredientUnits[$index];
                $stmtLinkIng->bind_param("iids", $newRecipeId, $ingredientId, $amountValue, $unitValue);
                $stmtLinkIng->execute();
            }
        }

        // C. ZUBEREITUNGSSCHRITTE
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

        // D. KATEGORIEN
        $stmtCat = $conn->prepare("INSERT INTO recipe_categories (recipe_id, category_id) VALUES (?, ?)");
        foreach ($selectedCategories as $categoryId) {
            $stmtCat->bind_param("ii", $newRecipeId, $categoryId);
            $stmtCat->execute();
        }

        // E. BILDER-UPLOAD (Hier lag der Fehler)
        // Wir prüfen: Gibt es das Feld 'image' UND ist der Name des ersten Bildes nicht leer?
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'][0])) {
            $uploadPath = 'resources/uploads/recipes/';
            
            // Falls der Ordner fehlt, erstellen (einfach & anfängerfreundlich)
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($_FILES['image']['tmp_name'] as $index => $temporaryLocation) {
                // Nur verarbeiten, wenn kein Fehler beim Upload vorliegt (Code 0 = OK)
                if ($_FILES['image']['error'][$index] === 0) {
                    $fileExtension = pathinfo($_FILES['image']['name'][$index], PATHINFO_EXTENSION);
                    $uniqueFileName = "recipe_" . $newRecipeId . "_" . time() . "_" . $index . "." . $fileExtension;
                    $finalDestination = $uploadPath . $uniqueFileName;

                    if (move_uploaded_file($temporaryLocation, $finalDestination)) {
                        $conn->query("INSERT INTO recipe_images (recipe_id, image_path) VALUES ($newRecipeId, '$finalDestination')");
                    }
                }
            }
        }
        $isUploadSuccessful = true;
    } else {
        $errorMessage = "Hoppla, da lief was schief.";
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
                            <div id="ingredient-input-container">
                                </div>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none" onclick="addIngredientRow()">+ Weitere Zutat</button>
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-muted">ZUBEREITUNG</label>
                            <textarea name="steps" id="steps" class="form-control border-0 bg-light" rows="5" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-muted d-block mb-2">KATEGORIEN</label>
                            <div class="row g-2">
                                <?php foreach ($categoryOptions as $category): ?>
                                <div class="col-6 col-md-4">
                                    <div class="form-check small">
                                        <input class="form-check-input cat-check" type="checkbox" name="categories[]" value="<?php echo $category['category_id']; ?>">
                                        <label class="form-check-label"><?php echo $category['category_name']; ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="small fw-bold text-muted">FOTOS (OPTIONAL)</label>
                            <input type="file" name="image[]" class="form-control border-0 bg-light" multiple>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">VERÖFFENTLICHEN</button>
                    </form>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <script>
        const container = document.getElementById('ingredient-input-container');

        function addIngredientRow(data = {amount: '', unit: 'cl', name: ''}) {
            let newRow = document.createElement('div');
            newRow.className = 'row g-2 mb-2 ingredient-row';
            newRow.innerHTML = `
                <div class="col-3"><input type="number" step="0.1" name="ing_amount[]" class="form-control bg-light border-0 ing-amount" value="${data.amount}" placeholder="Menge"></div>
                <div class="col-3">
                    <select name="ing_unit[]" class="form-select bg-light border-0 ing-unit">
                        <option value="cl" ${data.unit === 'cl' ? 'selected' : ''}>cl</option>
                        <option value="ml" ${data.unit === 'ml' ? 'selected' : ''}>ml</option>
                        <option value="oz" ${data.unit === 'oz' ? 'selected' : ''}>oz</option>
                        <option value="Stück" ${data.unit === 'Stück' ? 'selected' : ''}>Stück</option>
                    </select>
                </div>
                <div class="col-6"><input type="text" name="ing_name[]" class="form-control bg-light border-0 ing-name" value="${data.name}" placeholder="Zutat"></div>
            `;
            container.appendChild(newRow);
        }

        // LocalStorage Logik
        function saveToLocal() {
            const ingredients = [];
            document.querySelectorAll('.ingredient-row').forEach(row => {
                ingredients.push({
                    amount: row.querySelector('.ing-amount').value,
                    unit: row.querySelector('.ing-unit').value,
                    name: row.querySelector('.ing-name').value
                });
            });
            const draft = {
                name: document.getElementById('recipe_name').value,
                description: document.getElementById('description').value,
                steps: document.getElementById('steps').value,
                ingredients: ingredients
            };
            localStorage.setItem('recipeDraft', JSON.stringify(draft));
        }

        function loadFromLocal() {
            const raw = localStorage.getItem('recipeDraft');
            if (!raw) { addIngredientRow(); return; }
            const draft = JSON.parse(raw);
            document.getElementById('recipe_name').value = draft.name || '';
            document.getElementById('description').value = draft.description || '';
            document.getElementById('steps').value = draft.steps || '';
            if (draft.ingredients && draft.ingredients.length > 0) {
                draft.ingredients.forEach(ing => addIngredientRow(ing));
            } else { addIngredientRow(); }
        }

        document.getElementById('recipeForm').addEventListener('input', saveToLocal);
        window.addEventListener('load', loadFromLocal);
    </script>
    <?php include "includes/footer.php"; ?>
</body>
</html>