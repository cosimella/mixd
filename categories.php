<?php
session_start();
require_once "util/dbutil.php";

$selectedCategory = $_GET['kategorie'] ?? "";
$foundRecipes = [];

if ($selectedCategory !== "") { 
    $sql = "SELECT 
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

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selectedCategory);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $foundRecipes[] = $row;
    }
    $stmt->close();
}

$descriptions = [
    "Alkoholfrei" => "Leckere Drinks ohne Prozente.",
    "Sommer"      => "Erfrischende Cocktails für heiße Tage.",
    "Klassiker"   => "Die bekanntesten Drinks der Welt.",
    "Erfrischend" => "Perfekt für eine kleine Abkühlung.",
    "Fruchtig"    => "Süße Früchte treffen auf feine Spirituosen.",
    "Winter"      => "Wärmende Drinks für gemütliche Abende.",
    "Party"       => "Drinks, die auf keiner Feier fehlen dürfen."
];

$displayDescription = $descriptions[$selectedCategory] ?? "Entdecke unsere Rezepte.";
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Kategorie: <?= htmlspecialchars($selectedCategory) ?></title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        
        <div class="text-center mb-5">
            <h2 class="fw-bold">
                <?php if ($selectedCategory !== ""): ?>
                    Kategorie: <span class="text-primary"><?= htmlspecialchars($selectedCategory) ?></span>
                <?php else: ?>
                    Alle Rezepte
                <?php endif; ?>
            </h2>
            <p class="text-muted"><?= htmlspecialchars($displayDescription) ?></p>
        </div>

        <div class="row g-4">
            <?php if (count($foundRecipes) > 0): ?>
                
                <?php foreach ($foundRecipes as $rezept): ?>
                    <?php 
                        $showIngredients = false; 
                        include "includes/recipe-card.php"; 
                    ?>
                <?php endforeach; ?>

            <?php else: ?>
                
                <?php 
                    $displayIcon = "bi-tsunami"; 
                    $displayTitle = "Hier ist es noch leer";
                    $displayText = "In dieser Kategorie wurden leider noch keine Cocktails hochgeladen.";
                    include "includes/empty-state.php";
                ?>

            <?php endif; ?>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>