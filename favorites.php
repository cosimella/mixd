<?php
session_start();
// Zentrale Datenbankverbindung einbinden
include "util/dbutil.php"; 

include "util/auth_check.php";

$currentUserId = $_SESSION['userid'];

// 2. Favoriten laden (Verknüpfung von Favoriten-Tabelle, Rezepten und Bildern)
$queryFavoriteRecipes = "SELECT r.recipe_id, r.recipe_name, ri.image_path 
        FROM favorites f
        JOIN recipes r ON f.recipe_id = r.recipe_id
        LEFT JOIN recipe_images ri ON r.recipe_id = ri.recipe_id
        WHERE f.user_id = ? 
        GROUP BY r.recipe_id
        ORDER BY f.created_at DESC";

$statementFavorites = $conn->prepare($queryFavoriteRecipes);
$favoriteRecipesList = [];

if ($statementFavorites) {
    $statementFavorites->bind_param("i", $currentUserId);
    $statementFavorites->execute();
    $queryResult = $statementFavorites->get_result();
    
    // Daten zeilenweise in unsere Liste übertragen
    while ($recipeRow = $queryResult->fetch_assoc()) {
        $favoriteRecipesList[] = $recipeRow;
    }
    $statementFavorites->close();
}
?>

<!doctype html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Meine Favoriten - MIXD</title>
</head>
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-0">Meine Merkliste</h2>
                <p class="text-muted">Hier findest du deine gespeicherten Drinks.</p>
            </div>
            <span class="badge bg-danger rounded-pill px-3 shadow-sm">
                <?php echo count($favoriteRecipesList); ?> gespeichert
            </span>
        </div>

        <div class="row g-4">
            <?php if (count($favoriteRecipesList) > 0): ?>
                
                <?php 
                $showIngredients = false; 
                // Wir nutzen $rezept als Variable, da die recipe-card.php diesen Namen erwartet
                foreach ($favoriteRecipesList as $rezept) {
                    include "includes/recipe-card.php"; 
                } 
                ?>

            <?php else: ?>
                
                <?php 
                    $emptyIcon = "bi-heart";
                    $emptyTitle = "Deine Liste ist noch leer";
                    $emptyText = "Du hast noch keine Drinks mit einem Herz markiert. Schau dir doch mal die neuesten Rezepte an!";
                    include "includes/empty-state.php";
                ?>

            <?php endif; ?>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>