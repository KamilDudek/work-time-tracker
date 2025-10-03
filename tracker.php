<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja Czasu Pracy</title>
    <link rel="stylesheet" href="tracker.css">
</head>
<body>
<div class="app-container">
    <header class="tracker-header">
        <img src="http://www.dd-tech.pl/images/logo_min.png" alt="Logo" class="header-logo">
        <h1>Rejestracja Czasu Pracy</h1>
        <div class="user-info">
            <span>Witaj, <strong>[Imię Pracownika]</strong>!</span>
            <a href="logout.php" class="btn-logout">Wyloguj</a>
        </div>
    </header>

    <main class="tracker-main">
        <div class="timer-card">
            <div class="project-selection">
                <h2>Wybierz Projekt</h2>
                <div class="form-group">
                    <label for="project-search">Wyszukaj projekt (numer lub nazwa)</label>
                    <input type="text" id="project-search" list="project-list" placeholder="Zacznij pisać, aby wyszukać...">
                    <datalist id="project-list">
                        <!-- Opcje będą ładowane dynamicznie -->
                    </datalist>
                </div>
            </div>

            <div class="timer-controls">
                <div id="timer-display">00:00:00</div>
                <button id="start-btn" class="btn-start">Start</button>
                <button id="stop-btn" class="btn-stop" disabled>Stop</button>
            </div>
        </div>
    </main>
</div>

<div id="stop-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h2>Zatrzymać pracę?</h2>
        <p>Czy na pewno chcesz zakończyć rejestrację czasu dla tego zadania?</p>
        <div class="form-group">
            <label for="work-description">Dodaj opis (opcjonalnie)</label>
            <textarea id="work-description" rows="3" placeholder="Opisz, co zostało zrobione..."></textarea>
        </div>
        <div class="modal-actions">
            <button id="confirm-stop-btn" class="btn-confirm">Tak, zatrzymaj</button>
            <button id="cancel-stop-btn" class="btn-cancel">Anuluj</button>
        </div>
    </div>
</div>

<script src="tracker.js"></script>
</body>
</html>