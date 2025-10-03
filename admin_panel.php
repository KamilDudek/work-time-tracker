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
    
    // Get all employees
    $sth_employees = $conn->prepare("SELECT id, imie, nazwisko, id_skaner FROM pracownik ORDER BY nazwisko, imie");
    $sth_employees->execute();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="admin_panel.css">
    <script src="admin_panel.js" defer></script>
</head>
<body>
    <div class="admin-panel-container">
        <header class="admin-panel-header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <img src="http://www.dd-tech.pl/images/logo_min.png" alt="Logo" class="header-logo">
                <h1>Panel Administratora</h1>
            </div>
            <div class="admin-user-info">
                <span>Zalogowano jako <strong><?= $_SESSION['admin'] ?></strong></span>
                <a href="admin_panel.php<?= isset($_GET['c']) ? "" : "?c" ?>" class="btn-nav"><?= isset($_GET['c']) ? "Otwarte projekty" : "Archiwizowane projekty" ?></a>
                <a href="przegladaj.php" class="btn-nav">Przeglądaj z filtrami</a>
                <a href="logout.php" class="btn-logout">Wyloguj</a>
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
                <h2>Pracownicy</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Imię i Nazwisko</th>
                            <th>ID Skanera</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while($employee = $sth_employees->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($employee['imie']) . " " . htmlspecialchars($employee['nazwisko']) . "</td>";
                                echo "<td>" . htmlspecialchars($employee['id_skaner']) . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<a href='edit_employee.php?id=" . $employee['id'] . "' class='btn-edit'>Edytuj</a>";
                                echo "<button onclick=\"confirmDelete('pracownika', " . $employee['id'] . ", '" . htmlspecialchars($employee['imie']) . " " . htmlspecialchars($employee['nazwisko']) . "')\" class='btn-delete'>Usuń</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
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
                                echo "<td>" . format_project_number($row['nazwa']) . ($row['opis'] != "" ? " - " . htmlspecialchars($row['opis']) : "") . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<a href='edit_project.php?id=" . $row['id'] . "' class='btn-edit'>Edytuj</a>";
                                echo "<button onclick=\"confirmDelete('projekt', " . $row['id'] . ", '" . format_project_number($row['nazwa']) . ($row['opis'] != "" ? " - " . htmlspecialchars($row['opis']) : "") . "')\" class='btn-delete'>Usuń</button>";
                                echo "<a href='akcja_zadania.php?" . (isset($_GET['c']) ? 'o' : 'z') . "&id=" . $row['id'] . "' class='btn-archive'>" . (isset($_GET['c']) ? 'Otwórz' : 'Archiwizuj') . "</a>";
                                if ($row['zaakceptowano'] == 0) {
                                    echo "<a href='akcja_zadania.php?a=1&id=" . $row['id'] . "' class='btn-approve'>Akceptuj</a>";
                                    echo "<a href='akcja_zadania.php?n=1&id=" . $row['id'] . "' class='btn-reject'>Odrzuć</a>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
        
        <!-- Delete Confirmation Modal -->
        <div id="delete-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <h2 id="modal-title">Usuń element</h2>
                <p id="modal-message">Czy na pewno chcesz usunąć ten element?</p>
                <div class="modal-actions">
                    <button id="confirm-delete-btn" class="btn-confirm">Tak, usuń</button>
                    <button onclick="closeModal()" class="btn-cancel">Anuluj</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>