<?php
require_once 'db.php';

session_start();

if(!isset($_SESSION['admin']) || $_SESSION['admin'] == "") {
    header("Location: admin.php");
    exit;
}

if(isset($_GET['id'])) {
    $employee_id = $_GET['id'];
    
    // Check if employee has any time entries
    $sth = $conn->prepare("SELECT COUNT(*) as count FROM czas_pracy WHERE pracownik_id = ?");
    $sth->execute([$employee_id]);
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    
    if($result['count'] > 0) {
        header("Location: blad.php?error=nie można usunąć pracownika z zapisami czasu pracy&back=admin_panel.php");
        exit;
    }
    
    $sth = $conn->prepare("DELETE FROM pracownik WHERE id = ?");
    $sth->execute([$employee_id]);
    
    header("Location: admin_panel.php");
    exit;
} else {
    header("Location: admin_panel.php");
    exit;
}
?>