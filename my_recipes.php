<?php
session_start();
include "util/dbutil.php";
include "util/auth_check.php";

$currentUserId = $_SESSION['userid'];

// SQL: Jetzt mit Durchschnittsbewertung (AVG), damit die Karte Sterne zeigt
$sqlOwnRecipes = "SELECT 
                    r.recipe_id, 
                    r.recipe_name, 
                    ri.image_path, 
                    u.is_barkeeper,
                    AVG(ra.stars) as avg_rating
                  FROM recipes r
                  JOIN users u ON r.created_by = u.userid
                  LEFT JOIN recipe_images ri ON r.recipe_id = ri.recipe_id
                  LEFT JOIN ratings ra ON r.recipe_id = ra.recipe_id
                  WHERE r.created_by = ?
                  GROUP BY r.recipe_id
                  ORDER BY r.recipe_id DESC";

$statementOwnRecipes = $conn->prepare($sqlOwnRecipes);
$statementOwnRecipes->bind_param("i", $currentUserId);
$statementOwnRecipes->execute();
$queryResponse = $statementOwnRecipes->get_result();

$userRecipesList = [];
while ($recipeRow = $queryResponse->fetch_assoc()) {
    $userRecipesList[] = $recipeRow;
}
$statementOwnRecipes->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Meine Rezepte - MIXD</title>
</head>
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'deleted'): ?>
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill me-2"></i> Rezept erfolgreich gelöscht.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Meine Rezepte</h2>
                <p class="text-muted mb-0">Du hast bisher <strong><?php echo count($userRecipesList); ?></strong> Rezepte erstellt.</p>
            </div>
            <a href="create_recipe.php" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-plus-lg me-2"></i> NEUES REZEPT
            </a>
        </div>

        <div class="row g-4">
            <?php if (count($userRecipesList) > 0): ?>
                <?php foreach ($userRecipesList as $rezept): ?>
                    <?php 
                        $showControls = true; 
                        include "includes/recipe-card.php"; 
                    ?>
                <?php endforeach; ?>
            <?php else: ?>
                <?php 
                    $displayIcon = "bi-cup-hot"; 
                    $displayTitle = "Noch keine eigenen Rezepte";
                    $displayText = "Du hast bisher noch keine Cocktails hochgeladen. Fang jetzt damit an!";
                    include "includes/empty-state.php";
                ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>