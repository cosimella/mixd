<?php

session_start();
require_once "util/dbutil.php";
include "util/auth_check.php";

$currentUserId = $_SESSION['userid'];

$stmtUser = $conn->prepare("SELECT benutzername, email, profile_image, is_barkeeper FROM users WHERE userid = ?");
$stmtUser->bind_param("i", $currentUserId);
$stmtUser->execute();
$userData = $stmtUser->get_result()->fetch_assoc();

$stmtApp = $conn->prepare("SELECT status FROM barkeeper_applications WHERE userid = ? ORDER BY created_at DESC LIMIT 1");
$stmtApp->bind_param("i", $currentUserId);
$stmtApp->execute();
$resApp = $stmtApp->get_result();
$verificationStatus = ($resApp->num_rows > 0) ? $resApp->fetch_assoc()['status'] : null;

$sqlBase = "SELECT r.recipe_id, r.recipe_name, ri.image_path, u.is_barkeeper 
            FROM recipes r 
            JOIN users u ON r.created_by = u.userid 
            LEFT JOIN recipe_images ri ON r.recipe_id = ri.recipe_id ";

$favSql = $sqlBase . "JOIN favorites f ON r.recipe_id = f.recipe_id WHERE f.user_id = ? GROUP BY r.recipe_id LIMIT 4";
$stmtFav = $conn->prepare($favSql);
$stmtFav->bind_param("i", $currentUserId);
$stmtFav->execute();
$favoriteRecipes = $stmtFav->get_result()->fetch_all(MYSQLI_ASSOC);

$ownSql = $sqlBase . "WHERE r.created_by = ? GROUP BY r.recipe_id LIMIT 4";
$stmtOwn = $conn->prepare($ownSql);
$stmtOwn->bind_param("i", $currentUserId);
$stmtOwn->execute();
$ownRecipes = $stmtOwn->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Profil - MIXD</title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5 flex-grow-1">
        <div class="row">
            <aside class="col-md-4 col-lg-3 mb-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 text-center mb-4">
                    <img src="<?= $userData['profile_image'] ?: 'resources/images/placeholders/default_profile.png' ?>" 
                         class="rounded-circle mb-3 mx-auto shadow-sm" style="width: 110px; height: 110px; object-fit: cover;">
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($userData['benutzername']) ?></h5>
                    <p class="text-muted small mb-3"><?= htmlspecialchars($userData['email']) ?></p>
                    <a href="edit_profile.php" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">Profil bearbeiten</a>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <h6 class="fw-bold mb-3 text-uppercase small text-muted">Verifizierung</h6>
                    <?php if ($userData['is_barkeeper'] == 1): ?>
                        <div class="text-success small fw-bold mb-2">
                            <i class="bi bi-patch-check-fill"></i> Barkeeper Profi
                        </div>
                    <?php elseif ($verificationStatus == 'pending'): ?>
                        <div class="text-warning small fw-bold mb-2">
                            <i class="bi bi-hourglass-split"></i> In Prüfung...
                        </div>
                    <?php else: ?>
                        <p class="x-small text-muted mb-3">Werde verifizierter Barkeeper für volle Sichtbarkeit.</p>
                        <a href="apply_barkeeper.php" class="btn btn-dark btn-sm w-100 rounded-pill">Jetzt verifizieren</a>
                    <?php endif; ?>
                </div>
            </aside>

            <section class="col-md-8 col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Meine Merkliste</h5>
                    <a href="favorites.php" class="small text-decoration-none">Alle zeigen</a>
                </div>
                <div class="row g-3 mb-5">
                    <?php if (count($favoriteRecipes) > 0): ?>
                        <?php foreach ($favoriteRecipes as $rezept) include "includes/recipe-card.php"; ?>
                    <?php else: ?>
                        <div class="col-12 text-muted small italic">Noch keine Favoriten gespeichert.</div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Eigene Rezepte</h5>
                    <a href="my_recipes.php" class="small text-decoration-none">Verwalten</a>
                </div>
                <div class="row g-3">
                    <?php if (count($ownRecipes) > 0): ?>
                        <?php foreach ($ownRecipes as $rezept) include "includes/recipe-card.php"; ?>
                    <?php else: ?>
                        <div class="col-12 text-muted small italic">Du hast noch keine eigenen Rezepte erstellt.</div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>