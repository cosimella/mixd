<?php
session_start();
include "util/dbutil.php";

$queryCountRecipes = $conn->query("SELECT COUNT(*) FROM recipes");
$countData = $queryCountRecipes->fetch_row();
$totalAvailableRecipes = $countData[0];

$sqlRecipeBaseTemplate = "SELECT r.recipe_id, r.recipe_name, ri.image_path, u.is_barkeeper 
                          FROM recipes r 
                          JOIN users u ON r.created_by = u.userid
                          LEFT JOIN recipe_images ri ON r.recipe_id = ri.recipe_id 
                          GROUP BY r.recipe_id ";

$resultPopularFeed = $conn->query($sqlRecipeBaseTemplate . "LIMIT 8");
$resultNewestFeed  = $conn->query($sqlRecipeBaseTemplate . "ORDER BY r.recipe_id DESC LIMIT 4");
$resultRandomDiscovery = $conn->query($sqlRecipeBaseTemplate . "ORDER BY RAND() LIMIT 3");
?>

<!doctype html>
<html lang="de">

<head>
    <?php include "includes/head-includes.php"; ?>
    <title>MIXD | Home</title>
</head>

<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <section class="hero-header text-white d-flex align-items-center mb-5" 
             style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('resources/images/ui/banner.webp') no-repeat center center; 
                    background-size: cover; 
                    min-height: 400px; 
                    border-radius: 20px; 
                    overflow: hidden;">
        
        <div class="container text-center">
            <h1 class="display-4 fw-bold text-white">Cocktails für jeden Moment.</h1>
            <p class="lead">Entdecke <?php echo $totalAvailableRecipes; ?> Rezepte von unserer Community.</p>
        </div>
    </section>

    <main class="container">

        <div class="row g-3 mb-5 mt-n5 justify-content-center">
            <?php
            $quicklinkConfig = [
                ['label' => 'Klassiker', 'icon' => 'bi-star'],
                ['label' => 'Alkoholfrei', 'icon' => 'bi-cup-straw'],
                ['label' => 'Erfrischend', 'icon' => 'bi-droplet-half'],
                ['label' => 'Sommer', 'icon' => 'bi-sun'],
            ];
            foreach ($quicklinkConfig as $linkItem): ?>
                <div class="col-6 col-md-3 text-center">
                    <a href="categories.php?kategorie=<?php echo urlencode($linkItem['label']); ?>" class="text-decoration-none">
                        <div class="cat-card shadow-sm h-100 p-3 bg-white rounded-4">
                            <i class="bi <?php echo $linkItem['icon']; ?> mb-2 fs-2 text-primary"></i>
                            <span class="fw-bold d-block small text-dark"><?php echo $linkItem['label']; ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mb-5">
            <h3 class="fw-bold mb-4 border-bottom border-primary pb-2 text-uppercase" style="font-size: 1.1rem; letter-spacing: 1px;">Beliebte Rezepte</h3>
            <div class="row g-4">
                <?php while ($rezept = $resultPopularFeed->fetch_assoc()):
                    include "includes/recipe-card.php"; endwhile; ?>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="fw-bold mb-4 border-bottom border-primary pb-2 text-uppercase" style="font-size: 1.1rem; letter-spacing: 1px;">Frisch gemixt</h3>
            <div class="row g-4">
                <?php while ($rezept = $resultNewestFeed->fetch_assoc()):
                    include "includes/recipe-card.php"; endwhile; ?>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="fw-bold mb-4 border-bottom border-primary pb-2 text-uppercase" style="font-size: 1.1rem; letter-spacing: 1px;">Lust auf ein Abenteuer?</h3>
            <div class="row g-4">
                <?php while ($rezept = $resultRandomDiscovery->fetch_assoc()):
                    include "includes/recipe-card.php"; endwhile; ?>

                <?php
                $rezept = ['recipe_id' => 'placeholder', 'recipe_name' => 'Dein Rezept fehlt hier!'];
                include "includes/recipe-card.php";
                ?>
            </div>
        </div>

    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>