<?php
session_start();

include "util/dbutil.php";
include "util/admin_check.php";

$activeTab = "rezepte";
if (isset($_GET['tab'])) {
    $activeTab = $_GET['tab'];
}

$queryRecipeCount = $conn->query("SELECT COUNT(*) FROM recipes");
$totalRecipes = $queryRecipeCount->fetch_row()[0];

$queryUserCount = $conn->query("SELECT COUNT(*) FROM users");
$totalUsers = $queryUserCount->fetch_row()[0];

$queryPendingApps = $conn->query("SELECT COUNT(*) FROM barkeeper_applications WHERE status = 'pending'");
$pendingApplicationsCount = $queryPendingApps->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Verwaltung - MIXD</title>
</head>
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold">Verwaltung</h2>
            <span class="badge bg-dark rounded-pill">
                Status: <?php echo ($_SESSION['user_role'] == 3 ? 'Administrator' : 'Moderator'); ?>
            </span>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <a href="admin_dashboard.php?tab=rezepte" class="btn <?php echo ($activeTab == 'rezepte' ? 'btn-primary' : 'btn-white shadow-sm'); ?> w-100 p-3 rounded-4 border-0">
                    <i class="bi bi-cup-straw d-block fs-3"></i>
                    Rezepte (<?php echo $totalRecipes; ?>)
                </a>
            </div>
            
            <?php if($_SESSION['user_role'] == 3):?> 
            <div class="col-md-4">
                <a href="admin_dashboard.php?tab=users" class="btn <?php echo ($activeTab == 'users' ? 'btn-primary' : 'btn-white shadow-sm'); ?> w-100 p-3 rounded-4 border-0">
                    <i class="bi bi-people d-block fs-3"></i>
                    Benutzer (<?php echo $totalUsers; ?>)
                </a>
            </div>
            <div class="col-md-4">
                <a href="admin_dashboard.php?tab=verification" class="btn <?php echo ($activeTab == 'verification' ? 'btn-primary' : 'btn-white shadow-sm'); ?> w-100 p-3 rounded-4 border-0 position-relative">
                    <i class="bi bi-patch-check d-block fs-3"></i>
                    Anträge
                    <?php if($pendingApplicationsCount > 0): ?>
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"><?php echo $pendingApplicationsCount; ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($activeTab == 'rezepte'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4">Rezepte moderieren</h5>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Von</th>
                        <th class="text-end">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sqlModeration = "SELECT r.recipe_id, r.recipe_name, u.benutzername 
                                      FROM recipes r 
                                      JOIN users u ON r.created_by = u.userid 
                                      ORDER BY r.recipe_id DESC";
                    $moderationResult = $conn->query($sqlModeration);
                    while($recipeRow = $moderationResult->fetch_assoc()): ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($recipeRow['recipe_name']); ?></td>
                        <td class="small text-muted">@<?php echo htmlspecialchars($recipeRow['benutzername']); ?></td>
                        <td class="text-end">
                            <a href="recipe.php?id=<?php echo $recipeRow['recipe_id']; ?>" class="btn btn-sm btn-light border" target="_blank">Ansehen</a>
                            <a href="util/admin_actions.php?action=delete_recipe&id=<?php echo $recipeRow['recipe_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Dieses Rezept unwiderruflich löschen?')">Löschen</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if ($activeTab == 'users' && $_SESSION['user_role'] == 3): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4">Mitglieder verwalten</h5>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Rolle</th>
                        <th class="text-end">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $userListResult = $conn->query("SELECT userid, benutzername, role FROM users ORDER BY role DESC");
                    while($userRow = $userListResult->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($userRow['benutzername']); ?></strong></td>
                        <td>
                            <span class="badge <?php echo ($userRow['role'] >= 2 ? 'bg-primary' : 'bg-light text-dark'); ?>">
                                <?php echo ($userRow['role'] == 3 ? 'Admin' : ($userRow['role'] == 2 ? 'Mod' : 'User')); ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <?php if($userRow['userid'] != $_SESSION['userid']): ?>
                                <a href="util/admin_actions.php?action=delete_user&id=<?php echo $userRow['userid']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Benutzer wirklich löschen?')">Löschen</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>