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
    <title>Impressum - MIXD</title>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    
    <?php 
    include "includes/navbar.php"; 
    ?>

    <main class="container py-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5">
                    <h2 class="fw-bold mb-4">Impressum</h2>
                    
                    <section class="mb-5">
                        <h5 class="fw-bold">Angaben gemäß § 5 TMG</h5>
                        <p class="text-muted">
                            Max Mustermann<br>
                            MIXD<br>
                            Musterstraße 123<br>
                            6767 Musterstadt
                        </p>
                    </section>

                    <section class="mb-5">
                        <h5 class="fw-bold">Kontakt</h5>
                        <p class="text-muted">
                            Telefon: +43 (0) 123 6769187<br>
                            E-Mail: <a href="mailto:support@mixd.at" class="text-decoration-none fw-bold">support@mixd.at</a>
                        </p>
                    </section>

                    <section class="mb-5">
                        <h5 class="fw-bold">Redaktionell verantwortlich</h5>
                        <p class="text-muted">
                            Max Mustermann<br>
                            Musterstraße 123<br>
                            6767 Musterstadt
                        </p>
                    </section>

                    <hr class="my-4">

                    <p class="small text-muted mb-0">
                        Dieses Impressum gilt auch für unsere Social Media Kanäle.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <?php 
    include "includes/footer.php"; 
    ?>
</body>
</html>