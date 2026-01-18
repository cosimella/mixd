<?php
session_start();
require_once "util/dbutil.php"; 
include "util/auth_check.php";

$currentUserId = $_SESSION['userid'];

$query = "SELECT r.recipe_id, r.recipe_name, ri.image_path 
          FROM favorites f
          JOIN recipes r ON f.recipe_id = r.recipe_id
          LEFT JOIN recipe_images ri ON r.recipe_id = ri.recipe_id
          WHERE f.user_id = ? 
          GROUP BY r.recipe_id
          ORDER BY f.created_at DESC";

$stmt = $conn->prepare($query);
$favorites = [];

if ($stmt) {
    $stmt->execute();
    $res = $stmt->get_result();
    
    while ($row = $res->fetch_assoc()) {
        $favorites[] = $row;
    }
    $stmt->close();
}
?>

<!doctype html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Meine Favoriten - MIXD</title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-0">Meine Merkliste</h2>
                <p class="text-muted">Deine persönlichen Favoriten auf einen Blick.</p>
            </div>
            <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
                <?= count($favorites) ?> gespeichert
            </span>
        </div>

        <div class="row g-4">
            <?php if (count($favorites) > 0): ?>
                
                <?php 
                $showIngredients = false; 
                foreach ($favorites as $rezept):
                    include "includes/recipe-card.php"; 
                endforeach; 
                ?>

            <?php else: ?>
                
                <?php 
                    $displayIcon = "bi-heart";
                    $displayTitle = "Deine Liste ist noch leer";
                    $displayText = "Markiere Drinks mit einem Herz, um sie hier später schnell wiederzufinden.";
                    include "includes/empty-state.php";
                ?>

            <?php endif; ?>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>