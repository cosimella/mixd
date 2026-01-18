<?php

session_start();
require_once "util/dbutil.php";
include "util/admin_check.php"; 


$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);
$appId  = (int)($_GET['app_id'] ?? 0);


switch ($action) {

    case 'delete_recipe':
        if ($id > 0) {

            $conn->query("DELETE FROM recipe_ingredients WHERE recipe_id = $id");
            $conn->query("DELETE FROM recipe_steps WHERE recipe_id = $id");
            $conn->query("DELETE FROM recipe_images WHERE recipe_id = $id");
            $conn->query("DELETE FROM favorites WHERE recipe_id = $id");
            $conn->query("DELETE FROM ratings WHERE recipe_id = $id");
            
            $stmt = $conn->prepare("DELETE FROM recipes WHERE recipe_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            header("Location: ../admin_dashboard.php?msg=recipe_deleted");
        }
        break;


    case 'verify_barkeeper':
        if ($id > 0 && $appId > 0) {

            $stmtUser = $conn->prepare("UPDATE users SET is_barkeeper = 1 WHERE userid = ?");
            $stmtUser->bind_param("i", $id);
            $stmtUser->execute();

            $stmtApp = $conn->prepare("UPDATE barkeeper_applications SET status = 'approved' WHERE app_id = ?");
            $stmtApp->bind_param("i", $appId);
            $stmtApp->execute();
            
            header("Location: ../admin_dashboard.php?msg=verified");
        }
        break;


    case 'reject_barkeeper':
        if ($appId > 0) {
            $stmt = $conn->prepare("UPDATE barkeeper_applications SET status = 'rejected' WHERE app_id = ?");
            $stmt->bind_param("i", $appId);
            $stmt->execute();
            
            header("Location: ../admin_dashboard.php?msg=rejected");
        }
        break;


    case 'delete_user':

        if ($_SESSION['user_role'] == 3 && $id > 0) {
            $stmt = $conn->prepare("DELETE FROM users WHERE userid = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            header("Location: ../admin_dashboard.php?msg=user_deleted");
        } else {
            header("Location: ../admin_dashboard.php?msg=no_permission");
        }
        break;

    default:
        header("Location: ../admin_dashboard.php");
        break;
}
exit;
?>