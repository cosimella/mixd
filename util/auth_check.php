<?php
// Wir prüfen, ob die Session-Variable 'userid' NICHT gesetzt ist.
if (!isset($_SESSION['userid'])) {
    
    // Falls keine ID gefunden wurde, ist der Nutzer nicht eingeloggt.
    // Wir leiten ihn sofort zur Login-Seite um.
    header("Location: login.php");
    
    // WICHTIG: exit verhindert, dass der restliche Code der 
    // aufrufenden Seite noch ausgeführt wird.
    exit;
}
?>