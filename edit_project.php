<?php
require_once 'db.php';

session_start();

if(!isset($_SESSION['admin']) || $_SESSION['admin'] == "") {
    header("Location: admin.php");
    exit;
}

if(isset($_POST['edit_project'])) {
    $project_id = $_POST['project_id'];
    $nazwa = $_POST['nazwa'];
    $opis = $_POST['opis'];
    
    if(strlen($nazwa) > 4) {
        header("Location: blad.php?error=nazwa projektu to max 4 znaki&back=admin_panel.php");
        exit;
    }
    
    // Check if project number already exists (excluding current project)
    $sth = $conn->prepare("SELECT * FROM projekt WHERE nazwa = ? AND id != ?");
    $sth->execute([$nazwa, $project_id]);
    if($sth->rowCount() > 0) {
        header("Location: blad.php?error=ten numer projektu już istnieje&back=admin_panel.php");
        exit;
    }
    
    $sth = $conn->prepare("UPDATE projekt SET nazwa = ?, opis = ? WHERE id = ?");
    $sth->execute([$nazwa, ($opis != "") ? $opis : null, $project_id]);
    
    header("Location: admin_panel.php");
    exit;
}

if(isset($_GET['id'])) {
    $project_id = $_GET['id'];
    $sth = $conn->prepare("SELECT * FROM projekt WHERE id = ?");
    $sth->execute([$project_id]);
    $project = $sth->fetch(PDO::FETCH_ASSOC);
    
    if(!$project) {
        header("Location: admin_panel.php");
        exit;
    }
} else {
    header("Location: admin_panel.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Projekt</title>
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="admin_panel.css">
</head>
<body>
    <div class="admin-panel-container">
        <header class="admin-panel-header">
            <h1>Edytuj Projekt</h1>
            <div class="admin-user-info">
                <span>Zalogowano jako <strong><?= $_SESSION['admin'] ?></strong></span>
                <a href="admin_panel.php">Powrót do panelu</a>
            </div>
        </header>

        <main>
            <section class="admin-section">
                <h2>Edytuj dane projektu</h2>
                <form method="post" class="admin-form">
                    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                    <div class="form-group">
                        <label for="nazwa">Numer Projektu</label>
                        <input type="number" id="nazwa" name="nazwa" value="<?= $project['nazwa'] ?>" max="9999" required/>
                    </div>
                    <div class="form-group">
                        <label for="opis">Opis</label>
                        <input type="text" id="opis" name="opis" value="<?= $project['opis'] ?>" maxlength="64" placeholder="Opcjonalny opis"/>
                    </div>
                    <button type="submit" name="edit_project" class="btn-add">Zapisz zmiany</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>