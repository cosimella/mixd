<?php
session_start();
require_once "util/dbutil.php";
include "util/auth_check.php";

$idUser = $_SESSION['userid'];
$msgError = "";
$msgSuccess = "";

$sqlUser = "SELECT benutzername, email, profile_image FROM users WHERE userid = ?";
$stmt = $conn->prepare($sqlUser);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$imgPreview = !empty($user['profile_image']) ? $user['profile_image'] : "resources/images/placeholders/default_profile.png";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newUsername = trim($_POST['benutzername']);
    $newEmail    = trim($_POST['email']);
    
    $sqlUpdText = "UPDATE users SET benutzername = ?, email = ? WHERE userid = ?";
    $stmtUpd = $conn->prepare($sqlUpdText);
    $stmtUpd->bind_param("ssi", $newUsername, $newEmail, $idUser);
    
    if ($stmtUpd->execute()) {
        $msgSuccess = "Profildaten wurden aktualisiert!";
        $_SESSION['benutzername'] = $newUsername; 
    }

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $uploadDir = 'resources/uploads/profiles/';
            $fileName = "user_" . $idUser . "_" . time() . "." . $ext;
            $destination = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
                $sqlUpdImg = "UPDATE users SET profile_image = ? WHERE userid = ?";
                $stmtImg = $conn->prepare($sqlUpdImg);
                $stmtImg->bind_param("si", $destination, $idUser);
                $stmtImg->execute();
                
                $imgPreview = $destination;
                $msgSuccess .= " Bild wurde hochgeladen.";
            } else {
                $msgError = "Dateisystem-Fehler beim Verschieben.";
            }
        } else {
            $msgError = "Ungültiges Format. Erlaubt: " . implode(", ", $allowed);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Profil bearbeiten - MIXD</title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <h2 class="fw-bold text-center mb-4">Profil bearbeiten</h2>

                    <?php if ($msgError): ?>
                        <div class="alert alert-danger small rounded-3"><?= htmlspecialchars($msgError) ?></div>
                    <?php endif; ?>
                    <?php if ($msgSuccess): ?>
                        <div class="alert alert-success small rounded-3"><?= htmlspecialchars($msgSuccess) ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="text-center mb-4">
                            <img src="<?= $imgPreview ?>" 
                                 class="rounded-circle border shadow-sm mb-3" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                            
                            <div class="px-3">
                                <label class="small fw-bold text-muted text-uppercase d-block mb-2">Profilbild ändern</label>
                                <input type="file" name="profile_pic" class="form-control form-control-sm border-0 bg-light">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted text-uppercase">Benutzername</label>
                            <input type="text" name="benutzername" class="form-control border-0 bg-light" 
                                   value="<?= htmlspecialchars($user['benutzername']) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-muted text-uppercase">E-Mail Adresse</label>
                            <input type="email" name="email" class="form-control border-0 bg-light" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill fw-bold py-2 shadow">SPEICHERN</button>
                            <a href="profile.php" class="btn btn-link text-muted text-decoration-none small">Abbrechen</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>