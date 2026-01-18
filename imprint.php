<?php
// Sitzung starten, um z.B. den Login-Status in der Navbar anzuzeigen
session_start();

// Einbinden der zentralen Datenbank-Konfiguration
include "util/dbutil.php"; 
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <?php 
    // Zentrales Einbinden von Meta-Daten, Bootstrap und CSS-Ressourcen
    include "includes/head-includes.php"; 
    ?>
    <title>Impressum - MIXD</title>
</head>
<body class="bg-light">
<?php 
// Die Navigation wird modular geladen, damit Änderungen überall gleichzeitig wirken
include "includes/navbar.php"; 
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5">
                <h2 class="fw-bold mb-4">Impressum</h2>
                
                <div class="mb-5">
                    <h5 class="fw-bold">Angaben gemäß § 5 TMG</h5>
                    <p class="text-muted">
                        Max Mustermann<br>
                        Cocktail Welt Projekte<br>
                        Musterstraße 123<br>
                        12345 Musterstadt
                    </p>
                </div>

                <div class="mb-5">
                    <h5 class="fw-bold">Kontakt</h5>
                    <p class="text-muted">
                        Telefon: +49 (0) 123 445566<br>
                        E-Mail: <a href="mailto:support@cocktail-welt.de" class="text-decoration-none fw-bold">support@cocktail-welt.de</a>
                    </p>
                </div>

                <div class="mb-5">
                    <h5 class="fw-bold">Redaktionell verantwortlich</h5>
                    <p class="text-muted">
                        Max Mustermann<br>
                        Musterstraße 123<br>
                        12345 Musterstadt
                    </p>
                </div>

                <hr>

                <p class="small text-muted mb-0">
                    Dieses Impressum gilt auch für unsere Social Media Kanäle.
                </p>
            </div>
        </div>
    </div>
</main>

<?php 
// Der Footer enthält meist rechtliche Links und wird ebenfalls zentral eingebunden
include "includes/footer.php"; 
?>
</body>
</html>