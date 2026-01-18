<?php
session_start();
require_once "util/dbutil.php";

$searchQuery = $_GET['query'] ?? "";
$searchResults = [];

if ($searchQuery !== "") {

    $searchPattern = "%" . $searchQuery . "%";
    
    
    $sql = "SELECT 
                r.recipe_id, 
                r.recipe_name, 
                ri.image_path,
                u.is_barkeeper,
                AVG(rat.stars) as avg_rating
            FROM recipes r
            LEFT JOIN recipe_images ri ON r.recipe_id = ri.recipe_id
            LEFT JOIN recipe_ingredients rin ON r.recipe_id = rin.recipe_id
            LEFT JOIN ingredients i ON rin.ingredient_id = i.ingredient_id
            LEFT JOIN users u ON r.created_by = u.userid
            LEFT JOIN ratings rat ON r.recipe_id = rat.recipe_id
            WHERE r.recipe_name LIKE ?
            OR i.ingredient_name LIKE ?
            GROUP BY r.recipe_id
            ORDER BY r.recipe_name ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchPattern, $searchPattern);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $searchResults[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Suche: <?= htmlspecialchars($searchQuery) ?></title>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5 flex-grow-1">
        <div class="mb-5">
            <?php if ($searchQuery !== ""): ?>
                <h2 class="fw-bold">Ergebnisse für "<?= htmlspecialchars($searchQuery) ?>"</h2>
                <p class="text-muted"><?= count($searchResults) ?> Drinks gefunden</p>
            <?php else: ?>
                <h2 class="fw-bold">Suche</h2>
                <p class="text-muted">Gib einen Namen oder eine Zutat ein.</p>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <?php if (count($searchResults) > 0): ?>
                <?php foreach ($searchResults as $rezept): ?> 
                    <?php
                    $showIngredients = false;
                    $showControls = false; 
                    include "includes/recipe-card.php";
                    ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="card shadow-sm border-0 p-5 rounded-4">
                        <i class="bi bi-search text-muted mb-3"></i>
                        <h4 class="fw-bold">Leider nichts gefunden</h4>
                        <p class="text-muted">Probiere es mal mit "Gin" oder "Limetten".</p>
                        <div class="mt-3">
                            <a href="index.php" class="btn btn-primary rounded-pill px-4">Zurück zur Startseite</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>