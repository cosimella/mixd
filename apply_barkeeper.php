<?php
session_start();
include "util/dbutil.php";
include "util/auth_check.php"; 

$currentUserId = $_SESSION['userid'];
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicantFullName = trim($_POST['full_name']);

    if (isset($_FILES['doc']) && $_FILES['doc']['error'] === 0) {
        $verificationUploadDir = 'resources/uploads/verify/';
        
        $fileExtension = strtolower(pathinfo($_FILES['doc']['name'], PATHINFO_EXTENSION));
        $allowedFileTypes = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($fileExtension, $allowedFileTypes)) {
    
            $uniqueFileName = "verify_" . $currentUserId . "_" . time() . "." . $fileExtension;
            $destinationPath = $verificationUploadDir . $uniqueFileName;

            if (move_uploaded_file($_FILES['doc']['tmp_name'], $destinationPath)) {
                
                $insertQuery = "INSERT INTO barkeeper_applications (userid, full_name, document_path, status) VALUES (?, ?, ?, 'pending')";
                $statementApplication = $conn->prepare($insertQuery);
                $statementApplication->bind_param("iss", $currentUserId, $applicantFullName, $destinationPath);
                
                if ($statementApplication->execute()) {
                
                    header("Location: profile.php?msg=applied");
                    exit;
                } else {
                    $errorMessage = "Datenbank-Fehler beim Speichern des Antrags.";
                }
                $statementApplication->close();
            } else {
                $errorMessage = "Fehler beim Verschieben der Datei. Prüfe die Schreibrechte des Ordners.";
            }
        } else {
            $errorMessage = "Ungültiges Format! Nur JPG, PNG oder PDF erlaubt.";
        }
    } else {
        $errorMessage = "Bitte wähle ein Dokument zum Hochladen aus.";
    }
}

$queryPendingCheck = $conn->query("SELECT status FROM barkeeper_applications WHERE userid = $currentUserId AND status = 'pending'");

if ($queryPendingCheck->num_rows > 0) {
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php include "includes/head-includes.php"; ?>
    <title>Barkeeper werden - MIXD</title>
</head>
<body class="bg-light">
    <?php include "includes/navbar.php"; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="text-center mb-4">
                    <i class="bi bi-patch-check text-primary display-4"></i>
                    <h2 class="fw-bold mt-2">Barkeeper-Verifizierung</h2>
                    <p class="text-muted">Lade einen Nachweis hoch, um als Profi markiert zu werden.</p>
                </div>

                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-3">
                        <i class="bi bi-exclamation-triangle me-2"></i> <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <form action="apply_barkeeper.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vollständiger Name</label>
                            <input type="text" name="full_name" class="form-control border-0 bg-light py-2" placeholder="z.B. Max Mustermann" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nachweis (PDF oder Bild)</label>
                            <input type="file" name="doc" class="form-control border-0 bg-light py-2" required>
                            <div class="form-text small mt-1">Lade hier dein Zertifikat oder Arbeitszeugnis hoch.</div>
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
                        <strong>Hinweis:</strong> Dein Antrag wird von unserem Team geprüft. Sobald du verifiziert bist, erscheint ein blaues Siegel neben deinem Namen.
                    </p>
                </div>

            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>