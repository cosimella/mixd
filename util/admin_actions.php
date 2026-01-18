<?php
session_start();
include "dbutil.php";

include "admin_check.php";

// 2. DATEN HOLEN: Was soll getan werden und bei wem?
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$app_id = isset($_GET['app_id']) ? (int)$_GET['app_id'] : 0;

// 3. LOGIK-WEICHE (Was wurde angeklickt?)
switch ($action) {

    // --- FALL A: EIN REZEPT LÖSCHEN ---
    case 'delete_recipe':
        // Wir löschen alles, was zum Rezept gehört (Zutaten, Schritte, Bilder-Einträge)
        $conn->query("DELETE FROM recipe_ingredients WHERE recipe_id = $id");
        $conn->query("DELETE FROM recipe_steps WHERE recipe_id = $id");
        $conn->query("DELETE FROM recipe_images WHERE recipe_id = $id");
        $conn->query("DELETE FROM favorites WHERE recipe_id = $id");
        $conn->query("DELETE FROM ratings WHERE recipe_id = $id");
        $conn->query("DELETE FROM recipes WHERE recipe_id = $id");
        
        header("Location: ../admin_dashboard.php?msg=recipe_deleted");
        break;

    // --- FALL B: BARKEEPER VERIFIZIEREN (Annehmen) ---
    case 'verify_barkeeper':
        // 1. In der User-Tabelle das Häkchen setzen
        $conn->query("UPDATE users SET is_barkeeper = 1 WHERE userid = $id");
        // 2. Den Antrag als 'approved' markieren
        $conn->query("UPDATE barkeeper_applications SET status = 'approved' WHERE app_id = $app_id");
        
        header("Location: ../admin_dashboard.php?msg=verified");
        break;

    // --- FALL C: BARKEEPER ABLEHNEN ---
    case 'reject_barkeeper':
        // Den Antrag einfach auf 'rejected' setzen
        $conn->query("UPDATE barkeeper_applications SET status = 'rejected' WHERE app_id = $app_id");
        
        header("Location: ../admin_dashboard.php?msg=rejected");
        break;

    // --- FALL D: USER LÖSCHEN (Nur für echte Admins - Rolle 3) ---
    case 'delete_user':
        if ($_SESSION['user_role'] == 3) {
            $conn->query("DELETE FROM users WHERE userid = $id");
            header("Location: ../admin_dashboard.php?msg=user_deleted");
        }
        break;

    // Falls nichts zutrifft: Zurück zum Dashboard
    default:
        header("Location: ../admin_dashboard.php");
        break;
}
exit;