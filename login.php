<?php
session_start();
require_once "util/dbutil.php"; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$rememberedEmail = $_COOKIE['remember_user'] ?? "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emailInput    = trim($_POST['email']);
    $passwordInput = $_POST['password'];
    $rememberMe    = isset($_POST['remember_me']);

    
    $query = "SELECT userid, benutzername, passwort, profile_image, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $emailInput);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();

    if ($userData && password_verify($passwordInput, $userData['passwort'])) {
        
        if ($rememberMe) {
            setcookie("remember_user", $emailInput, time() + (30 * 24 * 60 * 60), "/");
        } else {
            setcookie("remember_user", "", time() - 3600, "/");
        }
        
        $_SESSION['userid']    = $userData['userid']; 
        $_SESSION['user']      = $userData['benutzername'];
        $_SESSION['user_role'] = $userData['role']; 
        $_SESSION['user_pfp']  = !empty($userData['profile_image']) ? $userData['profile_image'] : 'resources/images/placeholders/default_profile.png';

        header("Location: profile.php");
        exit;
    } else {
        $errorMessage = "E-Mail oder Passwort ist leider falsch.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Anmelden | MIXD</title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5 flex-grow-1">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5 col-lg-4">
                
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Login</h2>
                        <p class="text-muted small">Schön, dass du wieder da bist!</p>
                    </div>

                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger small rounded-3"><?= htmlspecialchars($errorMessage) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">E-Mail</label>
                            <input type="email" name="email" class="form-control border-0 bg-light" 
                                   value="<?= htmlspecialchars($rememberedEmail) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Passwort</label>
                            <input type="password" name="password" class="form-control border-0 bg-light" required>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" name="remember_me" class="form-check-input" id="remember" 
                                   <?= !empty($rememberedEmail) ? 'checked' : '' ?>>
                            <label class="form-check-label small text-muted" for="remember">E-Mail merken</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                            EINLOGGEN
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="small text-muted">
                            Noch kein Konto? <br>
                            <a href="register.php" class="text-primary fw-bold text-decoration-none">Jetzt registrieren</a>
                        </p>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="index.php" class="text-muted small text-decoration-none">
                        <i class="bi bi-house"></i> Zurück zur Startseite
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>