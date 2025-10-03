<?php
    require_once "db.php";

    session_start();

    if(isset($_SESSION['admin']) && $_SESSION['admin'] != "") {
        header("Location: admin_panel.php");
        exit;
    }

    if(isset($_POST['login']) && isset($_POST['passwd'])) {
        $sth = $conn->prepare("SELECT * FROM admin WHERE login = ? and passwd = ?");
        $sth->execute([$_POST['login'], md5($_POST['passwd'])]);

        if($sth->rowCount() == 0) {
            echo "<p>zły login lub hasło</p>";
            echo "<a href='admin.php'>wróć</a>";
            exit;
        }

        $_SESSION['admin'] = $sth->fetch(PDO::FETCH_ASSOC)['login'];
        header("Location: admin_panel.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora - Logowanie</title>
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-page">
        <div class="admin-container">
            <header class="admin-header">
                <img src="http://www.dd-tech.pl/images/logo_min.png" alt="Logo" class="header-logo">
                <h1>Panel Administratora</h1>
                <p>Zaloguj się, aby zarządzać systemem</p>
            </header>
            <form method="post" class="admin-form">
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" id="login" name="login" placeholder="Wprowadź login" required />
                </div>
                <div class="form-group">
                    <label for="passwd">Hasło</label>
                    <input type="password" id="passwd" name="passwd" placeholder="Wprowadź hasło" required />
                </div>
                <button type="submit" class="btn-admin-login">Zaloguj</button>
            </form>
            <footer class="admin-footer">
                <a href="index.php">Powrót do logowania pracownika</a>
            </footer>
        </div>
    </div>
</body>
</html>