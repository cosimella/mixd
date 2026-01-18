<?php
session_start();
include "util/dbutil.php"; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$errorMessage = "";

$rememberedEmail = isset($_COOKIE['remember_user']) ? $_COOKIE['remember_user'] : "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emailInput    = trim($_POST['email']);
    $passwordInput = $_POST['password'];
    $rememberMe    = isset($_POST['remember_me']);

    $queryFindUser = "SELECT userid, benutzername, passwort, profile_image, role FROM users WHERE email = ?";
    $statementLookup = $conn->prepare($queryFindUser);
    $statementLookup->bind_param("s", $emailInput);
    $statementLookup->execute();
    $userData = $statementLookup->get_result()->fetch_assoc();

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
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Login</h2>
                        <p class="text-muted small">Schön, dass du wieder da bist!</p>
                    </div>

                    <?php include "includes/messages.php"; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">E-MAIL</label>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="Deine E-Mail" 
                                   value="<?php echo htmlspecialchars($rememberedEmail); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">PASSWORT</label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Dein Passwort" required>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" name="remember_me" class="form-check-input" id="remember" 
                                   <?php echo !empty($rememberedEmail) ? 'checked' : ''; ?>>
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