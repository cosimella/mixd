<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userid'])) {

    header("Location: login.php?msg=login_required");
    
    exit;
}
?>