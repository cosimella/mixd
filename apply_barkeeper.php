<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once "util/dbutil.php";
include "util/auth_check.php"; 

$idUser = $_SESSION['userid'];
$msgError = "";

$sqlCheck = "SELECT status FROM barkeeper_applications WHERE userid = ? AND status = 'pending'";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $idUser);
$stmtCheck->execute();
if ($stmtCheck->get_result()->num_rows > 0) {
    header("Location: profile.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);

    if (isset($_FILES['doc']) && $_FILES['doc']['error'] === 0) {
        $uploadDir = 'resources/uploads/verification';
        
        $ext = strtolower(pathinfo($_FILES['doc']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($ext, $allowed)) {
            $fileName = "verify_" . $idUser . "_" . time() . "." . $ext;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['doc']['tmp_name'], $targetPath)) {
                
                $sqlIns = "INSERT INTO barkeeper_applications (userid, full_name, document_path, status) VALUES (?, ?, ?, 'pending')";
                $stmtIns = $conn->prepare($sqlIns);
                $stmtIns->bind_param("iss", $idUser, $fullName, $targetPath);
                
                if ($stmtIns->execute()) {
                    header("Location: profile.php?msg=applied");
                    exit;
                } else {
                    $msgError = "Datenbank-Fehler beim Speichern.";
                }
            } else {
                $msgError = "Datei-Transfer fehlgeschlagen.";
            }
        } else {
            $msgError = "Ungültiges Format! Erlaubt sind: " . implode(', ', $allowed);
        }
    } else {
        $msgError = "Bitte laden Sie ein gültiges Dokument hoch.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Barkeeper werden - MIXD</title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="text-center mb-4">
                    <i class="bi bi-patch-check text-primary display-4"></i>
                    <h2 class="fw-bold mt-2">Barkeeper-Verifizierung</h2>
                    <p class="text-muted small">Lade einen Nachweis hoch, um das Profi-Siegel zu erhalten.</p>
                </div>

                <?php if ($msgError): ?>
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle me-3 fs-4"></i>
                        <div><span class="small"><?= htmlspecialchars($msgError) ?></span></div>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <form action="apply_barkeeper.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="small fw-bold text-muted text-uppercase">Vollständiger Name</label>
                            <input type="text" name="full_name" class="form-control border-0 bg-light py-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-muted text-uppercase">Nachweis (PDF oder Bild)</label>
                            <input type="file" name="doc" class="form-control border-0 bg-light py-2" required>
                            <div class="form-text small mt-1">Lade dein Zertifikat oder Arbeitszeugnis hoch.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm">
                                JETZT ANTRAG STELLEN
                            </button>
                            <a href="profile.php" class="btn btn-link text-muted text-decoration-none small">Abbrechen</a>
                        </div>
                    </form>
                </div>

                <div class="mt-4 p-3 bg-white rounded-4 shadow-sm border-start border-primary border-4">
                    <p class="small text-muted mb-0">
                        <strong>Hinweis:</strong> Nach der Prüfung durch einen Moderator erscheint das Siegel neben deinem Profil.
                    </p>
                </div>

            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>