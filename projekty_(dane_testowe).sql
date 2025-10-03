-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Lip 03, 2025 at 09:29 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projekty`
--

drop database projekty;
create database if not exists projekty;
use projekty;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `login` varchar(32) DEFAULT NULL,
  `passwd` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `login`, `passwd`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `czas_pracy`
--

CREATE TABLE `czas_pracy` (
  `id` int(11) NOT NULL,
  `id_projekt` int(11) DEFAULT NULL,
  `id_symbol` int(11) DEFAULT NULL,
  `id_pracownik` int(11) DEFAULT NULL,
  `uwagi` varchar(128) DEFAULT NULL,
  `poczatek` timestamp NOT NULL DEFAULT current_timestamp(),
  `zakonczenie` timestamp NULL DEFAULT NULL,
  `czas` decimal(5,1) DEFAULT NULL,
  `zaakceptowano` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `czas_pracy`
--

INSERT INTO `czas_pracy` (`id`, `id_projekt`, `id_symbol`, `id_pracownik`, `uwagi`, `poczatek`, `zakonczenie`, `czas`, `zaakceptowano`) VALUES
(277, 51, 13, 115, 'Montaż podstawowych elementów', '2025-09-01 08:00:00', '2025-09-01 12:00:00', 4.0, 1),
(278, 51, 8, 115, 'Praca nad dokumentacją techniczną', '2025-09-01 13:00:00', '2025-09-01 17:00:00', 4.0, 1),
(279, 51, 12, 115, 'Opracowanie dokumentacji projektu', '2025-09-02 08:30:00', '2025-09-02 11:30:00', 3.0, 1),
(280, 52, 9, 116, 'Projektowanie nowej funkcjonalności', '2025-09-02 09:00:00', '2025-09-02 17:00:00', 8.0, 1),
(281, 52, 8, 116, 'Implementacja modułu płatności', '2025-09-03 08:00:00', '2025-09-03 16:00:00', 8.0, 1),
(282, 58, 9, 114, 'Analiza wymagań klienta', '2025-09-03 10:00:00', '2025-09-03 14:00:00', 4.0, 1),
(283, 58, 12, 114, 'Tworzenie specyfikacji technicznej', '2025-09-04 08:00:00', '2025-09-04 12:00:00', 4.0, 1),
(284, 66, 13, 117, 'Montaż serwerów w data center', '2025-09-04 09:00:00', '2025-09-04 18:00:00', 9.0, 1),
(285, 66, 8, 117, 'Konfiguracja oprogramowania serwerowego', '2025-09-05 08:00:00', '2025-09-05 17:00:00', 9.0, 1),
(286, 51, 9, 118, 'Projektowanie interfejsu użytkownika', '2025-09-05 09:00:00', '2025-09-05 13:00:00', 4.0, 1),
(287, 51, 8, 118, 'Kodowanie frontendu aplikacji', '2025-09-06 08:00:00', '2025-09-06 16:00:00', 8.0, 1),
(288, 62, 12, 119, 'Przygotowanie dokumentacji użytkownika', '2025-09-06 10:00:00', '2025-09-06 15:00:00', 5.0, 1),
(289, 62, 9, 119, 'Projektowanie bazy danych', '2025-09-09 08:00:00', '2025-09-09 17:00:00', 9.0, 1),
(290, 58, 13, 120, 'Instalacja sprzętu sieciowego', '2025-09-09 09:00:00', '2025-09-09 17:30:00', 8.5, 1),
(291, 58, 8, 120, 'Konfiguracja routerów i switchy', '2025-09-10 08:00:00', '2025-09-10 16:00:00', 8.0, 1),
(292, 66, 9, 121, 'Analiza wydajności systemu', '2025-09-10 10:00:00', '2025-09-10 14:00:00', 4.0, 1),
(293, 66, 12, 121, 'Raport z testów wydajnościowych', '2025-09-11 08:00:00', '2025-09-11 12:00:00', 4.0, 1),
(294, 52, 13, 115, 'Montaż komponentów elektronicznych', '2025-09-11 13:00:00', '2025-09-11 17:00:00', 4.0, 1),
(295, 52, 8, 115, 'Programowanie mikrokontrolerów', '2025-09-12 08:00:00', '2025-09-12 17:00:00', 9.0, 1),
(296, 51, 12, 116, 'Aktualizacja dokumentacji projektu', '2025-09-12 09:00:00', '2025-09-12 13:00:00', 4.0, 1),
(297, 62, 9, 117, 'Projektowanie architektury systemu', '2025-09-13 08:00:00', '2025-09-13 16:00:00', 8.0, 1),
(298, 62, 8, 117, 'Implementacja API RESTful', '2025-09-13 09:00:00', '2025-09-13 18:00:00', 9.0, 1),
(299, 58, 9, 118, 'Projektowanie interfejsów sieciowych', '2025-09-16 08:00:00', '2025-09-16 16:00:00', 8.0, 1),
(300, 58, 12, 118, 'Dokumentacja konfiguracji sieci', '2025-09-16 10:00:00', '2025-09-16 15:00:00', 5.0, 1),
(301, 66, 13, 119, 'Instalacja systemów bezpieczeństwa', '2025-09-17 08:00:00', '2025-09-17 17:00:00', 9.0, 1),
(302, 66, 8, 119, 'Konfiguracja firewalli i VPN', '2025-09-17 09:00:00', '2025-09-17 18:00:00', 9.0, 1),
(303, 51, 9, 120, 'Optymalizacja algorytmów', '2025-09-18 08:00:00', '2025-09-18 16:00:00', 8.0, 1),
(304, 51, 12, 120, 'Przygotowanie raportów technicznych', '2025-09-18 10:00:00', '2025-09-18 14:00:00', 4.0, 1),
(305, 52, 9, 121, 'Projektowanie modułów integracyjnych', '2025-09-19 08:00:00', '2025-09-19 17:00:00', 9.0, 1),
(306, 52, 13, 114, 'Montaż prototypu urządzenia', '2025-09-19 09:00:00', '2025-09-19 16:00:00', 7.0, 1),
(307, 62, 8, 115, 'Implementacja systemu logowania', '2025-09-20 08:00:00', '2025-09-20 17:00:00', 9.0, 1),
(308, 62, 12, 115, 'Dokumentacja systemu logowania', '2025-09-20 10:00:00', '2025-09-20 13:00:00', 3.0, 1),
(309, 58, 9, 116, 'Projektowanie topologii sieci', '2025-09-23 08:00:00', '2025-09-23 16:00:00', 8.0, 1),
(310, 58, 13, 116, 'Instalacja przewodów sieciowych', '2025-09-23 09:00:00', '2025-09-23 18:00:00', 9.0, 1),
(311, 66, 8, 117, 'Aktualizacja oprogramowania serwerów', '2025-09-24 08:00:00', '2025-09-24 16:00:00', 8.0, 1),
(312, 66, 12, 117, 'Raport z aktualizacji systemów', '2025-09-24 10:00:00', '2025-09-24 14:00:00', 4.0, 1),
(313, 51, 8, 118, 'Refaktoryzacja kodu aplikacji', '2025-09-25 08:00:00', '2025-09-25 17:00:00', 9.0, 1),
(314, 51, 9, 118, 'Projektowanie nowych funkcji', '2025-09-25 09:00:00', '2025-09-25 15:00:00', 6.0, 1),
(315, 52, 12, 119, 'Przygotowanie instrukcji obsługi', '2025-09-26 08:00:00', '2025-09-26 16:00:00', 8.0, 1),
(316, 52, 13, 119, 'Testy integracyjne urządzenia', '2025-09-26 10:00:00', '2025-09-26 17:00:00', 7.0, 1),
(317, 62, 9, 120, 'Analiza wymagań bezpieczeństwa', '2025-09-27 08:00:00', '2025-09-27 16:00:00', 8.0, 1),
(318, 62, 8, 120, 'Implementacja zabezpieczeń', '2025-09-27 09:00:00', '2025-09-27 18:00:00', 9.0, 1),
(319, 58, 12, 121, 'Dokumentacja infrastruktury IT', '2025-09-30 08:00:00', '2025-09-30 16:00:00', 8.0, 1),
(320, 58, 9, 121, 'Projektowanie disaster recovery', '2025-09-30 10:00:00', '2025-09-30 17:00:00', 7.0, 1),
(321, 66, 13, 114, 'Instalacja systemów monitoringu', '2025-10-01 08:00:00', '2025-10-01 17:00:00', 9.0, 1),
(322, 66, 8, 114, 'Konfiguracja narzędzi monitoringu', '2025-10-01 09:00:00', '2025-10-01 16:00:00', 7.0, 1),
(323, 51, 12, 115, 'Finalizacja dokumentacji projektu', '2025-10-02 08:00:00', '2025-10-02 15:00:00', 7.0, 1),
(324, 51, 9, 115, 'Przegląd końcowy projektu', '2025-10-02 10:00:00', '2025-10-02 14:00:00', 4.0, 1),
(325, 52, 8, 116, 'Ostatnie poprawki w kodzie', '2025-10-02 08:00:00', '2025-10-02 17:00:00', 9.0, 1),
(326, 62, 13, 117, 'Testy akceptacyjne systemu', '2025-10-02 09:00:00', '2025-10-02 18:00:00', 9.0, 1),
(327, 58, 8, 118, 'Optymalizacja wydajności sieci', '2025-10-02 10:00:00', '2025-10-02 16:00:00', 6.0, 1),
(328, 66, 9, 119, 'Planowanie przyszłych rozszerzeń', '2025-10-02 08:00:00', '2025-10-02 17:00:00', 9.0, 1),
(329, 51, 13, 120, 'Końcowe testy sprzętowe', '2025-10-02 09:00:00', NULL, NULL, 0),
(330, 52, 12, 121, 'Aktualizacja dokumentacji końcowej', '2025-10-02 10:00:00', NULL, NULL, 0);

--
-- Wyzwalacze `czas_pracy`
--
DELIMITER $$
CREATE TRIGGER `oblicz_czas_przed_insert` BEFORE INSERT ON `czas_pracy` FOR EACH ROW BEGIN
  IF NEW.poczatek IS NOT NULL AND NEW.zakonczenie IS NOT NULL THEN
    SET NEW.czas = ROUND(TIMESTAMPDIFF(MINUTE, NEW.poczatek, NEW.zakonczenie) / 60, 1);
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `oblicz_czas_przed_update` BEFORE UPDATE ON `czas_pracy` FOR EACH ROW BEGIN
  IF NEW.poczatek IS NOT NULL AND NEW.zakonczenie IS NOT NULL THEN
    SET NEW.czas = ROUND(TIMESTAMPDIFF(MINUTE, NEW.poczatek, NEW.zakonczenie) / 60, 1);
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klient`
--

CREATE TABLE `klient` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(64) DEFAULT NULL,
  `usunieto` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownik`
--

CREATE TABLE `pracownik` (
  `id` int(11) NOT NULL,
  `id_skaner` int(11) DEFAULT NULL,
  `imie` varchar(32) DEFAULT NULL,
  `nazwisko` varchar(32) DEFAULT NULL,
  `usunieto` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pracownik`
--

INSERT INTO `pracownik` (`id`, `id_skaner`, `imie`, `nazwisko`, `usunieto`) VALUES
(114, 12560000, 'Mariusz', 'Kowalski', 0),
(115, 11111111, 'Anna', 'Nowak', 0),
(116, 22222222, 'Piotr', 'Wiśniewski', 0),
(117, 22222221, 'Katarzyna', 'Wójcik', 0),
(118, 98727777, 'Marek', 'Kowalczyk', 0),
(119, 23465555, 'Agnieszka', 'Kamińska', 0),
(120, 12345678, 'Tomasz', 'Lewandowski', 0),
(121, 44444444, 'Magdalena', 'Zielińska', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `projekt`
--

CREATE TABLE `projekt` (
  `id` int(11) NOT NULL,
  `nazwa` int(4) NOT NULL,
  `opis` varchar(64) DEFAULT NULL,
  `id_klient` int(11) DEFAULT NULL,
  `zakonczono` tinyint(1) DEFAULT 0,
  `zaakceptowano` tinyint(1) DEFAULT 0,
  `skasowano` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projekt`
--

INSERT INTO `projekt` (`id`, `nazwa`, `opis`, `id_klient`, `zakonczono`, `zaakceptowano`, `skasowano`) VALUES
(51, 1237, 'System CRM dla firmy handlowej', NULL, 0, 1, 0),
(52, 3213, 'Aplikacja mobilna e-commerce', NULL, 1, 1, 0),
(56, 5789, 'Portal internetowy korporacyjny', NULL, 1, 1, 0),
(58, 3456, 'Infrastruktura sieciowa biura', NULL, 0, 1, 0),
(59, 8574, 'System zarządzania magazynem', NULL, 1, 1, 0),
(60, 9576, 'Platforma e-learningowa', NULL, 1, 1, 0),
(61, 2349, 'System księgowy online', NULL, 0, 1, 0),
(62, 1345, 'Aplikacja do zarządzania projektami', NULL, 0, 1, 0),
(63, 2309, 'System bezpieczeństwa IT', NULL, 0, 1, 0),
(66, 7654, 'Modernizacja serwerowni', NULL, 0, 1, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `symbol`
--

CREATE TABLE `symbol` (
  `id` int(11) NOT NULL,
  `symbol` int(4) DEFAULT NULL,
  `opis` varchar(32) DEFAULT NULL,
  `usunieto` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `symbol`
--

INSERT INTO `symbol` (`id`, `symbol`, `opis`, `usunieto`) VALUES
(8, 8792, 'Programowanie/Implementacja', 0),
(9, 2859, 'Projektowanie/Analiza', 0),
(12, 2624, 'Dokumentacja', 0),
(13, 5656, 'Instalacja/Montaż', 0);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `czas_pracy`
--
ALTER TABLE `czas_pracy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_projekt` (`id_projekt`),
  ADD KEY `fk_id_pracownik` (`id_pracownik`),
  ADD KEY `fk_id_projekt` (`id_symbol`);

--
-- Indeksy dla tabeli `klient`
--
ALTER TABLE `klient`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `pracownik`
--
ALTER TABLE `pracownik`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `projekt`
--
ALTER TABLE `projekt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_projekt_id_klient` (`id_klient`);

--
-- Indeksy dla tabeli `symbol`
--
ALTER TABLE `symbol`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `czas_pracy`
--
ALTER TABLE `czas_pracy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=331;

--
-- AUTO_INCREMENT for table `klient`
--
ALTER TABLE `klient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pracownik`
--
ALTER TABLE `pracownik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `projekt`
--
ALTER TABLE `projekt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `symbol`
--
ALTER TABLE `symbol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `czas_pracy`
--
ALTER TABLE `czas_pracy`
  ADD CONSTRAINT `czas_pracy_ibfk_1` FOREIGN KEY (`id_projekt`) REFERENCES `projekt` (`id`),
  ADD CONSTRAINT `fk_id_pracownik` FOREIGN KEY (`id_pracownik`) REFERENCES `pracownik` (`id`),
  ADD CONSTRAINT `fk_id_projekt` FOREIGN KEY (`id_symbol`) REFERENCES `symbol` (`id`);

--
-- Constraints for table `projekt`
--
ALTER TABLE `projekt`
  ADD CONSTRAINT `fk_projekt_id_klient` FOREIGN KEY (`id_klient`) REFERENCES `klient` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
