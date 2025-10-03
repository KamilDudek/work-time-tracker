<?php

require_once 'db.php';

session_start();

if((!isset($_SESSION['admin']) || !isset($_GET['id']) && !isset($_GET['z']) && !isset($_GET['o']) && !isset($_GET['a']) && !isset($_GET['n']))) {
    header("Location: index.php");
    exit;
}

function set_projekt($conn, $id, $state) {
    $sth = $conn->prepare("UPDATE projekt SET zakonczono = ? WHERE id = ?");
    $sth->execute([$state, $id]);
    return $sth->fetch(PDO::FETCH_ASSOC);
}

if(isset($_GET['z'])) {
    set_projekt($conn, $_GET['id'], 1);
    header("Location: admin_panel.php");
    exit();
} else if(isset($_GET['o'])) {
    set_projekt($conn, $_GET['id'], 0);
    header("Location: admin_panel.php?c");    
}

if(isset($_GET['a'])) {
    $sth = $conn->prepare("UPDATE projekt SET zaakceptowano = 1 WHERE id = ?");
    $sth->execute([$_GET['id']]);
    header("Location: admin_panel.php");    
} else if(isset($_GET['n'])) {
    $sth = $conn->prepare("DELETE FROM projekt WHERE id = ?");
    $sth->execute([$_GET['id']]);
    header("Location: admin_panel.php");    
}