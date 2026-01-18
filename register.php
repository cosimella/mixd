<?php
session_start();
include "util/dbutil.php"; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $usernameInput = trim($_POST['username']);
    $emailInput    = trim($_POST['email']);
    $passwordRaw   = $_POST['password'];
    $passwordRepeat = $_POST['password_confirm'];

    if (empty($usernameInput) || empty($emailInput) || empty($passwordRaw)) {
        $errorMessage = "Bitte fülle alle Felder aus!";
    } 
    elseif (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Die E-Mail Adresse ist ungültig!";
    }
    elseif ($passwordRaw !== $passwordRepeat) {
        $errorMessage = "Die Passwörter stimmen nicht überein!";
    } 
    elseif (strlen($passwordRaw) < 8) {
        $errorMessage = "Das Passwort muss mindestens 8 Zeichen haben!";
    } 
    else {
        
        $queryCheckAvailability = "SELECT userid FROM users WHERE benutzername = ? OR email = ? LIMIT 1";
        $statementUserLookup = $conn->prepare($queryCheckAvailability);
        $statementUserLookup->bind_param("ss", $usernameInput, $emailInput);
        $statementUserLookup->execute();
        $lookupResult = $statementUserLookup->get_result();

        if ($lookupResult->num_rows > 0) {
            $errorMessage = "Benutzername oder E-Mail wird bereits verwendet!";
        } else {
           
            $hashedPassword = password_hash($passwordRaw, PASSWORD_DEFAULT);
            $defaultProfileImage = "resources/images/placeholders/default_profile.png";
            $queryInsertUser = "INSERT INTO users (benutzername, email, passwort, role, profile_image) VALUES (?, ?, ?, 1, ?)";
            $statementExecution = $conn->prepare($queryInsertUser);
            $statementExecution->bind_param("ssss", $usernameInput, $emailInput, $hashedPassword, $defaultProfileImage);
            
            if ($statementExecution->execute()) {
                $successMessage = "Konto erfolgreich erstellt! Du kannst dich jetzt einloggen.";
            } else {
                $errorMessage = "Ein Datenbankfehler ist aufgetreten: " . $conn->error;
            }
            $statementExecution->close();
        }
        $statementUserLookup->close();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Registrieren | MIXD</title>
</head>
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Konto erstellen</h2>
                    </div>

                    <?php include "includes/messages.php"; ?>

                    <form method="POST" action="register.php">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">BENUTZERNAME</label>
                            <input type="text" name="username" class="form-control" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">E-MAIL ADRESSE</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">PASSWORT</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">BESTÄTIGEN</label>
                                <input type="password" name="password_confirm" class="form-control" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                            REGISTRIEREN
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="small text-muted">Bereits ein Konto? <a href="login.php" class="text-decoration-none fw-bold">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>