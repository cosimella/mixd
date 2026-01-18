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
    <title>Kontakt - MIXD</title>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
    
    <?php 
    include "includes/navbar.php"; 
    ?>

    <main class="container py-5 flex-grow-1">
        
        <div class="text-center mb-5">
            <h1 class="fw-bold">Kontakt & Hilfe</h1>
            <p class="text-muted">Hast du Fragen zu unseren Rezepten oder Feedback für uns?</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-5 text-center">
                        <h4 class="fw-bold mb-3">Schreib uns eine E-Mail!</h4>
                        <p class="text-muted">
                            Wir freuen uns über jedes Feedback. 
                            Unser Team antwortet in der Regel innerhalb von 24 Stunden.
                        </p>
                        
                        <div class="mt-4 p-4 bg-light rounded-4">
                            <i class="bi bi-envelope-at text-primary fs-1 mb-3 d-block"></i>
                            <a href="mailto:support@mixd.at" class="h5 text-decoration-none fw-bold text-primary">
                                support@mixd.at
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 rounded-4 h-100 p-4 text-center">
                            <i class="bi bi-question-circle text-muted fs-3 mb-2"></i>
                            <h6 class="fw-bold">Hilfe / FAQ</h6>
                            <p class="small text-muted mb-0">Antworten auf die häufigsten Fragen rund um das Mixen.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 rounded-4 h-100 p-4 text-center">
                            <i class="bi bi-briefcase text-muted fs-3 mb-2"></i>
                            <h6 class="fw-bold">Für Partner</h6>
                            <p class="small text-muted mb-0">Zusammenarbeit und geschäftliche Anfragen.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php 
    include "includes/footer.php"; 
    ?>
</body>
</html>