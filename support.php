<?php
session_start();

// Datenbank-Hilfsfunktionen einbinden
include "util/dbutil.php"; 
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php 
    // Einbinden der globalen Header-Ressourcen (CSS, Meta-Tags)
    include "includes/head-includes.php"; 
    ?>
    <title>Kontakt - Cocktail Welt</title>
</head>

<body class="bg-light">
    <?php 
    // Einbinden der Navigationsleiste
    include "includes/navbar.php"; 
    ?>

    <main class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Kontakt & Hilfe</h1>
            <p>Hast du Fragen zu unseren Rezepten?</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-5 text-center">
                        <h4 class="fw-bold mb-3">Schreib uns eine E-Mail!</h4>
                        <p class="text-muted">
                            Wir freuen uns über jedes Feedback – egal ob Lob oder Kritik an unseren Drinks.
                            Normalerweise antworten wir innerhalb eines Tages.
                        </p>
                        
                        <div class="mt-4 p-4 bg-light rounded-4">
                            <i class="bi bi-envelope-at text-primary fs-1"></i>
                            <br>
                            <a href="mailto:support@mixd.at" class="h5 text-decoration-none fw-bold">
                                support@mixd.at
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 rounded-4 h-100 p-3 text-center">
                            <h6 class="fw-bold">Hilfe / FAQ</h6>
                            <p class="small text-muted">Hier findest du Antworten auf die häufigsten Fragen.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 rounded-4 h-100 p-3 text-center">
                            <h6 class="fw-bold">Für Partner</h6>
                            <p class="small text-muted">Du willst deine Spirituosen bei uns zeigen? Melde dich!</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php 
    // Einbinden des Seitenfußes
    include "includes/footer.php"; 
    ?>
</body>
</html>