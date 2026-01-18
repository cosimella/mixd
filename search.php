<?php
session_start();
include "util/dbutil.php";

$searchQuery = "";

// Prüfen, ob ein Suchbegriff über die URL (GET) übergeben wurde
if (isset($_GET['query'])) {
    $searchQuery = trim($_GET['query']);
}

$searchResults = [];

if ($searchQuery !== "") {
    // Suchmuster für die SQL-LIKE-Abfrage vorbereiten
    $searchPattern = "%" . $searchQuery . "%";
    
    // SQL angepasst: Wir laden auch die Durchschnittsbewertung für die Sterne-Anzeige
    $sqlSearchRecipes = "SELECT 
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

    $statementSearch = $conn->prepare($sqlSearchRecipes);
    $statementSearch->bind_param("ss", $searchPattern, $searchPattern);
    $statementSearch->execute();
    $queryResponse = $statementSearch->get_result();

    while ($recipeRow = $queryResponse->fetch_assoc()) {
        $searchResults[] = $recipeRow;
    }

    $statementSearch->close();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Suche: <?php echo htmlspecialchars($searchQuery); ?></title>
</head>

<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="mb-5">
            <?php if ($searchQuery !== ""): ?>
                <h2 class="fw-bold">Ergebnisse für "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
                <p class="text-muted"><?php echo count($searchResults); ?> Cocktails gefunden</p>
            <?php else: ?>
                <h2 class="fw-bold">Suche</h2>
                <p class="text-muted">Gib einen Namen oder eine Zutat ein.</p>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <?php if (count($searchResults) > 0): ?>
                <?php foreach ($searchResults as $rezept): ?> <?php
                    $showIngredients = false;
                    $showControls = false; // In der Suche keine Bearbeiten-Buttons zeigen
                    include "includes/recipe-card.php";
                    ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="card shadow-sm border-0 p-5 rounded-4">
                        <i class="bi bi-search text-muted mb-3" style="font-size: 3rem;"></i>
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