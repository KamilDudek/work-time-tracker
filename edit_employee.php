<?php
require_once 'db.php';

session_start();

if(!isset($_SESSION['admin']) || $_SESSION['admin'] == "") {
    header("Location: admin.php");
    exit;
}

if(isset($_POST['edit_employee'])) {
    $employee_id = $_POST['employee_id'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $id_skaner = $_POST['id_skaner'];
    
    if($imie == "" || $nazwisko == "" || $id_skaner == "") {
        header("Location: blad.php?error=proszę wprowadzić wszystkie dane&back=admin_panel.php");
        exit;
    }
    
    if(strlen($id_skaner) != 8) {
        header("Location: blad.php?error=id skanera musi mieć dokładnie 8 cyfr&back=admin_panel.php");
        exit;
    }
    
    // Check if scanner ID already exists (excluding current employee)
    $sth = $conn->prepare("SELECT * FROM pracownik WHERE id_skaner = ? AND id != ?");
    $sth->execute([$id_skaner, $employee_id]);
    if($sth->rowCount() > 0) {
        header("Location: blad.php?error=ten kod skanera już jest używany&back=admin_panel.php");
        exit;
    }
    
    $sth = $conn->prepare("UPDATE pracownik SET imie = ?, nazwisko = ?, id_skaner = ? WHERE id = ?");
    $sth->execute([$imie, $nazwisko, $id_skaner, $employee_id]);
    
    header("Location: admin_panel.php");
    exit;
}

if(isset($_GET['id'])) {
    $employee_id = $_GET['id'];
    $sth = $conn->prepare("SELECT * FROM pracownik WHERE id = ?");
    $sth->execute([$employee_id]);
    $employee = $sth->fetch(PDO::FETCH_ASSOC);
    
    if(!$employee) {
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
    <title>Edytuj Pracownika</title>
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="admin_panel.css">
</head>
<body>
    <div class="admin-panel-container">
        <header class="admin-panel-header">
            <h1>Edytuj Pracownika</h1>
            <div class="admin-user-info">
                <span>Zalogowano jako <strong><?= $_SESSION['admin'] ?></strong></span>
                <a href="admin_panel.php">Powrót do panelu</a>
            </div>
        </header>

        <main>
            <section class="admin-section">
                <h2>Edytuj dane pracownika</h2>
                <form method="post" class="admin-form">
                    <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
                    <div class="form-group">
                        <label for="imie">Imię</label>
                        <input type="text" id="imie" name="imie" value="<?= $employee['imie'] ?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="nazwisko">Nazwisko</label>
                        <input type="text" id="nazwisko" name="nazwisko" value="<?= $employee['nazwisko'] ?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="id_skaner">ID Skanera</label>
                        <input type="text" id="id_skaner" name="id_skaner" value="<?= $employee['id_skaner'] ?>" pattern="\d{8}" maxlength="8" minlength="8" required/>
                    </div>
                    <button type="submit" name="edit_employee" class="btn-add">Zapisz zmiany</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>