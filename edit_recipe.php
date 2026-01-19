<?php
session_start();
require_once "util/dbutil.php";
include "util/auth_check.php";

$idUser = $_SESSION['userid'];
$idRecipe = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sqlBase = "SELECT * FROM recipes WHERE recipe_id = ? AND created_by = ?";
$stmt = $conn->prepare($sqlBase);
$stmt->bind_param("ii", $idRecipe, $idUser);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();

if (!$recipe) {
    die("Kein Zugriff auf dieses Rezept.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_recipe'])) {

    $sqlUpd = "UPDATE recipes SET recipe_name = ?, beschreibung = ?, anleitung = ? WHERE recipe_id = ?";
    $stmtUpd = $conn->prepare($sqlUpd);
    $stmtUpd->bind_param("sssi", $_POST['recipe_name'], $_POST['beschreibung'], $_POST['anleitung'], $idRecipe);
    $stmtUpd->execute();

    $conn->query("DELETE FROM recipe_categories WHERE recipe_id = $idRecipe");
    if (!empty($_POST['categories'])) {
        $stmtCat = $conn->prepare("INSERT INTO recipe_categories (recipe_id, category_id) VALUES (?, ?)");
        foreach ($_POST['categories'] as $catId) {
            $stmtCat->bind_param("ii", $idRecipe, $catId);
            $stmtCat->execute();
        }
    }

    $conn->query("DELETE FROM recipe_ingredients WHERE recipe_id = $idRecipe");
    $amounts = $_POST['amount'] ?? [];
    $units = $_POST['unit'] ?? [];
    $names = $_POST['ingredient'] ?? [];

    $stmtIns = $conn->prepare("INSERT IGNORE INTO ingredients (ingredient_name) VALUES (?)");
    $stmtGet = $conn->prepare("SELECT ingredient_id FROM ingredients WHERE ingredient_name = ?");
    $stmtLnk = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id, amount, unit) VALUES (?, ?, ?, ?)");

    foreach ($names as $idx => $name) {
        $name = trim($name);
        if ($name != "") {
            $stmtIns->bind_param("s", $name);
            $stmtIns->execute();
            $stmtGet->bind_param("s", $name);
            $stmtGet->execute();
            $ingId = $stmtGet->get_result()->fetch_assoc()['ingredient_id'];

            $amt = floatval($amounts[$idx]);
            $unt = $units[$idx];
            $stmtLnk->bind_param("iids", $idRecipe, $ingId, $amt, $unt);
            $stmtLnk->execute();
        }
    }

    if (!empty($_POST['delete_images'])) {
        $stmtImg = $conn->prepare("SELECT image_path FROM recipe_images WHERE image_id = ?");
        foreach ($_POST['delete_images'] as $imgId) {
            $stmtImg->bind_param("i", $imgId);
            $stmtImg->execute();
            $path = $stmtImg->get_result()->fetch_assoc()['image_path'];
            if ($path && file_exists($path)) {
                unlink($path);
            }
            $conn->query("DELETE FROM recipe_images WHERE image_id = " . (int) $imgId);
        }
    }

    if (!empty($_FILES['new_images']['name'][0])) {
        $uploadDir = 'resources/uploads/recipes/';
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        foreach ($_FILES['new_images']['tmp_name'] as $i => $tmp) {
            if ($_FILES['new_images']['error'][$i] === 0) {

                $ext = strtolower(pathinfo($_FILES['new_images']['name'][$i], PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    continue;
                }
                $file = "recipe_" . $idRecipe . "_" . time() . "_" . $i . "." . $ext;

                if (move_uploaded_file($tmp, $uploadDir . $file)) {
                    $fullPath = $uploadDir . $file;
                    $conn->query(
                        "INSERT INTO recipe_images (recipe_id, image_path) 
                     VALUES ($idRecipe, '$fullPath')"
                    );
                }
            }
        }
    }


    header("Location: recipe.php?id=$idRecipe&msg=updated");
    exit;
}

$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC")->fetch_all(MYSQLI_ASSOC);
$resIngs = $conn->query("SELECT ri.*, i.ingredient_name FROM recipe_ingredients ri JOIN ingredients i ON ri.ingredient_id = i.ingredient_id WHERE ri.recipe_id = $idRecipe");
$currentIngredients = $resIngs->fetch_all(MYSQLI_ASSOC);
$images = $conn->query("SELECT * FROM recipe_images WHERE recipe_id = $idRecipe")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Anpassen - MIXD</title>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row g-4">

                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                        <h5 class="section-title">Basis-Informationen</h5>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted">NAME DES DRINKS</label>
                            <input type="text" name="recipe_name" class="form-control form-control-custom"
                                value="<?= htmlspecialchars($recipe['recipe_name']) ?>" required>
                        </div>
                        <div class="mb-0">
                            <label class="small fw-bold text-muted">BESCHREIBUNG</label>
                            <textarea name="beschreibung" class="form-control form-control-custom"
                                rows="3"><?= htmlspecialchars($recipe['beschreibung']) ?></textarea>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                        <h5 class="section-title">Zutaten (Max. 15)</h5>
                        <?php
                        for ($i = 0; $i < 15; $i++):
                            $ing = $currentIngredients[$i] ?? null;
                            ?>
                            <div class="row g-2 mb-2 align-items-center">
                                <div class="col-2">
                                    <input type="number" step="0.1" name="amount[]"
                                        class="form-control form-control-custom text-center"
                                        value="<?= $ing ? (float) $ing['amount'] : '' ?>" placeholder="Anz.">
                                </div>
                                <div class="col-3">
                                    <select name="unit[]" class="form-select form-control-custom">
                                        <?php $u = $ing['unit'] ?? 'cl'; ?>
                                        <option value="cl" <?= $u == 'cl' ? 'selected' : '' ?>>cl</option>
                                        <option value="ml" <?= $u == 'ml' ? 'selected' : '' ?>>ml</option>
                                        <option value="Stück" <?= $u == 'Stück' ? 'selected' : '' ?>>Stück</option>
                                        <option value="BL" <?= $u == 'BL' ? 'selected' : '' ?>>BL</option>
                                    </select>
                                </div>
                                <div class="col-7">
                                    <input type="text" name="ingredient[]" class="form-control form-control-custom"
                                        value="<?= $ing ? htmlspecialchars($ing['ingredient_name']) : '' ?>"
                                        placeholder="Zutat <?= $i + 1 ?>">
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                        <h5 class="section-title text-center">Bildverwaltung</h5>
                        <div class="d-flex flex-wrap gap-2 justify-content-center mb-3">
                            <?php foreach ($images as $img): ?>
                                <div class="text-center" style="width: 80px;">
                                    <img src="<?= $img['image_path'] ?>" class="edit-img-thumbnail shadow-sm">
                                    <div class="form-check">
                                        <input class="form-check-input mx-auto" type="checkbox" name="delete_images[]"
                                            value="<?= $img['image_id'] ?>">
                                        <label class="label-delete d-block">LÖSCHEN</label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <hr class="my-3">
                        <label class="small fw-bold text-muted mb-2">NEUE BILDER HOCHLADEN</label>
                        <input type="file" name="new_images[]" class="form-control form-control-sm form-control-custom"
                            multiple>
                    </div>

                    <button type="submit" name="save_recipe"
                        class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm">
                        ÄNDERUNGEN SPEICHERN
                    </button>

                    <div class="text-center mt-3">
                        <a href="recipe.php?id=<?= $idRecipe ?>" class="text-muted small text-decoration-none">Abbrechen
                            & Zurück</a>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <?php include "includes/footer.php"; ?>
</body>

</html>