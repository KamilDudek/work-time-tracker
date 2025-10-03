<?php
    require_once "db.php";

    session_start();

    // Handle CSV export first to avoid any HTML output
    if(isset($_POST['b_export'])) {
        // Suppress all error output for clean CSV
        error_reporting(0);
        ini_set('display_errors', 0);
        
        // Generate filename with timestamp
        $filename = "time_report_" . date('Y-m-d_H-i-s') . ".csv";
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for proper UTF-8 encoding in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV headers - fix deprecated fputcsv warning by adding escape parameter
        fputcsv($output, [
            'Pracownik',
            'Projekt',
            'Opis projektu', 
            'Symbol pracy',
            'Opis symbolu',
            'Data rozpoczęcia',
            'Data zakończenia',
            'Czas pracy (godz:min:sek)',
            'Czas pracy (godziny)',
            'Uwagi'
        ], ';', '"', '\\');
        
        // Rebuild query for export (same as display query)
        $export_query = "SELECT 
            pracownik.imie, 
            pracownik.nazwisko, 
            projekt.nazwa as projekt_nazwa, 
            projekt.opis as projekt_opis,
            symbol.symbol as symbol_kod,
            symbol.opis as symbol_opis,
            czas_pracy.poczatek, 
            czas_pracy.zakonczenie, 
            czas_pracy.czas,
            czas_pracy.uwagi,
            TIMEDIFF(czas_pracy.zakonczenie, czas_pracy.poczatek) as czas_formatted
        FROM czas_pracy 
        JOIN pracownik ON czas_pracy.id_pracownik = pracownik.id 
        JOIN projekt ON czas_pracy.id_projekt = projekt.id 
        JOIN symbol ON czas_pracy.id_symbol = symbol.id 
        WHERE 1=1 ";
        
        $export_tokens = array();
        
        if(isset($_POST['pracownik']) && $_POST['pracownik'] != "") {
            $export_query .= "AND pracownik.id = ? ";
            $export_tokens[] = $_POST['pracownik'];
        }
        if(isset($_POST['projekt']) && $_POST['projekt'] != "") {
            $export_query .= "AND projekt.id = ? ";
            $export_tokens[] = $_POST['projekt'];
        }
        if(isset($_POST['data1']) && $_POST['data1'] != "") {
            $export_query .= "AND czas_pracy.poczatek >= ? ";
            $export_tokens[] = $_POST['data1'] . " 00:00:00";
        }
        if(isset($_POST['data2']) && $_POST['data2'] != "") {
            $export_query .= "AND czas_pracy.zakonczenie <= ? ";
            $export_tokens[] = $_POST['data2'] . " 23:59:59";
        }
        
        $export_query .= "ORDER BY czas_pracy.poczatek ASC";
        
        $export_sth = $conn->prepare($export_query);
        $export_sth->execute($export_tokens);
        
        $total_hours = 0;
        $project_summaries = [];
        $worker_summaries = [];
        $data_rows = [];
        
        // Collect data and build summaries
        while($row = $export_sth->fetch(PDO::FETCH_ASSOC)) {
            $data_rows[] = $row;
            
            if($row['czas']) {
                $hours = floatval($row['czas']);
                $total_hours += $hours;
                
                // Project summary
                $project_key = format_project_number($row['projekt_nazwa']) . ' - ' . ($row['projekt_opis'] ?: 'Bez opisu');
                if(!isset($project_summaries[$project_key])) {
                    $project_summaries[$project_key] = 0;
                }
                $project_summaries[$project_key] += $hours;
                
                // Worker summary
                $worker_key = $row['imie'] . ' ' . $row['nazwisko'];
                if(!isset($worker_summaries[$worker_key])) {
                    $worker_summaries[$worker_key] = 0;
                }
                $worker_summaries[$worker_key] += $hours;
            }
        }
        
        // Output data rows
        foreach($data_rows as $row) {
            fputcsv($output, [
                $row['imie'] . ' ' . $row['nazwisko'],
                format_project_number($row['projekt_nazwa']),
                $row['projekt_opis'] ?: '',
                $row['symbol_kod'],
                $row['symbol_opis'],
                $row['poczatek'],
                $row['zakonczenie'] ?: 'W trakcie',
                $row['czas_formatted'] ?: '',
                $row['czas'] ?: '',
                $row['uwagi'] ?: ''
            ], ';', '"', '\\');
        }
        
        // Determine what summaries to show based on filters
        $show_project_summary = false;
        $show_worker_summary = false;
        
        // If filtering by date only (no specific worker or project)
        if((!isset($_POST['pracownik']) || $_POST['pracownik'] == "") && 
           (!isset($_POST['projekt']) || $_POST['projekt'] == "") &&
           ((isset($_POST['data1']) && $_POST['data1'] != "") || 
            (isset($_POST['data2']) && $_POST['data2'] != ""))) {
            $show_project_summary = true;
            $show_worker_summary = true;
        }
        // If filtering by worker only
        elseif((isset($_POST['pracownik']) && $_POST['pracownik'] != "") && 
               (!isset($_POST['projekt']) || $_POST['projekt'] == "")) {
            $show_project_summary = true;
        }
        // If filtering by project only  
        elseif((!isset($_POST['pracownik']) || $_POST['pracownik'] == "") && 
               (isset($_POST['projekt']) && $_POST['projekt'] != "")) {
            $show_worker_summary = true;
        }
        
        // Add summaries
        fputcsv($output, [], ';', '"', '\\'); // Empty row
        fputcsv($output, ['=== PODSUMOWANIA ==='], ';', '"', '\\');
        fputcsv($output, [], ';', '"', '\\'); // Empty row
        
        if($show_project_summary && count($project_summaries) > 0) {
            fputcsv($output, ['CZAS PRACY WG PROJEKTÓW:'], ';', '"', '\\');
            fputcsv($output, ['Projekt', 'Łączny czas (godziny)', 'Łączny czas (format)'], ';', '"', '\\');
            
            // Sort projects by hours (descending)
            arsort($project_summaries);
            
            foreach($project_summaries as $project => $hours) {
                $time_formatted = gmdate("H:i:s", $hours * 3600);
                fputcsv($output, [$project, number_format($hours, 1), $time_formatted], ';', '"', '\\');
            }
            fputcsv($output, [], ';', '"', '\\'); // Empty row
        }
        
        if($show_worker_summary && count($worker_summaries) > 0) {
            fputcsv($output, ['CZAS PRACY WG PRACOWNIKÓW:'], ';', '"', '\\');
            fputcsv($output, ['Pracownik', 'Łączny czas (godziny)', 'Łączny czas (format)'], ';', '"', '\\');
            
            // Sort workers by hours (descending)
            arsort($worker_summaries);
            
            foreach($worker_summaries as $worker => $hours) {
                $time_formatted = gmdate("H:i:s", $hours * 3600);
                fputcsv($output, [$worker, number_format($hours, 1), $time_formatted], ';', '"', '\\');
            }
            fputcsv($output, [], ';', '"', '\\'); // Empty row
        }
        
        // Overall summary
        fputcsv($output, ['PODSUMOWANIE OGÓŁEM:'], ';', '"', '\\');
        $total_time_formatted = gmdate("H:i:s", $total_hours * 3600);
        fputcsv($output, ['Łączny czas wszystkich prac:', number_format($total_hours, 1) . ' godzin', $total_time_formatted], ';', '"', '\\');
        fputcsv($output, ['Liczba rekordów:', count($data_rows)], ';', '"', '\\');
        
        // Add filter info
        fputcsv($output, [], ';', '"', '\\'); // Empty row
        fputcsv($output, ['=== ZASTOSOWANE FILTRY ==='], ';', '"', '\\');
        
        if(isset($_POST['pracownik']) && $_POST['pracownik'] != "") {
            // Get worker name
            $worker_query = $conn->prepare("SELECT imie, nazwisko FROM pracownik WHERE id = ?");
            $worker_query->execute([$_POST['pracownik']]);
            $worker_info = $worker_query->fetch(PDO::FETCH_ASSOC);
            if($worker_info) {
                fputcsv($output, ['Pracownik:', $worker_info['imie'] . ' ' . $worker_info['nazwisko']], ';', '"', '\\');
            }
        }
        
        if(isset($_POST['projekt']) && $_POST['projekt'] != "") {
            // Get project name
            $project_query = $conn->prepare("SELECT nazwa, opis FROM projekt WHERE id = ?");
            $project_query->execute([$_POST['projekt']]);
            $project_info = $project_query->fetch(PDO::FETCH_ASSOC);
            if($project_info) {
                fputcsv($output, ['Projekt:', format_project_number($project_info['nazwa']) . ' - ' . ($project_info['opis'] ?: 'Bez opisu')], ';', '"', '\\');
            }
        }
        
        if(isset($_POST['data1']) && $_POST['data1'] != "") {
            fputcsv($output, ['Data od:', $_POST['data1']], ';', '"', '\\');
        }
        
        if(isset($_POST['data2']) && $_POST['data2'] != "") {
            fputcsv($output, ['Data do:', $_POST['data2']], ';', '"', '\\');
        }
        
        fputcsv($output, ['Data eksportu:', date('Y-m-d H:i:s')], ';', '"', '\\');
        
        fclose($output);
        exit;
    }

    if(!isset($_SESSION['admin']) || $_SESSION['admin'] == "") {
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

    if(!isset($f_sort) || isset($_POST['b_no_filter'])) {
        $f_sort = array(
            "id" => "selected",
            "pracownik" => "",
            "projekt" => "",
            "start" => "",
            "koniec" => ""
        );

        $f_sort2 = array(
            "asc" => "selected",
            "desc" => ""
        );

        $f_pracownik = 0;
        $f_projekt = 0;
        $f_data1 = 0;
        $f_data2 = 0;
    }

    // Enhanced query with symbol information
    $harm = "SELECT 
        TIMEDIFF(czas_pracy.zakonczenie, czas_pracy.poczatek) as czas_formatted,
        czas_pracy.czas,
        pracownik.imie, 
        pracownik.nazwisko, 
        czas_pracy.poczatek, 
        czas_pracy.zakonczenie, 
        projekt.nazwa, 
        projekt.opis, 
        projekt.id,
        symbol.symbol as symbol_kod,
        symbol.opis as symbol_opis,
        czas_pracy.uwagi
    FROM czas_pracy 
    JOIN pracownik ON czas_pracy.id_pracownik = pracownik.id 
    JOIN projekt ON czas_pracy.id_projekt = projekt.id 
    JOIN symbol ON czas_pracy.id_symbol = symbol.id 
    WHERE 1=1 ";

    $tokens = array();

    if(isset($_POST['b_szukaj']) || isset($_POST['b_export'])) {
        // Handle form data for both search and export
        if(isset($_POST['sort'])) {
            foreach($sort as $key => $value) {
                if($_POST['sort'] == $key) {
                    $f_sort[$key] = "selected";
                } else {
                    $f_sort[$key] = "";
                }
            }
        }
        if(isset($_POST['sort2'])) {
            foreach($f_sort2 as $key => $value) {
                if($_POST['sort2'] == $key) {
                    $f_sort2[$key] = "selected";
                } else {
                    $f_sort2[$key] = "";
                }
            }
        }

        $f_pracownik = $_POST['pracownik'] ?? 0;
        $f_projekt = $_POST['projekt'] ?? 0;
        $f_data1 = $_POST['data1'] ?? 0;
        $f_data2 = $_POST['data2'] ?? 0;

        if($f_pracownik != "") {
            $harm = $harm . "AND pracownik.id = ? ";
            $tokens = array_merge($tokens, [$f_pracownik]);
        }
        if($f_projekt != "") {
            $harm = $harm . "AND projekt.id = ? ";
            $tokens = array_merge($tokens, [$f_projekt]);
        }
        if($f_data1 != "") {
            $harm = $harm . "AND czas_pracy.poczatek >= ? ";
            $tokens = array_merge($tokens, [$f_data1 . " 00:00:00"]);
        }
        if($f_data2 != "") {
            $harm = $harm . "AND czas_pracy.zakonczenie <= ? ";
            $tokens = array_merge($tokens, [$f_data2 . " 23:59:59"]);
        }

        $harm = $harm . "ORDER BY " . ((isset($_POST['sort'])) ? $sort[$_POST['sort']] : "czas_pracy.id") . ((isset($_POST['sort2']) ? " " . $_POST['sort2'] : " asc"));

        // Only execute query if not exporting (export has its own query)
        if(!isset($_POST['b_export'])) {
            $sth = $conn->prepare($harm);
            $sth->execute($tokens);
        }
    }
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
    <div class="misunia2">
    <nav><p>zalogowano jako <?= $_SESSION['admin'] ?></p><a href="admin_panel.php">panel</a><a href="logout.php">wyloguj</a> </nav>
    <div class="acojaktudammaxwidth"><h1>Filtrowanie danych czasu pracy</h1>
    <form method="post">
        <div class="filter-section">
            <p><strong>Filtr po pracowniku:</strong></p>
            <select name="pracownik">
                <option value="">-- Wszyscy pracownicy --</option>
<?php
                $sth4 = $conn->prepare("SELECT * FROM pracownik WHERE usunieto = 0 ORDER BY nazwisko, imie");
                $sth4->execute();
                $pracownicy = $sth4->fetchAll(PDO::FETCH_ASSOC);
                foreach ($pracownicy as $pracownik) {
                    echo "<option value='{$pracownik['id']}' " . (($f_pracownik == $pracownik['id']) ? 'selected' : '') . ">{$pracownik['imie']} {$pracownik['nazwisko']} (ID: {$pracownik['id_skaner']})</option>";
                }
?>
            </select>
        </div>
        
        <div class="filter-section">
            <p><strong>Filtr po projekcie:</strong></p>
            <select name="projekt">
                <option value="">-- Wszystkie projekty --</option>
<?php
                $sth4 = $conn->prepare("SELECT * FROM projekt WHERE skasowano = 0 ORDER BY nazwa");
                $sth4->execute();
                $projekty = $sth4->fetchAll(PDO::FETCH_ASSOC);
                foreach ($projekty as $projekt) {
                    echo "<option value='{$projekt['id']}' " . (($f_projekt == $projekt['id']) ? 'selected' : '') . ">" . format_project_number($projekt['nazwa']) . ($projekt['opis'] != "" ? " - " . $projekt['opis'] : "") . "</option>";
                }
?>
            </select>
        </div>
        
        <div class="filter-section">
            <p><strong>Zakres dat:</strong></p>
            <label>Od: <input type="date" name="data1" value="<?= (isset($f_data1) ? $f_data1 : "") ?>" /></label>
            <label>Do: <input type="date" name="data2" value="<?= (isset($f_data2) ? $f_data2 : "") ?>" /></label>
        </div>
        
        <div class="filter-section">
            <p><strong>Sortowanie:</strong></p>
            <select name="sort">
                <option value="id" <?= $f_sort['id'] ?>>Chronologicznie</option>
                <option value="pracownik" <?= $f_sort['pracownik'] ?>>Pracownik</option>
                <option value="projekt" <?= $f_sort['projekt'] ?>>Projekt</option>
                <option value="start" <?= $f_sort['start'] ?>>Data rozpoczęcia</option>
                <option value="koniec" <?= $f_sort['koniec'] ?>>Data zakończenia</option>
            </select>
            <select name="sort2">
                <option value="asc" <?= $f_sort2['asc'] ?>>Rosnąco</option>
                <option value="desc" <?= $f_sort2['desc'] ?>>Malejąco</option>
            </select>
        </div>
        
        <div class="button-section">
            <button type="submit" name="b_szukaj">🔍 Wyszukaj</button>
            <button type="submit" name="b_no_filter">❌ Usuń filtry</button>
        </div>
        
        <!-- Hidden fields to preserve search state for export -->
        <input type="hidden" name="search_performed" value="<?= isset($_POST['b_szukaj']) || isset($_POST['b_export']) ? '1' : '0' ?>" />
    </form>
    
    <!-- Separate form for export to preserve search parameters -->
    <?php if((isset($_POST['b_szukaj']) || isset($_POST['b_export'])) && isset($sth) && $sth->rowCount() > 0): ?>
    <form method="post" style="display: inline;">
        <input type="hidden" name="pracownik" value="<?= htmlspecialchars($f_pracownik) ?>" />
        <input type="hidden" name="projekt" value="<?= htmlspecialchars($f_projekt) ?>" />
        <input type="hidden" name="data1" value="<?= htmlspecialchars($f_data1) ?>" />
        <input type="hidden" name="data2" value="<?= htmlspecialchars($f_data2) ?>" />
        <input type="hidden" name="sort" value="<?= htmlspecialchars($_POST['sort'] ?? 'id') ?>" />
        <input type="hidden" name="sort2" value="<?= htmlspecialchars($_POST['sort2'] ?? 'asc') ?>" />
        <button type="submit" name="b_export" style="background-color: #28a745; margin-left: 10px; padding: 0.75rem 1.5rem; color: white; border: none; border-radius: 4px; cursor: pointer;">📊 Eksportuj do CSV</button>
    </form>
    <?php endif; ?>
    </form>
    </div>
    <div class="results-summary">
<?php
    if(isset($_POST['b_szukaj']) || (isset($_POST['search_performed']) && $_POST['search_performed'] == '1')) {
        // If export was clicked, we need to re-run the search query to display results
        if(isset($_POST['b_export'])) {
            $display_harm = "SELECT 
                TIMEDIFF(czas_pracy.zakonczenie, czas_pracy.poczatek) as czas_formatted,
                czas_pracy.czas,
                pracownik.imie, 
                pracownik.nazwisko, 
                czas_pracy.poczatek, 
                czas_pracy.zakonczenie, 
                projekt.nazwa, 
                projekt.opis, 
                projekt.id,
                symbol.symbol as symbol_kod,
                symbol.opis as symbol_opis,
                czas_pracy.uwagi
            FROM czas_pracy 
            JOIN pracownik ON czas_pracy.id_pracownik = pracownik.id 
            JOIN projekt ON czas_pracy.id_projekt = projekt.id 
            JOIN symbol ON czas_pracy.id_symbol = symbol.id 
            WHERE 1=1 ";
            
            $display_tokens = array();
            
            if(isset($_POST['pracownik']) && $_POST['pracownik'] != "") {
                $display_harm .= "AND pracownik.id = ? ";
                $display_tokens[] = $_POST['pracownik'];
            }
            if(isset($_POST['projekt']) && $_POST['projekt'] != "") {
                $display_harm .= "AND projekt.id = ? ";
                $display_tokens[] = $_POST['projekt'];
            }
            if(isset($_POST['data1']) && $_POST['data1'] != "") {
                $display_harm .= "AND czas_pracy.poczatek >= ? ";
                $display_tokens[] = $_POST['data1'] . " 00:00:00";
            }
            if(isset($_POST['data2']) && $_POST['data2'] != "") {
                $display_harm .= "AND czas_pracy.zakonczenie <= ? ";
                $display_tokens[] = $_POST['data2'] . " 23:59:59";
            }
            
            $display_harm .= "ORDER BY czas_pracy.poczatek ASC";
            
            $sth = $conn->prepare($display_harm);
            $sth->execute($display_tokens);
        }
        
        if(isset($sth)) {
            echo "<h3>📋 Wyniki wyszukiwania: {$sth->rowCount()} rekordów</h3>";
            if($f_pracownik != 0) {
                $worker_info = $conn->prepare("SELECT imie, nazwisko FROM pracownik WHERE id = ?");
                $worker_info->execute([$f_pracownik]);
                $worker = $worker_info->fetch(PDO::FETCH_ASSOC);
                if($worker) {
                    echo "<p><strong>Pracownik:</strong> {$worker['imie']} {$worker['nazwisko']}</p>";
                }
            }
            if($f_projekt != 0) {
                $project_info = $conn->prepare("SELECT nazwa, opis FROM projekt WHERE id = ?");
                $project_info->execute([$f_projekt]);
                $project = $project_info->fetch(PDO::FETCH_ASSOC);
                if($project) {
                    echo "<p><strong>Projekt:</strong> " . format_project_number($project['nazwa']) . ($project['opis'] ? " - " . $project['opis'] : "") . "</p>";
                }
            }
            if($f_data1 != 0 || $f_data2 != 0) {
                echo "<p><strong>Okres:</strong> ";
                if($f_data1 != 0) echo "od " . $f_data1 . " ";
                if($f_data2 != 0) echo "do " . $f_data2;
                echo "</p>";
            }
        }
    }
?>
    </div>
</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>👤 Pracownik</th>
                <th>📁 Projekt</th>
                <th>🔧 Typ pracy</th>
                <th>⏰ Rozpoczęcie</th>
                <th>⏹️ Zakończenie</th>
                <th>⏱️ Czas pracy</th>
                <th>💬 Uwagi</th>
            </tr>
        </thead>
        <tbody>
<?php
    $total_seconds = 0;
    $total_hours = 0;

    if(isset($_POST['b_szukaj']) || isset($_POST['b_export']) || (isset($_POST['search_performed']) && $_POST['search_performed'] == '1')) {
        if(isset($sth) && $sth->rowCount() > 0) {
            while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                // Calculate time for totals
                if($row['zakonczenie'] != "" && $row['czas']) {
                    $total_hours += floatval($row['czas']);
                }
                
                echo "<tr>";
                echo "<td>{$row['imie']} {$row['nazwisko']}</td>";
                echo "<td>" . format_project_number($row['nazwa']) . ($row['opis'] != "" ? "<br><small>" . htmlspecialchars($row['opis']) . "</small>" : "") . "</td>";
                echo "<td><strong>{$row['symbol_kod']}</strong><br><small>" . htmlspecialchars($row['symbol_opis']) . "</small></td>";
                echo "<td>" . date('Y-m-d H:i', strtotime($row['poczatek'])) . "</td>";
                echo "<td>" . ($row['zakonczenie'] ? date('Y-m-d H:i', strtotime($row['zakonczenie'])) : '<span style="color: orange;">W trakcie</span>') . "</td>";
                echo "<td><strong>" . ($row['czas_formatted'] ?: '-') . "</strong>" . ($row['czas'] ? "<br><small>(" . number_format($row['czas'], 1) . " h)</small>" : "") . "</td>";
                echo "<td>" . ($row['uwagi'] ? htmlspecialchars($row['uwagi']) : '-') . "</td>";
                echo "</tr>";
            }
            
            // Summary row
            if($total_hours > 0) {
                $total_time_formatted = gmdate("H:i:s", $total_hours * 3600);
                echo "<tr class='summary-row'>";
                echo "<td colspan='5'><strong>PODSUMOWANIE CZASU PRACY:</strong></td>";
                echo "<td><strong>{$total_time_formatted}</strong><br><small>(" . number_format($total_hours, 1) . " godzin)</small></td>";
                echo "<td></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7' style='text-align: center; color: #666;'>❌ Brak wyników dla podanych kryteriów</td></tr>";
        }
    } else {
        echo "<tr><td colspan='7' style='text-align: center; color: #666;'>🔍 Użyj filtrów powyżej, aby wyszukać dane</td></tr>";
    }
?>
        </tbody>
    </table>
</body>
</html>