<?php
/**
 * MIXD - Rezept-Detailansicht (Final Version)
 * Fokus: Design-Elemente (Barkeeper Badge & Kreis-Favorit)
 */
session_start();
require_once "util/dbutil.php";

$recipeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$servings = isset($_GET['servings']) ? (int) $_GET['servings'] : 1;
if ($servings < 1)
    $servings = 1;

if ($recipeId <= 0) {
    header("Location: index.php");
    exit;
}

$sqlRecipe = "SELECT r.*, u.benutzername, u.is_barkeeper 
              FROM recipes r 
              JOIN users u ON r.created_by = u.userid 
              WHERE r.recipe_id = ?";
$stmt = $conn->prepare($sqlRecipe);
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();

if (!$recipe)
    die("Drink nicht gefunden.");

$stmtAvg = $conn->prepare("SELECT AVG(stars) as avg, COUNT(*) as total FROM ratings WHERE recipe_id = ?");
$stmtAvg->bind_param("i", $recipeId);
$stmtAvg->execute();
$stats = $stmtAvg->get_result()->fetch_assoc();
$avgStars = round($stats['avg'] ?? 0, 1);
$totalRatings = $stats['total'] ?? 0;

$stmtIng = $conn->prepare("SELECT ri.amount, ri.unit, i.ingredient_name 
                           FROM recipe_ingredients ri 
                           JOIN ingredients i ON ri.ingredient_id = i.ingredient_id 
                           WHERE ri.recipe_id = ?");
$stmtIng->bind_param("i", $recipeId);
$stmtIng->execute();
$ingredients = $stmtIng->get_result()->fetch_all(MYSQLI_ASSOC);

$stmtImg = $conn->prepare("SELECT image_path FROM recipe_images WHERE recipe_id = ?");
$stmtImg->bind_param("i", $recipeId);
$stmtImg->execute();
$recipeImages = $stmtImg->get_result()->fetch_all(MYSQLI_ASSOC);

$stmtComm = $conn->prepare("SELECT rating_image FROM ratings WHERE recipe_id = ? AND rating_image IS NOT NULL");
$stmtComm->bind_param("i", $recipeId);
$stmtComm->execute();
$commImages = $stmtComm->get_result()->fetch_all(MYSQLI_ASSOC);

$allImages = array_merge(
    array_column($recipeImages, 'image_path'),
    array_column($commImages, 'rating_image')
);

$isFav = false;
if (isset($_SESSION['userid'])) {
    $stmtFav = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmtFav->bind_param("ii", $_SESSION['userid'], $recipeId);
    $stmtFav->execute();
    if ($stmtFav->get_result()->num_rows > 0)
        $isFav = true;
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <?php include "includes/head-includes.php"; ?>
    <title><?= htmlspecialchars($recipe['recipe_name']) ?> - MIXD</title>
</head>

<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div id="recipeCarousel" class="carousel slide shadow rounded-4 overflow-hidden mb-4"
                    data-bs-ride="carousel">
                    <div class="carousel-inner bg-dark" style="height: 450px;">
                        <?php if (empty($allImages)): ?>
                            <div class="carousel-item active h-100">
                                <img src="resources/images/placeholders/platzhalter2.png"
                                    class="d-block w-100 h-100 object-fit-cover">
                            </div>
                        <?php else: ?>
                            <?php foreach ($allImages as $index => $path): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> h-100">
                                    <img src="<?= htmlspecialchars($path) ?>" class="d-block w-100 h-100 object-fit-cover">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h1 class="fw-bold mb-1"><?= htmlspecialchars($recipe['recipe_name']) ?></h1>
                        <div class="mb-2">
                            <span class="text-warning fw-bold">★ <?= $avgStars ?></span>
                            <span class="text-muted small">(<?= $totalRatings ?> Bewertungen)</span>
                        </div>
                        <p class="text-muted d-flex align-items-center">
                            Von <strong class="ms-1"><?= htmlspecialchars($recipe['benutzername']) ?></strong>

                            <?php if ($recipe['is_barkeeper']): ?>
                                <span class="badge bg-primary rounded-pill ms-2 d-flex align-items-center">
                                    <i class="bi bi-patch-check-fill me-1"></i> Verifizierter Barkeeper
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <a href="util/toggle_favorite.php?id=<?= $recipeId ?>"
                        class="btn <?= $isFav ? 'btn-danger' : 'btn-outline-danger' ?> fav-circle shadow-sm">
                        <i class="bi <?= $isFav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                    </a>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                    <h5 class="fw-bold mb-3 text-uppercase small text-muted">Über diesen Drink</h5>
                    <p class="lead mb-0 text-secondary"><?= nl2br(htmlspecialchars($recipe['beschreibung'])) ?></p>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold mb-0">Zutaten</h4>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill"
                                data-bs-toggle="dropdown">
                                <?= $servings ?> Portion(en)
                            </button>
                            <ul class="dropdown-menu">
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <li><a class="dropdown-item" href="?id=<?= $recipeId ?>&servings=<?= $i ?>"><?= $i ?>
                                            Portionen</a></li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    </div>
                    <table class="table table-borderless mb-0">
                        <?php foreach ($ingredients as $ing): ?>
                            <tr class="border-bottom">
                                <td class="py-3 text-primary fw-bold" style="width: 30%;">
                                    <?= (float) ($ing['amount'] * $servings) ?>     <?= htmlspecialchars($ing['unit']) ?>
                                </td>
                                <td class="py-3"><?= htmlspecialchars($ing['ingredient_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                    <h4 class="fw-bold mb-4">Zubereitung</h4>
                    <div class="lh-lg">
                        <?= nl2br(htmlspecialchars($recipe['anleitung'])) ?>
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h5 class="fw-bold mb-3 text-center">Diesen Drink bewerten</h5>
                    <?php if (isset($_SESSION['userid'])): ?>
                        <form action="util/submit_rating.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="recipe_id" value="<?= $recipeId ?>">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <select name="stars" class="form-select border-0 bg-light py-2">
                                        <option value="5">⭐⭐⭐⭐⭐</option>
                                        <option value="4">⭐⭐⭐⭐</option>
                                        <option value="3">⭐⭐⭐</option>
                                        <option value="2">⭐⭐</option>
                                        <option value="1">⭐</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="file" name="rating_pic" class="form-control border-0 bg-light py-2">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit"
                                        class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-pill">SENDEN</button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center p-2">
                            <p class="text-muted small mb-0">Bitte <a href="login.php" class="fw-bold">logge dich ein</a>,
                                um eine Bewertung abzugeben.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>

</html>