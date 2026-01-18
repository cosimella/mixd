<?php
session_start();
include "util/dbutil.php";
include "util/auth_check.php";

$authenticatedUserId = $_SESSION['userid'];
$errorMessage = "";
$successMessage = "";

$queryUser = $conn->prepare("SELECT benutzername, email, profile_image FROM users WHERE userid = ?");
$queryUser->bind_param("i", $authenticatedUserId);
$queryUser->execute();
$userData = $queryUser->get_result()->fetch_assoc();
$profilePreviewPath = !empty($userData['profile_image']) ? $userData['profile_image'] : "resources/images/placeholders/default_profile.png";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updatedUsername = trim($_POST['benutzername']);
    $updatedEmail    = trim($_POST['email']);
    
    $updateText = $conn->prepare("UPDATE users SET benutzername = ?, email = ? WHERE userid = ?");
    $updateText->bind_param("ssi", $updatedUsername, $updatedEmail, $authenticatedUserId);
    
    if ($updateText->execute()) {
        $successMessage = "Profildaten wurden aktualisiert!";
        $_SESSION['benutzername'] = $updatedUsername;
    }

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $fileExtension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFolder = 'resources/uploads/profiles/';
            $uniqueFileName = "user_" . $authenticatedUserId . "_" . time() . "." . $fileExtension;
            $destination = $uploadFolder . $uniqueFileName;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
              
                $updateImage = $conn->prepare("UPDATE users SET profile_image = ? WHERE userid = ?");
                $updateImage->bind_param("si", $destination, $authenticatedUserId);
                $updateImage->execute();
                
                $profilePreviewPath = $destination;
                $successMessage .= " Bild wurde hochgeladen.";
            } else {
                $errorMessage = "Fehler beim Verschieben der Datei.";
            }
        } else {
            $errorMessage = "Ungültiges Dateiformat. Erlaubt: JPG, PNG, WEBP.";
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
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <h2 class="fw-bold text-center mb-4">Profil bearbeiten</h2>

                    <?php include "includes/messages.php"; ?>

                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="text-center mb-4">
                            <img src="<?php echo $profileImagePreview; ?>" 
                                 class="rounded-circle border shadow-sm mb-3" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                            
                            <div class="px-3">
                                <label class="form-label small fw-bold text-muted">PROFILBILD ÄNDERN</label>
                                <input type="file" name="profile_pic" class="form-control form-control-sm border-0 bg-light">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">BENUTZERNAME</label>
                            <input type="text" name="benutzername" class="form-control border-0 bg-light" 
                                   value="<?php echo htmlspecialchars($userData['benutzername']); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">E-MAIL ADRESSE</label>
                            <input type="email" name="email" class="form-control border-0 bg-light" 
                                   value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill fw-bold">SPEICHERN</button>
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