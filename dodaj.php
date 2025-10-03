<?php
require_once 'db.php';

session_start();

if(!isset($_SESSION['admin']) && !isset($_POST['b_pracownik']) && !isset($_POST['b_projekt'])) {
    header("Location: index.php");
    exit;
} 

if(isset($_POST['b_projekt'])) {
    if(strlen($_POST['nazwa']) > 4) {
        header("Location: blad.php?error=nazwa projektu to max 4 znaki&back=admin.php");
        exit;
    }

    $sth = $conn->prepare("SELECT * FROM projekt WHERE nazwa = ?");
    $sth->execute([$_POST['nazwa']]);
    if($sth->rowCount() > 0) {
        header("Location: blad.php?error=ten projekt już istnieje&back=" . (isset($_SESSION['admin']) ? 'admin.php' : 'zadania.php'));
        exit;
    }

$sth = $conn->prepare("INSERT INTO projekt (nazwa, opis, zaakceptowano) VALUES (?, ?, ?)");
$sth->execute([
    $_POST['nazwa'],
    ($_POST['opis'] != "") ? $_POST['opis'] : null,
    0
]);


    header("Location: " . (isset($_SESSION['admin']) ? "admin.php" : "zadania.php"));
    exit;
}


if(isset($_POST['b_pracownik'])) {
    if($_POST['imie'] == "" || $_POST['nazwisko'] == "" || $_POST['id_skaner'] == "") {
        header("Location: blad.php?error=proszę wprowadzić dane&back=admin.php");
        exit;
    }
    if(strlen($_POST['id_skaner']) > 8) {
        header("Location: blad.php?error=id skanera to max 8 cyfry&back=admin.php");
        exit;
    }
    
    $sth = $conn->prepare("SELECT * FROM pracownik WHERE id_skaner = ?");
    $sth->execute([$_POST['id_skaner']]);
    if($sth->rowCount() > 0) {
        header("Location: blad.php?error=ten kod skanera już jest używany&back=admin.php");
        exit;
    }
    $sth = $conn->prepare("INSERT INTO pracownik (id_skaner, imie, nazwisko) VALUES (?, ?, ?)");
    $sth->execute([$_POST['id_skaner'], $_POST['imie'], $_POST['nazwisko']]);

    header("Location: admin.php");
}