<?php
require_once "db.php";
session_start();

header('Content-Type: application/json');

$response = [];

if (!isset($_SESSION['pracownik_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'Brak autoryzacji. Proszę się zalogować.';
    http_response_code(401);
    echo json_encode($response);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_initial_data':
            // Get employee name
            $sth = $conn->prepare("SELECT imie, nazwisko FROM pracownik WHERE id = ?");
            $sth->execute([$_SESSION['pracownik_id']]);
            $employee = $sth->fetch(PDO::FETCH_ASSOC);
            $response['employeeName'] = $employee ? "{$employee['imie']} {$employee['nazwisko']}" : "Pracownik";

            // Get projects
            $sth = $conn->prepare("SELECT id, nazwa, opis FROM projekt WHERE zakonczono = 0 AND zaakceptowano = 1 ORDER BY nazwa ASC");
            $sth->execute();
            $response['projects'] = $sth->fetchAll(PDO::FETCH_ASSOC);
            
            // Check for ongoing work
            $sth = $conn->prepare("SELECT id, id_projekt, poczatek FROM czas_pracy WHERE id_pracownik = ? AND zakonczenie IS NULL");
            $sth->execute([$_SESSION['pracownik_id']]);
            $ongoingWork = $sth->fetch(PDO::FETCH_ASSOC);
            if ($ongoingWork) {
                $response['ongoingWork'] = [
                    'workEntryId' => $ongoingWork['id'],
                    'projectId' => $ongoingWork['id_projekt'],
                    'startTime' => strtotime($ongoingWork['poczatek'])
                ];
            }

            $response['status'] = 'success';
            break;

        case 'start_work':
            $data = json_decode(file_get_contents('php://input'), true);
            $projectId = $data['projectId'] ?? null;

            if (!$projectId) {
                throw new Exception("Nie podano ID projektu.");
            }
            
            // Get the first available symbol from the database
            $symbol_sth = $conn->prepare("SELECT id FROM symbol WHERE usunieto = 0 ORDER BY id ASC LIMIT 1");
            $symbol_sth->execute();
            $symbol = $symbol_sth->fetch(PDO::FETCH_ASSOC);

            if (!$symbol) {
                throw new Exception("Brak zdefiniowanych typów pracy (symboli) w systemie.");
            }
            $symbol_id = $symbol['id'];

            $sth = $conn->prepare("INSERT INTO czas_pracy (id_projekt, id_symbol, id_pracownik, poczatek, zakonczenie, zaakceptowano) VALUES (?, ?, ?, ?, NULL, 0)");
            $sth->execute([$projectId, $symbol_id, $_SESSION['pracownik_id'], date('Y-m-d H:i:s')]);
            
            $response['status'] = 'success';
            $response['workEntryId'] = $conn->lastInsertId();
            $response['message'] = 'Praca rozpoczęta.';
            break;

        case 'stop_work':
            $data = json_decode(file_get_contents('php://input'), true);
            $workEntryId = $data['workEntryId'] ?? null;
            $description = $data['description'] ?? '';

            if (!$workEntryId) {
                throw new Exception("Brak ID wpisu pracy.");
            }

            $sth = $conn->prepare("UPDATE czas_pracy SET zakonczenie = ?, uwagi = ? WHERE id = ? AND id_pracownik = ?");
            $sth->execute([date('Y-m-d H:i:s'), $description, $workEntryId, $_SESSION['pracownik_id']]);

            if ($sth->rowCount() > 0) {
                $response['status'] = 'success';
                $response['message'] = 'Praca zakończona.';
            } else {
                throw new Exception("Nie udało się zatrzymać pracy. Wpis nie został znaleziony.");
            }
            break;

        default:
            throw new Exception("Nieznana akcja.");
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
