<?php
session_start();
require_once "util/dbutil.php";
include "util/auth_check.php";

$idUser = $_SESSION['userid'];
$success = false;

$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $sqlRecipe = "INSERT INTO recipes (recipe_name, beschreibung, anleitung, created_by) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlRecipe);
    $stmt->bind_param("sssi", $_POST['recipe_name'], $_POST['description'], $_POST['steps'], $idUser);
    
    if ($stmt->execute()) {
        $idNewRecipe = $conn->insert_id;

        if (!empty($_POST['categories'])) {
            $stmtCat = $conn->prepare("INSERT INTO recipe_categories (recipe_id, category_id) VALUES (?, ?)");
            foreach ($_POST['categories'] as $catId) {
                $stmtCat->bind_param("ii", $idNewRecipe, $catId);
                $stmtCat->execute();
            }
        }

        $names   = $_POST['ing_name'] ?? [];
        $amounts = $_POST['ing_amount'] ?? [];
        $units   = $_POST['ing_unit'] ?? [];

        $stmtIns = $conn->prepare("INSERT IGNORE INTO ingredients (ingredient_name) VALUES (?)");
        $stmtGet = $conn->prepare("SELECT ingredient_id FROM ingredients WHERE ingredient_name = ?");
        $stmtLnk = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id, amount, unit) VALUES (?, ?, ?, ?)");

        foreach ($names as $i => $name) {
            $name = trim($name);
            if ($name != "") {
                $stmtIns->bind_param("s", $name); $stmtIns->execute();
                $stmtGet->bind_param("s", $name); $stmtGet->execute();
                $idIng = $stmtGet->get_result()->fetch_assoc()['ingredient_id'];

                $val = floatval($amounts[$i]);
                $unit = $units[$i];
                $stmtLnk->bind_param("iids", $idNewRecipe, $idIng, $val, $unit);
                $stmtLnk->execute();
            }
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'][0] === 0) {
            $path = 'resources/uploads/recipes/';
            foreach ($_FILES['image']['tmp_name'] as $i => $tmp) {
                $ext = pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION);
                $file = "recipe_" . $idNewRecipe . "_" . time() . "_" . $i . "." . $ext;
                if (move_uploaded_file($tmp, $path . $file)) {
                    $fullPath = $path . $file;
                    $conn->query("INSERT INTO recipe_images (recipe_id, image_path) VALUES ($idNewRecipe, '$fullPath')");
                }
            }
        }
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Neuer Drink - MIXD</title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <?php if ($success): ?>
                    <div class="card shadow-sm border-0 rounded-4 p-5 text-center">
                        <i class="bi bi-stars text-warning display-1"></i>
                        <h2 class="fw-bold mt-3">Veröffentlicht!</h2>
                        <div class="mt-4">
                            <a href="recipe.php?id=<?= $idNewRecipe ?>" class="btn btn-primary rounded-pill px-4">Zum Drink</a>
                            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 ms-2">Startseite</a>
                        </div>
                    </div>
                <?php else: ?>

                <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                    <h2 class="fw-bold mb-4">Neuer Drink</h2>
                    <form action="create_recipe.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-4">
                            <label class="small fw-bold text-muted text-uppercase">Name des Drinks</label>
                            <input type="text" name="recipe_name" class="form-control border-0 bg-light py-3 rounded-3" placeholder="z.B. Gin Basil Smash" required>
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-muted text-uppercase">Kurze Beschreibung</label>
                            <textarea name="description" class="form-control border-0 bg-light py-3 rounded-3" rows="2" placeholder="Was macht diesen Cocktail besonders?"></textarea>
                        </div>

                        <div class="mb-4 p-4 bg-white border rounded-4">
                            <label class="small fw-bold text-muted mb-3 d-block text-uppercase">Kategorien</label>
                            <div class="row g-2">
                                <?php foreach ($categories as $cat): ?>
                                <div class="col-6 col-md-4">
                                    <div class="form-check small">
                                        <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $cat['category_id'] ?>" id="c<?= $cat['category_id'] ?>">
                                        <label class="form-check-label" for="c<?= $cat['category_id'] ?>">
                                            <?= htmlspecialchars($cat['category_name']) ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-4 p-4 bg-white border rounded-4">
                            <label class="small fw-bold text-muted mb-3 d-block text-uppercase">Zutaten (Max. 15)</label>
                            <?php for ($i = 1; $i <= 15; $i++): ?>
                            <div class="row g-2 mb-2">
                                <div class="col-3">
                                    <input type="number" step="0.1" name="ing_amount[]" class="form-control border-0 bg-light py-2 rounded-3 text-center" placeholder="Menge">
                                </div>
                                <div class="col-3">
                                    <select name="ing_unit[]" class="form-select border-0 bg-light py-2 rounded-3">
                                        <option value="cl">cl</option>
                                        <option value="ml">ml</option>
                                        <option value="g">g</option>
                                        <option value="Stück">Stück</option>
                                        <option value="Priese">Priese</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="text" name="ing_name[]" class="form-control border-0 bg-light py-2 rounded-3" placeholder="Zutat <?= $i ?>">
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-muted text-uppercase">Zubereitungsschritte</label>
                            <textarea name="steps" class="form-control border-0 bg-light py-3 rounded-3" rows="6" placeholder="1. Eis in das Glas geben...&#10;2. Zutaten verrühren..." required></textarea>
                        </div>

                        <div class="mb-5">
                            <label class="small fw-bold text-muted text-uppercase">Fotos</label>
                            <input type="file" name="image[]" class="form-control border-0 bg-light py-3 rounded-3" multiple>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">SPEICHERN & VERÖFFENTLICHEN</button>
                    </form>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>