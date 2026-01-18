<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
} 

$navigationProfilePicture = "resources/images/placeholders/default_profile.png";

if (isset($_SESSION['userid'])) {
    include_once "util/dbutil.php"; 
    $id = $_SESSION['userid']; 

    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE userid = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array(); 
    
    if ($row && !empty($row['profile_image'])) {
        $navigationProfilePicture = $row['profile_image'];
    }
}
?>

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-2">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
            <img src="resources/images/logos/mixdXschatten.png" alt="MIXD Logo" class="navbar-logo">
            <span class="ms-2 navbar-brand-text">MIXD</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Kategorien</a>
                    <ul class="dropdown-menu border-0 shadow-sm mt-2">
                        <li><a class="dropdown-item" href="categories.php?kategorie=Klassiker">Klassiker</a></li>
                        <li><a class="dropdown-item" href="categories.php?kategorie=Erfrischend">Erfrischend</a></li>
                        <li><a class="dropdown-item" href="categories.php?kategorie=Alkoholfrei">Alkoholfrei</a></li>
                        <li><a class="dropdown-item" href="categories.php?kategorie=Sommer">Sommer</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item small text-muted" href="categories.php">Alle Rezepte</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link" href="create_recipe.php">Rezept erstellen</a></li>

                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] >= 2): ?>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-danger" href="admin_dashboard.php">
                            <i class="bi bi-shield-lock"></i> Admin
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center">
                <form class="d-flex me-3" action="search.php" method="GET">
                    <div class="input-group input-group-sm search-group-custom">
                        <input class="form-control search-input-custom" type="search" name="query" placeholder="Drink suchen...">
                        <button class="btn btn-light border-0" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <?php if (isset($_SESSION['userid'])): ?>
                            <img src="<?= htmlspecialchars($navigationProfilePicture) ?>" alt="Profil" 
                                 class="rounded-circle border nav-profile-img">
                            <span class="ms-2 d-none d-sm-inline small fw-bold">
                                <?= htmlspecialchars($_SESSION['user'] ?? 'Profil') ?>
                            </span>
                        <?php else: ?>
                            <div class="rounded-circle border nav-profile-placeholder">
                                <i class="bi bi-person"></i>
                            </div>
                        <?php endif; ?>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-3">
                        <?php if (isset($_SESSION['userid'])): ?>
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="my_recipes.php"><i class="bi bi-journal-text me-2"></i>Meine Rezepte</a></li>
                            <li><a class="dropdown-item" href="favorites.php"><i class="bi bi-heart me-2"></i>Merkliste</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Abmelden</a></li>
                        <?php else: ?>
                            <li class="p-2"><a class="btn btn-primary btn-sm w-100 rounded-pill mb-1" href="login.php">Anmelden</a></li>
                            <li class="p-2"><a class="btn btn-outline-dark btn-sm w-100 rounded-pill" href="register.php">Registrieren</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>