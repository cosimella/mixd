<?php
session_start();
?>

<!DOCTYPE html>
<html lang="de">
<head>

    <?php 
    include "includes/head-includes.php"; 
    ?>

    <title>Datenschutz - MIXD</title>
</head>

<body class="bg-light">

<?php 
include "includes/navbar.php"; 
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5">
                <h2 class="fw-bold mb-4">Datenschutzerklärung</h2>
                
                <p class="text-muted">
                    Hier erfährst du, wie wir mit deinen Daten umgehen. Wir versuchen, alles so einfach wie möglich zu erklären.
                </p>

                <hr class="my-5">

                <section class="mb-5">
                    <h4 class="fw-bold">1. Datenschutz auf einen Blick</h4>
                    <p>
                        Personenbezogene Daten sind alle Daten, mit denen du persönlich identifiziert werden kannst (z.B. deine E-Mail Adresse). 
                        Wir speichern nur das, was wirklich nötig ist, damit MIXD funktioniert.
                    </p>
                </section>

                <section class="mb-5">
                    <h4 class="fw-bold mb-3">2. Datenerfassung auf dieser Website</h4>
                    
                    <div class="p-3 bg-light border-start border-primary border-4 rounded-3 mb-3">
                        <h5 class="h6 fw-bold">Registrierung & Login</h5>
                        <p class="small text-muted mb-0">
                            Wir speichern deinen Benutzernamen, deine E-Mail und dein verschlüsseltes Passwort. 
                            Ohne diese Daten könntest du keine Rezepte speichern oder favorisieren.
                        </p>
                    </div>

                    <div class="p-3 bg-light border-start border-info border-4 rounded-3 mb-3">
                        <h5 class="h6 fw-bold">Bilder & Uploads</h5>
                        <p class="small text-muted mb-0">
                            Hochgeladene Fotos landen auf unserem Server. Achte bitte darauf, dass du die Rechte an den Bildern besitzt.
                        </p>
                    </div>

                    <div class="p-3 bg-light border-start border-secondary border-4 rounded-3 mb-3">
                        <h5 class="h6 fw-bold">Cookies</h5>
                        <p class="small text-muted mb-0">
                            Wir nutzen nur "Session-Cookies". Die merken sich einfach nur, dass du gerade eingeloggt bist und werden gelöscht, wenn du den Browser schließt.
                        </p>
                    </div>
                </section>

                <section>
                    <h4 class="fw-bold">3. Deine Rechte</h4>
                    <p>
                        Du hast jederzeit das Recht zu erfahren, welche Daten wir über dich haben. 
                        Du kannst auch verlangen, dass wir dein Konto und alle deine Daten löschen. 
                        Schreib uns dazu einfach eine Nachricht (siehe <a href="imprint.php">Impressum</a>).
                    </p>
                </section>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="text-muted small text-decoration-none">
                    <i class="bi bi-house"></i> Zurück zur Startseite
                </a>
            </div>

        </div>
    </div>
</main>

<?php 
include "includes/footer.php"; 
?>

</body>
</html>