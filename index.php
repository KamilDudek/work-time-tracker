<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie Pracownika - System Czasu Pracy</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <header class="login-header">
                <img src="http://www.dd-tech.pl/images/logo_min.png" alt="Logo" class="header-logo">
                <h1>System Ewidencji Czasu Pracy</h1>
                <p>Zaloguj się, aby rozpocząć</p>
            </header>
            <form action="zadania.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="employee-id">ID Pracownika</label>
                    <input type="text" id="employee-id" name="skaner_id" placeholder="Wprowadź 8-cyfrowe ID" pattern="\d{8}" maxlength="8" minlength="8" required title="Wprowadź dokładnie 8 cyfr">
                </div>
                <button type="submit" class="btn-login">Zaloguj</button>
            </form>
            <footer class="login-footer">
                <a href="admin.php">Panel Administratora</a>
            </footer>
        </div>
    </div>
</body>
</html>