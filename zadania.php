<?php
require_once "db.php";
session_start();

// This script now only handles the login logic and redirects to the new tracker.

if (isset($_POST['skaner_id'])) {
    $sth = $conn->prepare("SELECT id FROM pracownik WHERE id_skaner = ? AND usunieto = 0");
    $sth->execute([$_POST['skaner_id']]);
    $pracownik = $sth->fetch(PDO::FETCH_ASSOC);
    
    if ($pracownik) {
        $_SESSION['pracownik_id'] = $pracownik['id'];
        header("Location: tracker.php"); // Redirect to the new tracker page
        exit;
    } else {
        // Optional: Redirect back to login with an error message
        header("Location: index.php?error=invalid_id");
        exit;
    }
}

// If someone tries to access zadania.php directly without posting data, redirect them.
header("Location: index.php");
exit;

