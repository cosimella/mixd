<?php
session_start();

require_once "util/dbutil.php"; 
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php 
    include "includes/head-includes.php"; 
    ?>
    <title>Datenschutz - MIXD</title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    
    <?php 
    include "includes/navbar.php"; 
    ?>

    <main class="container py-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5">
                    <h2 class="fw-bold mb-4">Datenschutzerklärung</h2>
                    
                    <p class="text-muted">
                        Der Schutz deiner persönlichen Daten ist uns wichtig. Hier erfährst du, wie MIXD mit deinen Informationen umgeht.
                    </p>

                    <hr class="my-5">

                    <section class="mb-5">
                        <h4 class="fw-bold">1. Datenschutz auf einen Blick</h4>
                        <p>
                            Personenbezogene Daten sind Informationen, mit denen du identifiziert werden kannst. 
                            Wir speichern nur Daten, die für den Betrieb der Community-Funktionen (Rezepte, Favoriten) notwendig sind.
                        </p>
                    </section>

                    <section class="mb-5">
                        <h4 class="fw-bold mb-3">2. Datenerfassung auf dieser Website</h4>
                        
                        
                        <div class="p-3 bg-light border-start border-primary border-4 rounded-3 mb-3">
                            <h5 class="h6 fw-bold">Registrierung & Login</h5>
                            <p class="small text-muted mb-0">
                                Wir speichern Benutzernamen, E-Mail und passwort_hash. 
                                Passwörter werden niemals im Klartext gespeichert.
                            </p>
                        </div>

                        <div class="p-3 bg-light border-start border-info border-4 rounded-3 mb-3">
                            <h5 class="h6 fw-bold">Bilder & Uploads</h5>
                            <p class="small text-muted mb-0">
                                Hochgeladene Fotos werden auf unserem Webserver gespeichert und mit deiner User-ID verknüpft.
                            </p>
                        </div>

                        <div class="p-3 bg-light border-start border-secondary border-4 rounded-3 mb-3">
                            <h5 class="h6 fw-bold">Cookies</h5>
                            <p class="small text-muted mb-0">
                                MIXD nutzt Session-Cookies zur Identifizierung deines Logins. Diese verfallen automatisch nach dem Schließen des Browsers.
                            </p>
                        </div>
                    </section>

                    <section>
                        <h4 class="fw-bold">3. Deine Rechte</h4>
                        <p>
                            Du hast das Recht auf Auskunft, Berichtigung und Löschung deiner Daten. 
                            Kontolöschungen können jederzeit über den Support angefordert werden.
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