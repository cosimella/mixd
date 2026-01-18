<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once "util/dbutil.php";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $usernameInput = trim($_POST['username']);
    $emailInput    = trim($_POST['email']);
    $passwordRaw   = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];


    if (empty($usernameInput) || empty($emailInput) || empty($passwordRaw)) {
        $errorMessage = "Bitte fülle alle Felder aus!";
    } 
    elseif (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Die E-Mail Adresse ist ungültig!";
    }
    elseif ($passwordRaw !== $passwordConfirm) {
        $errorMessage = "Die Passwörter stimmen nicht überein!";
    } 
    elseif (strlen($passwordRaw) < 8) {
        $errorMessage = "Sicherheitshinweis: Das Passwort muss mindestens 8 Zeichen haben!";
    } 
    else {
        $sqlCheck = "SELECT userid FROM users WHERE benutzername = ? OR email = ? LIMIT 1";
        $stmtLookup = $conn->prepare($sqlCheck);
        $stmtLookup->bind_param("ss", $usernameInput, $emailInput);
        $stmtLookup->execute();
        
        if ($stmtLookup->get_result()->num_rows > 0) {
            $errorMessage = "Dieser Benutzername oder diese E-Mail wird bereits verwendet!";
        } else {

            $hashedPassword = password_hash($passwordRaw, PASSWORD_DEFAULT);
            $defaultPfp = "resources/images/placeholders/default_profile.png";

            // Rolle 1 = Standard User
            $sqlInsert = "INSERT INTO users (benutzername, email, passwort, role, profile_image) VALUES (?, ?, ?, 1, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ssss", $usernameInput, $emailInput, $hashedPassword, $defaultPfp);
            
            if ($stmtInsert->execute()) {
                $successMessage = "Konto erfolgreich erstellt! Du kannst dich jetzt einloggen.";
            } else {
                $errorMessage = "Datenbankfehler: Die Registrierung konnte nicht abgeschlossen werden.";
            }
            $stmtInsert->close();
        }
        $stmtLookup->close();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Registrieren | MIXD</title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5 flex-grow-1">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Konto erstellen</h2>
                        <p class="text-muted small">Werde Teil der MIXD Community.</p>
                    </div>

                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger small rounded-3"><?= htmlspecialchars($errorMessage) ?></div>
                    <?php endif; ?>
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success small rounded-3"><?= htmlspecialchars($successMessage) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Benutzername</label>
                            <input type="text" name="username" class="form-control border-0 bg-light" 
                                   value="<?= htmlspecialchars($usernameInput ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">E-Mail Adresse</label>
                            <input type="email" name="email" class="form-control border-0 bg-light" 
                                   value="<?= htmlspecialchars($emailInput ?? '') ?>" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Passwort</label>
                                <input type="password" name="password" class="form-control border-0 bg-light" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Bestätigen</label>
                                <input type="password" name="password_confirm" class="form-control border-0 bg-light" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm mt-3">
                            JETZT REGISTRIEREN
                        </button>
                    </form>
                    
                    <div class="text-center mt-4 border-top pt-3">
                        <p class="small text-muted">Bereits ein Konto? <a href="login.php" class="text-decoration-none fw-bold">Hier einloggen</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>