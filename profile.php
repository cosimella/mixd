<?php
session_start();
include "util/dbutil.php";
include "util/auth_check.php";

$currentUserId = $_SESSION['userid'];

$queryUserProfile = "SELECT benutzername, email, profile_image, is_barkeeper FROM users WHERE userid = $currentUserId";
$userResult = $conn->query($queryUserProfile);
$userData = $userResult->fetch_assoc();

$queryApplicationStatus = "SELECT status FROM barkeeper_applications WHERE userid = $currentUserId ORDER BY created_at DESC LIMIT 1";
$applicationResult = $conn->query($queryApplicationStatus);
$verificationStatus = ($applicationResult->num_rows > 0) ? $applicationResult->fetch_assoc()['status'] : null;

$sqlRecipeBase = "SELECT r.recipe_id, r.recipe_name, ri.image_path, u.is_barkeeper 
                  FROM recipes r 
                  JOIN users u ON r.created_by = u.userid 
                  LEFT JOIN recipe_images ri ON r.recipe_id = ri.recipe_id ";

$favoriteRecipes = $conn->query($sqlRecipeBase . "JOIN favorites f ON r.recipe_id = f.recipe_id WHERE f.user_id = $currentUserId GROUP BY r.recipe_id LIMIT 4")->fetch_all(MYSQLI_ASSOC);

$ownRecipes = $conn->query($sqlRecipeBase . "WHERE r.created_by = $currentUserId GROUP BY r.recipe_id LIMIT 4")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Profil - MIXD</title>
</head>
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="row">
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 text-center mb-4">
                    <img src="<?php echo $userData['profile_image'] ?: 'resources/images/placeholders/default_profile.png'; ?>" 
                         class="rounded-circle mb-3 mx-auto shadow-sm" style="width: 110px; height: 110px; object-fit: cover;">
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($userData['benutzername']); ?></h5>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($userData['email']); ?></p>
                    <a href="edit_profile.php" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">Profil bearbeiten</a>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <h6 class="fw-bold mb-3">Verifizierung</h6>
                    <?php if ($userData['is_barkeeper'] == 1): ?>
                        <div class="text-success small fw-bold mb-2">
                            <i class="bi bi-patch-check-fill"></i> Status: Barkeeper Profi
                        </div>
                        <p class="x-small text-muted mb-0">Dein Profil ist verifiziert.</p>
                    <?php elseif ($verificationStatus == 'pending'): ?>
                        <div class="text-warning small fw-bold mb-2">
                            <i class="bi bi-hourglass-split"></i> Status: In Prüfung
                        </div>
                        <p class="x-small text-muted mb-0">Wir prüfen dein Zertifikat gerade.</p>
                    <?php else: ?>
                        <p class="x-small text-muted mb-3">Werde verifizierter Barkeeper für exklusive Vorteile.</p>
                        <a href="apply_barkeeper.php" class="btn btn-dark btn-sm w-100 rounded-pill">Jetzt verifizieren</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-8 col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Meine Merkliste</h5>
                    <a href="favorites.php" class="small text-decoration-none">Alle zeigen</a>
                </div>
                <div class="row g-3 mb-5">
                    <?php 
                    foreach ($favoriteRecipes as $rezept) {
                        include "includes/recipe-card.php"; 
                    }
                    ?>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Eigene Rezepte</h5>
                    <a href="my_recipes.php" class="small text-decoration-none">Verwalten</a>
                </div>
                <div class="row g-3">
                    <?php 
                    foreach ($ownRecipes as $rezept) {
                        include "includes/recipe-card.php"; 
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
    <?php include "includes/footer.php"; ?>
</body>
</html>