<?php
session_start();
require_once "util/dbutil.php";

$queryCount = $conn->query("SELECT COUNT(*) FROM recipes");
$totalAvailableRecipes = $queryCount->fetch_row()[0];

$sqlBase = "SELECT r.recipe_id, r.recipe_name, ri.image_path, u.is_barkeeper 
            FROM recipes r 
            JOIN users u ON r.created_by = u.userid
            LEFT JOIN recipe_images ri ON r.recipe_id = ri.recipe_id 
            GROUP BY r.recipe_id ";

$resPopular = $conn->query($sqlBase . "LIMIT 8");
$resNewest = $conn->query($sqlBase . "ORDER BY r.recipe_id DESC LIMIT 4");
$resRandom = $conn->query($sqlBase . "ORDER BY RAND() LIMIT 3");
?>

<!doctype html>
<html lang="de">

<head>
    <?php include "includes/head-includes.php"; ?>
    <title>MIXD | Home</title>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <header class="container mt-4">
        <div class="hero-header text-white d-flex align-items-center p-5 shadow" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                url('resources/images/ui/banner.webp') no-repeat center center; 
                background-size: cover; 
                min-height: 400px; 
                border-radius: 24px;">

            <div class="w-100 text-center">
                <h1 class="display-3 fw-bold" style="color: #F7F7F7 !important;">Cocktails für jeden Moment.</h1>
                <p class="lead opacity-75">Entdecke <?= $totalAvailableRecipes ?> Rezepte von unserer Community.</p>
                <a href="#popular" class="btn btn-primary btn-lg rounded-pill px-5 mt-3 shadow-sm">Jetzt stöbern</a>
            </div>
        </div>
    </header>

    <main class="container mt-5">

        <div class="row g-3 mb-5 justify-content-center">
            <?php
            $quicklinks = [
                ['label' => 'Klassiker', 'icon' => 'bi-star'],
                ['label' => 'Alkoholfrei', 'icon' => 'bi-cup-straw'],
                ['label' => 'Erfrischend', 'icon' => 'bi-droplet-half'],
                ['label' => 'Sommer', 'icon' => 'bi-sun'],
            ];
            foreach ($quicklinks as $item): ?>
                <div class="col-6 col-md-3">
                    <a href="categories.php?kategorie=<?= urlencode($item['label']) ?>" class="text-decoration-none">
                        <div class="card card-quicklink shadow-sm h-100 p-3 text-center rounded-4">
                            <i class="bi <?= $item['icon'] ?> mb-2 fs-2 text-primary"></i>
                            <span class="fw-bold d-block small text-dark"><?= $item['label'] ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <section id="popular" class="mb-5">
            <h3 class="section-title">Beliebte Rezepte</h3>
            <div class="row g-4">
                <?php while ($rezept = $resPopular->fetch_assoc()):
                    include "includes/recipe-card.php";
                endwhile; ?>
            </div>
        </section>

        <section class="mb-5">
            <h3 class="section-title">Unsere neuesten Rezepte</h3>
            <div class="row g-4">
                <?php while ($rezept = $resNewest->fetch_assoc()):
                    include "includes/recipe-card.php";
                endwhile; ?>
            </div>
        </section>

        <section class="mb-5 pb-5">
            <h3 class="section-title">Zufällige Ideen für dich!</h3>
            <div class="row g-4">
                <?php
                while ($rezept = $resRandom->fetch_assoc()):
                    include "includes/recipe-card.php";
                endwhile;

                $rezept = ['recipe_id' => 'placeholder'];
                include "includes/recipe-card.php";
                ?>
            </div>
        </section>

    </main>

    <?php include "includes/footer.php"; ?>
</body>

</html>