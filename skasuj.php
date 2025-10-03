<?php
require_once "db.php";
$sth = $conn->prepare("UPDATE projekt SET zakonczono = 1 WHERE id = ?");
$sth->execute([$_GET['id']]);
header("Location: admin_panel.php");
?>