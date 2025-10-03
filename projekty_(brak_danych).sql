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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `czas_pracy`
--
ALTER TABLE `czas_pracy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `klient`
--
ALTER TABLE `klient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pracownik`
--
ALTER TABLE `pracownik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projekt`
--
ALTER TABLE `projekt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `symbol`
--
ALTER TABLE `symbol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
