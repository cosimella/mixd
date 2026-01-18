<?php
session_start();
include "dbutil.php";

include "admin_check.php";

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$app_id = isset($_GET['app_id']) ? (int)$_GET['app_id'] : 0;

switch ($action) {

    case 'delete_recipe':

        $conn->query("DELETE FROM recipe_ingredients WHERE recipe_id = $id");
        $conn->query("DELETE FROM recipe_steps WHERE recipe_id = $id");
        $conn->query("DELETE FROM recipe_images WHERE recipe_id = $id");
        $conn->query("DELETE FROM favorites WHERE recipe_id = $id");
        $conn->query("DELETE FROM ratings WHERE recipe_id = $id");
        $conn->query("DELETE FROM recipes WHERE recipe_id = $id");
        
        header("Location: ../admin_dashboard.php?msg=recipe_deleted");
        break;

    case 'verify_barkeeper':
       
        $conn->query("UPDATE users SET is_barkeeper = 1 WHERE userid = $id");
        $conn->query("UPDATE barkeeper_applications SET status = 'approved' WHERE app_id = $app_id");
        
        header("Location: ../admin_dashboard.php?msg=verified");
        break;

    case 'reject_barkeeper':
    
        $conn->query("UPDATE barkeeper_applications SET status = 'rejected' WHERE app_id = $app_id");
        
        header("Location: ../admin_dashboard.php?msg=rejected");
        break;

    case 'delete_user':
        if ($_SESSION['user_role'] == 3) {
            $conn->query("DELETE FROM users WHERE userid = $id");
            header("Location: ../admin_dashboard.php?msg=user_deleted");
        }
        break;

    default:
        header("Location: ../admin_dashboard.php");
        break;
}
exit;