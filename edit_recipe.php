<?php
session_start();
include "util/dbutil.php";
include "util/auth_check.php";

$currentUserId = $_SESSION['userid'];
$targetRecipeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$queryBaseData = "SELECT * FROM recipes WHERE recipe_id = ? AND created_by = ?";
$statementBase = $conn->prepare($queryBaseData);
$statementBase->bind_param("ii", $targetRecipeId, $currentUserId);
$statementBase->execute();
$recipeData = $statementBase->get_result()->fetch_assoc();

if (!$recipeData) {
    die("Kein Zugriff auf dieses Rezept.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_recipe'])) {
    
    $recipeName = $_POST['recipe_name'];
    $beschreibung = $_POST['beschreibung'];
    $anleitung = $_POST['anleitung'];
    $selectedCategories = $_POST['categories'] ?? [];

    $updateStmt = $conn->prepare("UPDATE recipes SET recipe_name = ?, beschreibung = ?, anleitung = ? WHERE recipe_id = ?");
    $updateStmt->bind_param("sssi", $recipeName, $beschreibung, $anleitung, $targetRecipeId);
    $updateStmt->execute();

    $conn->query("DELETE FROM recipe_categories WHERE recipe_id = $targetRecipeId");
    foreach ($selectedCategories as $catId) {
        $conn->query("INSERT INTO recipe_categories (recipe_id, category_id) VALUES ($targetRecipeId, $catId)");
    }

    $conn->query("DELETE FROM recipe_ingredients WHERE recipe_id = $targetRecipeId");
    $amounts = $_POST['amount'] ?? [];
    $units = $_POST['unit'] ?? [];
    $ingredients = $_POST['ingredient'] ?? [];

    foreach ($ingredients as $idx => $ingName) {
        if (!empty(trim($ingName))) {
           
            $conn->query("INSERT IGNORE INTO ingredients (ingredient_name) VALUES ('$ingName')");
            $res = $conn->query("SELECT ingredient_id FROM ingredients WHERE ingredient_name = '$ingName'");
            $ingId = $res->fetch_assoc()['ingredient_id'];
            
            $amt = floatval($amounts[$idx]);
            $unt = $units[$idx];
            $conn->query("INSERT INTO recipe_ingredients (recipe_id, ingredient_id, amount, unit) VALUES ($targetRecipeId, $ingId, $amt, '$unt')");
        }
    }

    if (!empty($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $imgId) {
            $imgId = (int)$imgId;
            $res = $conn->query("SELECT image_path FROM recipe_images WHERE image_id = $imgId");
            $pathData = $res->fetch_assoc();
            if ($pathData && file_exists($pathData['image_path'])) {
                unlink($pathData['image_path']); 
            }
            $conn->query("DELETE FROM recipe_images WHERE image_id = $imgId");
        }
    }

    if (!empty($_FILES['new_images']['name'][0])) {
        $uploadPath = 'resources/uploads/recipes/';
        foreach ($_FILES['new_images']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['new_images']['error'][$index] === 0) {
                $ext = pathinfo($_FILES['new_images']['name'][$index], PATHINFO_EXTENSION);
                $newName = "recipe_" . $targetRecipeId . "_" . time() . "_" . $index . "." . $ext;
                if (move_uploaded_file($tmpName, $uploadPath . $newName)) {
                    $finalPath = $uploadPath . $newName;
                    $conn->query("INSERT INTO recipe_images (recipe_id, image_path) VALUES ($targetRecipeId, '$finalPath')");
                }
            }
        }
    }

    header("Location: recipe.php?id=$targetRecipeId&msg=updated");
    exit;
}

$queryAllCategories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$categoryList = $queryAllCategories->fetch_all(MYSQLI_ASSOC);

$selectedCategoryIds = [];
$resCat = $conn->query("SELECT category_id FROM recipe_categories WHERE recipe_id = $targetRecipeId");
while($row = $resCat->fetch_assoc()) $selectedCategoryIds[] = $row['category_id'];

$querySteps = $conn->query("SELECT instruction FROM recipe_steps WHERE recipe_id = $targetRecipeId ORDER BY step_number ASC");
$stepsFormattedText = implode("\n", array_column($querySteps->fetch_all(MYSQLI_ASSOC), 'instruction'));

$queryIngredients = $conn->query("SELECT ri.*, i.ingredient_name FROM recipe_ingredients ri JOIN ingredients i ON ri.ingredient_id = i.ingredient_id WHERE ri.recipe_id = $targetRecipeId");
$ingredientsData = $queryIngredients->fetch_all(MYSQLI_ASSOC);

$queryImages = $conn->query("SELECT * FROM recipe_images WHERE recipe_id = $targetRecipeId");
$imagesData = $queryImages->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Rezept anpassen - MIXD</title>
</head>
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                        <h5 class="fw-bold mb-3">Basis-Infos</h5>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted">NAME DES COCKTAILS</label>
                            <input type="text" name="recipe_name" class="form-control border-0 bg-light" value="<?php echo htmlspecialchars($recipeData['recipe_name']); ?>" required>
                        </div>
                        <div class="mb-0">
                            <label class="small fw-bold text-muted">BESCHREIBUNG</label>
                            <textarea name="beschreibung" class="form-control border-0 bg-light" rows="3"><?php echo htmlspecialchars($recipeData['beschreibung']); ?></textarea>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                        <h5 class="fw-bold mb-3">Kategorien</h5>
                        <div class="row">
                            <?php foreach ($categoryList as $categoryItem): ?>
                            <div class="col-6 col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" 
                                           value="<?php echo $categoryItem['category_id']; ?>" 
                                           <?php echo in_array($categoryItem['category_id'], $selectedCategoryIds) ? 'checked' : ''; ?>>
                                    <label class="form-check-label small"><?php echo $categoryItem['category_name']; ?></label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                        <h5 class="fw-bold mb-3">Zutaten</h5>
                        <div id="zutaten-liste">
                            <?php foreach ($ingredientsData as $ingredient): ?>
                            <div class="row g-2 mb-2 align-items-center zutat-reihe">
                                <div class="col-2"><input type="number" step="0.1" name="amount[]" class="form-control border-0 bg-light" value="<?php echo (float)$ingredient['amount']; ?>"></div>
                                <div class="col-3">
                                    <select name="unit[]" class="form-select border-0 bg-light">
                                        <option value="cl" <?php if($ingredient['unit'] == 'cl') echo 'selected'; ?>>cl</option>
                                        <option value="ml" <?php if($ingredient['unit'] == 'ml') echo 'selected'; ?>>ml</option>
                                        <option value="Stück" <?php if($ingredient['unit'] == 'Stück') echo 'selected'; ?>>Stück</option>
                                        <option value="BL" <?php if($ingredient['unit'] == 'BL') echo 'selected'; ?>>BL</option>
                                    </select>
                                </div>
                                <div class="col-6"><input type="text" name="ingredient[]" class="form-control border-0 bg-light" value="<?php echo htmlspecialchars($ingredient['ingredient_name']); ?>"></div>
                                <div class="col-1 text-end"><button type="button" class="btn text-danger p-0" onclick="this.closest('.zutat-reihe').remove()"><i class="bi bi-trash"></i></button></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-link btn-sm text-decoration-none mt-2" onclick="addNewIngredientRow()">+ Zutat hinzufügen</button>
                    </div>

                    <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                        <h5 class="fw-bold mb-3">Schritt-für-Schritt Anleitung</h5>
                        <textarea name="anleitung" class="form-control border-0 bg-light" rows="8"><?php echo htmlspecialchars($stepsFormattedText); ?></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                        <h5 class="fw-bold mb-3 text-center">Bilder verwalten</h5>
                        <div class="d-flex flex-wrap gap-2 justify-content-center mb-3">
                            <?php foreach ($imagesData as $imageData): ?>
                                <div class="text-center" style="width: 80px;">
                                    <img src="<?php echo $imageData['image_path']; ?>" class="rounded shadow-sm mb-1" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="form-check">
                                        <input class="form-check-input mx-auto" type="checkbox" name="delete_images[]" value="<?php echo $imageData['image_id']; ?>">
                                        <label class="x-small text-danger d-block" style="font-size: 0.7rem;">Löschen</label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <hr>
                        <label class="small fw-bold text-muted mb-2">NEUE BILDER</label>
                        <input type="file" name="new_images[]" class="form-control form-control-sm border-0 bg-light" multiple>
                    </div>

                    <button type="submit" name="save_recipe" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow">
                        ÄNDERUNGEN SPEICHERN
                    </button>
                </div>
            </div>
        </form>
    </main>

    <script>
        function addNewIngredientRow() {
            let listContainer = document.getElementById('zutaten-liste');
            let rowWrapper = document.createElement('div');
            rowWrapper.className = 'row g-2 mb-2 align-items-center zutat-reihe';
            rowWrapper.innerHTML = `
                <div class="col-2"><input type="number" step="0.1" name="amount[]" class="form-control border-0 bg-light"></div>
                <div class="col-3">
                    <select name="unit[]" class="form-select border-0 bg-light">
                        <option value="cl">cl</option>
                        <option value="ml">ml</option>
                        <option value="Stück">Stück</option>
                        <option value="BL">BL</option>
                    </select>
                </div>
                <div class="col-6"><input type="text" name="ingredient[]" class="form-control border-0 bg-light"></div>
                <div class="col-1 text-end"><button type="button" class="btn text-danger p-0" onclick="this.closest('.zutat-reihe').remove()"><i class="bi bi-trash"></i></button></div>
            `;
            listContainer.appendChild(rowWrapper);
        }
    </script>
</body>
</html>