<?php

session_start();
require_once "util/dbutil.php";
include "util/admin_check.php";

$activeTab = $_GET['tab'] ?? "rezepte";

$totalRecipes = $conn->query("SELECT COUNT(*) FROM recipes")->fetch_row()[0];
$totalUsers   = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];

$sqlPending = "SELECT COUNT(*) FROM barkeeper_applications WHERE status = 'pending'";
$pendingCount = $conn->query($sqlPending)->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Verwaltung - MIXD</title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold">Verwaltung</h2>
            <span class="badge bg-dark rounded-pill px-3 py-2">
                Status: <?= ($_SESSION['user_role'] == 3 ? 'Administrator' : 'Moderator') ?>
            </span>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <a href="admin_dashboard.php?tab=rezepte" 
                   class="btn <?= ($activeTab == 'rezepte' ? 'btn-primary' : 'btn-white shadow-sm') ?> w-100 p-3 rounded-4 border-0">
                    <i class="bi bi-cup-straw d-block fs-3"></i>
                    Rezepte (<?= $totalRecipes ?>)
                </a>
            </div>
            
            <?php if($_SESSION['user_role'] == 3): ?>
            <div class="col-md-4">
                <a href="admin_dashboard.php?tab=users" 
                   class="btn <?= ($activeTab == 'users' ? 'btn-primary' : 'btn-white shadow-sm') ?> w-100 p-3 rounded-4 border-0">
                    <i class="bi bi-people d-block fs-3"></i>
                    Benutzer (<?= $totalUsers ?>)
                </a>
            </div>
            <div class="col-md-4">
                <a href="admin_dashboard.php?tab=verification" 
                   class="btn <?= ($activeTab == 'verification' ? 'btn-primary' : 'btn-white shadow-sm') ?> w-100 p-3 rounded-4 border-0 position-relative">
                    <i class="bi bi-patch-check d-block fs-3"></i>
                    Anträge
                    <?php if($pendingCount > 0): ?>
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                            <?= $pendingCount ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($activeTab == 'rezepte'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4">Rezepte moderieren</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-muted small">
                            <th>NAME</th>
                            <th>AUTOR</th>
                            <th class="text-end">AKTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sqlMod = "SELECT r.recipe_id, r.recipe_name, u.benutzername 
                                   FROM recipes r 
                                   JOIN users u ON r.created_by = u.userid 
                                   ORDER BY r.recipe_id DESC";
                        $resMod = $conn->query($sqlMod);
                        while($row = $resMod->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($row['recipe_name']) ?></td>
                            <td class="text-muted small">@<?= htmlspecialchars($row['benutzername']) ?></td>
                            <td class="text-end">
                                <a href="recipe.php?id=<?= $row['recipe_id'] ?>" class="btn btn-sm btn-light border" target="_blank">View</a>
                                <a href="util/admin_actions.php?action=delete_recipe&id=<?= $row['recipe_id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Wirklich löschen?')">Löschen</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($activeTab == 'users' && $_SESSION['user_role'] == 3): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4">Mitglieder verwalten</h5>
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>BENUTZERNAME</th>
                        <th>ROLLE</th>
                        <th class="text-end">AKTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $resUser = $conn->query("SELECT userid, benutzername, role FROM users ORDER BY role DESC");
                    while($user = $resUser->fetch_assoc()): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($user['benutzername']) ?></td>
                        <td>
                            <span class="badge <?= ($user['role'] >= 2 ? 'bg-primary' : 'bg-light text-dark') ?>">
                                <?= ($user['role'] == 3 ? 'Admin' : ($user['role'] == 2 ? 'Mod' : 'User')) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <?php if($user['userid'] != $_SESSION['userid']): ?>
                                <a href="util/admin_actions.php?action=delete_user&id=<?= $user['userid'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Benutzer unwiderruflich löschen?')">Löschen</a>
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