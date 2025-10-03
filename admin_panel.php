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
    <title>admin</title>
    <link rel="stylesheet" href="global.css">
</head>
<body>
    <nav><p>zalogowano jako <?= $_SESSION['admin'] ?></p><a href="przegladaj.php">filtrowanie</a><a href="logout.php">wyloguj</a></nav>
<div class="dodaj1">
    <h1>dodaj pracownika</h1>
    <form method="post" action="dodaj.php">
        <input type="text" name="imie" placeholder="imię" required/>
        <input type="text" name="nazwisko" placeholder="nazwisko" required/>
        <input type="text" name="id_skaner" placeholder="kod skanera" required pattern="\d{8}" maxlength="8" minlength="8" />
        <button type="submit" name="b_pracownik">dodaj</button>
    </form>
</div>
<div class="dodaj2">
<h1>dodaj projekt</h1>
<form method="post" action="dodaj.php">
    <input type="number" name="nazwa" placeholder="numer" max="9999" required/>
    <input type="text" name="opis" maxlength="64" placeholder="opis (opcjonalnie)"/>
    <button type="submit" name="b_projekt">dodaj</button>
</form>
</div>

    <h1>projekty</h1>
    <table class="tabelka3">
        <tr>
            <th>projekt</th>
            <th>akcja</th>
        </tr>
<?php
    while($row = $sth2->fetch(PDO::FETCH_ASSOC)) {
        echo "</td>";
        echo "<td>" . format_project_number($row['nazwa']) . ($row['opis'] != "" ? " - " . $row['opis'] : "") . "</td>";
        echo "<td><a href='akcja_zadania.php?" . (isset($_GET['c']) ? 'o' : 'z') . "&id=" . $row['id'] . "'>" . (isset($_GET['c']) ? 'otwórz' : 'archiwizuj') . "</a>" . (($row['zaakceptowano'] == 0) ? " <a href='akcja_zadania.php?a=1&id=" . $row['id'] . "'>akceptuj</a> <a href='akcja_zadania.php?n=1&id=" . $row['id'] . "'>odrzuć</a>" : "")  . "</td>";
        echo "</tr>";
    }
?>
    </table>
    <div class="tabelka">
        <h2><a href="admin_panel.php<?= isset($_GET['c']) ? "" : "?c" ?>"><?= isset($_GET['c']) ? "pokaż otwarte projekty" : "pokaż archiwizowane projekty" ?> </a></h2>
        <h2><a href="przegladaj.php">przeglądaj z filtrami</a></h2>
    </div>
</body>
</html>