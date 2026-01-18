<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "auth_check.php";

$minimumRequiredRole = 2; 

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] < $minimumRequiredRole) {
    
    header("Location: ../index.php?error=no_admin_access");
    exit;
}

?>