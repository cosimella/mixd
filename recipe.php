<?php
session_start();
include "util/dbutil.php";

// 1. PARAMETER AUS URL HOLEN
$recipeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$servingsCount = isset($_GET['servings']) ? (int) $_GET['servings'] : 1;
if ($servingsCount < 1) $servingsCount = 1;

if ($recipeId <= 0) {
    header("Location: index.php");
    exit;
}

// 2. HAUPTDATEN LADEN
$stmtRecipe = $conn->prepare("SELECT r.*, u.benutzername, u.is_barkeeper FROM recipes r JOIN users u ON r.created_by = u.userid WHERE r.recipe_id = ?");
$stmtRecipe->bind_param("i", $recipeId);
$stmtRecipe->execute();
$recipeData = $stmtRecipe->get_result()->fetch_assoc();

if (!$recipeData) {
    die("Drink nicht gefunden.");
}

// --- DURCHSCHNITTS-STERNE BERECHNEN ---
$stmtAvg = $conn->prepare("SELECT AVG(stars) as average, COUNT(*) as total FROM ratings WHERE recipe_id = ?");
$stmtAvg->bind_param("i", $recipeId);
$stmtAvg->execute();
$ratingStats = $stmtAvg->get_result()->fetch_assoc();
$averageStars = round($ratingStats['average'], 1);
$totalRatings = $ratingStats['total'];

// --- COMMUNITY FOTOS LADEN ---
$stmtCommPics = $conn->prepare("SELECT rating_image FROM ratings WHERE recipe_id = ? AND rating_image IS NOT NULL ORDER BY created_at DESC");
$stmtCommPics->bind_param("i", $recipeId);
$stmtCommPics->execute();
$communityPhotos = $stmtCommPics->get_result()->fetch_all(MYSQLI_ASSOC);

// 3. ZUTATEN LADEN
$stmtIng = $conn->prepare("SELECT ri.amount, ri.unit, i.ingredient_name FROM recipe_ingredients ri JOIN ingredients i ON ri.ingredient_id = i.ingredient_id WHERE ri.recipe_id = ?");
$stmtIng->bind_param("i", $recipeId);
$stmtIng->execute();
$ingredientsList = $stmtIng->get_result()->fetch_all(MYSQLI_ASSOC);

// 4. ZUBEREITUNGSSCHRITTE LADEN
$stmtSteps = $conn->prepare("SELECT instruction FROM recipe_steps WHERE recipe_id = ? ORDER BY step_number ASC");
$stmtSteps->bind_param("i", $recipeId);
$stmtSteps->execute();
$preparationSteps = $stmtSteps->get_result()->fetch_all(MYSQLI_ASSOC);

// 5. REZEPTBILDER LADEN
$stmtImg = $conn->prepare("SELECT image_path FROM recipe_images WHERE recipe_id = ?");
$stmtImg->bind_param("i", $recipeId);
$stmtImg->execute();
$recipeImages = $stmtImg->get_result()->fetch_all(MYSQLI_ASSOC);

// --- KOMBINIEREN: Originalbilder + Community Bilder ---
$allImages = [];
foreach ($recipeImages as $img) {
    $allImages[] = $img['image_path'];
}
foreach ($communityPhotos as $cp) {
    $allImages[] = $cp['rating_image'];
}

// Falls gar kein Bild da ist (weder Original noch Community), Platzhalter setzen
if (empty($allImages)) {
    $allImages[] = 'resources/images/placeholders/platzhalter2.png';
}

// 6. FAVORITEN-STATUS
$isUserFavorite = false;
if (isset($_SESSION['userid'])) {
    $currentUserId = $_SESSION['userid'];
    $stmtFav = $conn->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmtFav->bind_param("ii", $currentUserId, $recipeId);
    $stmtFav->execute();
    if ($stmtFav->get_result()->num_rows > 0) {
        $isUserFavorite = true;
    }
}

$showRatingAlert = isset($_GET['msg']) && $_GET['msg'] == 'bewertet';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title><?php echo htmlspecialchars($recipeData['recipe_name']); ?> - MIXD</title>
</head>
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div id="recipeCarousel" class="carousel slide shadow rounded-4 overflow-hidden mb-4" data-bs-ride="carousel">
                    <div class="carousel-inner bg-dark" style="height: 500px;">
                        <?php foreach ($allImages as $index => $path): ?>
                            <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?> h-100">
                                <img src="<?php echo htmlspecialchars($path); ?>" 
                                     class="d-block w-100 h-100 object-fit-cover" 
                                     onerror="this.src='resources/images/placeholders/platzhalter2.png';">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($allImages) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#recipeCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#recipeCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h1 class="fw-bold mb-1"><?php echo htmlspecialchars($recipeData['recipe_name']); ?></h1>
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-warning me-2" style="letter-spacing: 2px;">
                                <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo ($i <= round($averageStars)) ? '★' : '☆';
                                    }
                                ?>
                            </span>
                            <span class="small text-muted fw-bold">
                                <?php echo $totalRatings > 0 ? $averageStars . " / 5 ($totalRatings)" : "Noch keine Bewertungen"; ?>
                            </span>
                        </div>
                        <p class="text-muted mb-0">
                            Von <strong><?php echo htmlspecialchars($recipeData['benutzername']); ?></strong>
                            <?php if ($recipeData['is_barkeeper']): ?>
                                <span class="badge bg-primary rounded-pill ms-2"><i class="bi bi-patch-check-fill"></i> verifizierter Barkeeper</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <a href="util/toggle_favorite.php?id=<?php echo $recipeId; ?>" 
                       class="btn <?php echo $isUserFavorite ? 'btn-danger' : 'btn-outline-danger'; ?> rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                       style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi <?php echo $isUserFavorite ? 'bi-heart-fill' : 'bi-heart'; ?> fs-5"></i>
                    </a>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                    <p class="lead mb-0 text-secondary"><?php echo nl2br(htmlspecialchars($recipeData['beschreibung'])); ?></p>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold mb-0">Zutaten</h4>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill" data-bs-toggle="dropdown">
                                <?php echo $servingsCount; ?> Portion(en)
                            </button>
                            <ul class="dropdown-menu">
                                <?php for($i=1; $i<=6; $i++): ?>
                                    <li><a class="dropdown-item" href="?id=<?php echo $recipeId; ?>&servings=<?php echo $i; ?>"><?php echo $i; ?> Portion(en)</a></li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    </div>
                    <table class="table table-borderless">
                        <?php foreach ($ingredientsList as $ing): ?>
                        <tr class="border-bottom">
                            <td class="py-3 fw-bold text-primary" style="width: 120px;">
                                <?php echo (float)($ing['amount'] * $servingsCount); ?> <?php echo htmlspecialchars($ing['unit']); ?>
                            </td>
                            <td class="py-3"><?php echo htmlspecialchars($ing['ingredient_name']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                    <h4 class="fw-bold mb-4">Zubereitung</h4>
                    <?php foreach ($preparationSteps as $idx => $step): ?>
                        <div class="d-flex mb-4">
                            <div class="me-3">
                                <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:30px; height:30px;">
                                    <?php echo $idx + 1; ?>
                                </span>
                            </div>
                            <div class="pt-1"><?php echo htmlspecialchars($step['instruction']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h5 class="fw-bold mb-3">Diesen Drink bewerten</h5>
                    <?php if ($showRatingAlert): ?>
                        <div class="alert alert-success alert-dismissible fade show border-0" role="alert">
                            Bewertung gespeichert!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['userid'])): ?>
                        <form action="util/submit_rating.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="recipe_id" value="<?php echo $recipeId; ?>">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <select name="stars" class="form-select bg-light border-0 py-2">
                                        <option value="5">⭐⭐⭐⭐⭐</option>
                                        <option value="4">⭐⭐⭐⭐</option>
                                        <option value="3">⭐⭐⭐</option>
                                        <option value="2">⭐⭐</option>
                                        <option value="1">⭐</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="file" name="rating_pic" class="form-control bg-light border-0 py-2" accept="image/*">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">Senden</button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <p class="text-center small text-muted">Einloggen zum Bewerten.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>