<?php
session_start();
include "util/dbutil.php";

$selectedCategory = "";
if (isset($_GET['kategorie'])) {
    $selectedCategory = $_GET['kategorie'];
}

$foundRecipesList = [];

if ($selectedCategory !== "") {

    $sqlFetchByCategory = "SELECT 
                r.recipe_id, 
                r.recipe_name, 
                ri.image_path, 
                u.is_barkeeper 
            FROM recipes r
            JOIN users u ON r.created_by = u.userid
            JOIN recipe_categories rc ON r.recipe_id = rc.recipe_id
            JOIN categories c ON rc.category_id = c.category_id
            LEFT JOIN recipe_images ri ON r.recipe_id = ri.recipe_id
            WHERE c.category_name = ?
            GROUP BY r.recipe_id";

    $statementLookup = $conn->prepare($sqlFetchByCategory);
    $statementLookup->bind_param("s", $selectedCategory);
    $statementLookup->execute();
    $queryResult = $statementLookup->get_result();

    while ($recipeRow = $queryResult->fetch_assoc()) {
        $foundRecipesList[] = $recipeRow;
    }
    $statementLookup->close();
}

$categoryDescriptions = [
    "Alkoholfrei" => "Leckere Drinks ohne Prozente.",
    "Sommer"      => "Erfrischende Cocktails für heiße Tage.",
    "Klassiker"   => "Die bekanntesten Drinks der Welt.",
    "Erfrischend" => "Perfekt für eine kleine Abkühlung."
];

$displayDescription = "Entdecke unsere Rezepte.";

if (isset($categoryDescriptions[$selectedCategory])) {
    $displayDescription = $categoryDescriptions[$selectedCategory];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Kategorie: <?php echo htmlspecialchars($selectedCategory); ?></title>
</head>
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        
        <div class="text-center mb-5">
            <h2 class="fw-bold">
                <?php if ($selectedCategory !== ""): ?>
                    Kategorie: <span class="text-primary"><?php echo htmlspecialchars($selectedCategory); ?></span>
                <?php else: ?>
                    Alle Kategorien
                <?php endif; ?>
            </h2>
            <p class="text-muted"><?php echo htmlspecialchars($displayDescription); ?></p>
        </div>

        <div class="row g-4">
            <?php if (count($foundRecipesList) > 0): ?>
                
                <?php foreach ($foundRecipesList as $rezept): ?>
                    <?php 
                    $showIngredients = false; 
                    include "includes/recipe-card.php"; 
                    ?>
                <?php endforeach; ?>

            <?php else: ?>
                
                <?php 
                    $emptyIcon = "bi-tsunami"; 
                    $emptyTitle = "Hier ist es noch leer";
                    $emptyText = "In dieser Kategorie wurden leider noch keine Cocktails hochgeladen.";
                    include "includes/empty-state.php";
                ?>

            <?php endif; ?>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>