<?php

// 1. Sitzung initialisieren (falls nicht bereits durch die aufrufende Seite geschehen)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "auth_check.php";

// 3. AUTORISIERUNG (RBAC): Verfügt der Nutzer über Moderations- oder Admin-Rechte?
// Hierarchie: 1 = User, 2 = Moderator, 3 = Administrator
$minimumRequiredRole = 2; 

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] < $minimumRequiredRole) {
    
    // Zugriff verweigert: Zurück zur Startseite mit Fehler-Flag
    header("Location: ../index.php?error=no_admin_access");
    exit;
}

?>