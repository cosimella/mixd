<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$minRole = 2; 

if (!isset($_SESSION['userid']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] < $minRole) {
    
    header("Location: index.php?error=no_access");
    exit;
}

?>