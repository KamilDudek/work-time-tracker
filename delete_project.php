<?php
require_once 'db.php';

session_start();

if(!isset($_SESSION['admin']) || $_SESSION['admin'] == "") {
    header("Location: admin.php");
    exit;
}

if(isset($_GET['id'])) {
    $project_id = $_GET['id'];
    
    // Check if project has any time entries
    $sth = $conn->prepare("SELECT COUNT(*) as count FROM czas_pracy WHERE projekt_id = ?");
    $sth->execute([$project_id]);
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    
    if($result['count'] > 0) {
        header("Location: blad.php?error=nie można usunąć projektu z zapisami czasu pracy&back=admin_panel.php");
        exit;
    }
    
    $sth = $conn->prepare("DELETE FROM projekt WHERE id = ?");
    $sth->execute([$project_id]);
    
    header("Location: admin_panel.php");
    exit;
} else {
    header("Location: admin_panel.php");
    exit;
}
?>