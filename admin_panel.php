<?php
    require_once "db.php";

    session_start();

    if(!isset($_SESSION['admin']) && $_SESSION['admin'] == "") {
        header("Location: admin.php");
        exit;
    }

    $sort = array(
        "id" => "czas_pracy.id",
        "pracownik" => "pracownik.id",
        "projekt" => "projekt.id",
        "start" => "czas_pracy.poczatek",
        "koniec" => "czas_pracy.zakonczenie"
    );

    $sth2 = $conn->prepare("select projekt.id, projekt.nazwa, projekt.opis, projekt.zaakceptowano from projekt where zakonczono = ? order by projekt.zaakceptowano desc, projekt.nazwa asc");
    $sth2->execute([(isset($_GET['c']) ? 1 : 0)]);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="admin_panel.css">
</head>
<body>
    <div class="admin-panel-container">
        <header class="admin-panel-header">
            <h1>Panel Administratora</h1>
            <div class="admin-user-info">
                <span>Zalogowano jako <strong><?= $_SESSION['admin'] ?></strong></span>
                <a href="logout.php">Wyloguj</a>
            </div>
        </header>

        <main>
            <section class="admin-section">
                <h2>Dodaj Pracownika</h2>
                <form method="post" action="dodaj.php" class="admin-form">
                    <div class="form-group">
                        <label for="imie">Imię</label>
                        <input type="text" id="imie" name="imie" placeholder="Imię" required/>
                    </div>
                    <div class="form-group">
                        <label for="nazwisko">Nazwisko</label>
                        <input type="text" id="nazwisko" name="nazwisko" placeholder="Nazwisko" required/>
                    </div>
                    <div class="form-group">
                        <label for="id_skaner">ID Skanera</label>
                        <input type="text" id="id_skaner" name="id_skaner" placeholder="8-cyfrowy kod" required pattern="\d{8}" maxlength="8" minlength="8" />
                    </div>
                    <button type="submit" name="b_pracownik" class="btn-add">Dodaj</button>
                </form>
            </section>

            <section class="admin-section">
                <h2>Dodaj Projekt</h2>
                <form method="post" action="dodaj.php" class="admin-form">
                    <div class="form-group">
                        <label for="nazwa">Numer Projektu</label>
                        <input type="number" id="nazwa" name="nazwa" placeholder="Numer" max="9999" required/>
                    </div>
                    <div class="form-group">
                        <label for="opis">Opis</label>
                        <input type="text" id="opis" name="opis" maxlength="64" placeholder="Opcjonalny opis"/>
                    </div>
                    <button type="submit" name="b_projekt" class="btn-add">Dodaj</button>
                </form>
            </section>

            <section class="admin-section">
                <h2>Projekty</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Projekt</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while($row = $sth2->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . format_project_number($row['nazwa']) . ($row['opis'] != "" ? " - " . $row['opis'] : "") . "</td>";
                                echo "<td>";
                                echo "<a href='akcja_zadania.php?" . (isset($_GET['c']) ? 'o' : 'z') . "&id=" . $row['id'] . "'>" . (isset($_GET['c']) ? 'Otwórz' : 'Archiwizuj') . "</a>";
                                if ($row['zaakceptowano'] == 0) {
                                    echo "<a href='akcja_zadania.php?a=1&id=" . $row['id'] . "'>Akceptuj</a>";
                                    echo "<a href='akcja_zadania.php?n=1&id=" . $row['id'] . "'>Odrzuć</a>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
                <footer class="admin-panel-footer">
                    <a href="admin_panel.php<?= isset($_GET['c']) ? "" : "?c" ?>"><?= isset($_GET['c']) ? "Pokaż otwarte projekty" : "Pokaż zarchiwizowane projekty" ?></a>
                    <a href="przegladaj.php">Przeglądaj z filtrami</a>
                </footer>
            </section>
        </main>
    </div>
</body>
</html>