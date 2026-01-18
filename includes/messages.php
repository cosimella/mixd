<?php 
/**
 * MESSAGES-KOMPONENTE (Anfängerfreundliche Version)
 * Hier zeigen wir dem Nutzer an, ob alles geklappt hat.
 */

// 1. FEHLERMELDUNG PRÜFEN
// Wir schauen nach, ob irgendwo ein Fehlertext gespeichert wurde
$aktuellerFehler = "";

if (isset($errorMessage) && !empty($errorMessage)) {
    $aktuellerFehler = $errorMessage;
} elseif (isset($error) && !empty($error)) {
    $aktuellerFehler = $error;
}

// Wenn wir einen Fehlertext gefunden haben, zeichnen wir die rote Box
if ($aktuellerFehler != ""): 
?>
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div>
                <strong class="d-block">Hoppla!</strong>
                <span class="small"><?php echo htmlspecialchars($aktuellerFehler); ?></span>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php 
// 2. ERFOLGSMELDUNG PRÜFEN
$erfolgsText = "";

if (isset($successMessage) && !empty($successMessage)) {
    $erfolgsText = $successMessage;
} elseif (isset($success) && !empty($success)) {
    $erfolgsText = $success;
}

// Wenn alles geklappt hat, zeichnen wir die grüne Box
if ($erfolgsText != ""): 
?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-3 fs-4"></i>
            <div>
                <strong class="d-block">Erfolg!</strong>
                <span class="small"><?php echo htmlspecialchars($erfolgsText); ?></span>
            </div>
        </div>
    </div>
<?php endif; ?>