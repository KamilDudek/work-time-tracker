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
    <title>Zaloguj</title>
    <link rel="stylesheet" href="global.css">
</head>
<body>

    <div class="adminnavv">
        <h1>admin panel</h1>
        <h2><a href="index.php" class="adminnav">powrót</a></h2>
    </div>

    <form method="post" class="form-boxx">
        <input type="text" name="login" placeholder="login"  reqiured />
        <input type="password" name="passwd" placeholder="hasło" required />
        <button type="submit">zaloguj</button>
    </form>

</body>
</html>