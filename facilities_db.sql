-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Gegenereerd op: 05 dec 2025 om 15:10
-- Serverversie: 5.7.39
-- PHP-versie: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `facilities_db`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `facilities`
--

CREATE TABLE `facilities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location_id` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tag_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `facilities`
--

INSERT INTO `facilities` (`id`, `name`, `location_id`, `created`, `tag_id`) VALUES
(13, 'Koninklijk paleis Amsterdam', 27, '2025-12-05 14:25:44', 11),
(16, 'Ajax', 28, '2025-12-05 16:05:44', 12);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `facilities_tags`
--

CREATE TABLE `facilities_tags` (
  `facility_tag_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `facilities_tags`
--

INSERT INTO `facilities_tags` (`facility_tag_id`, `tag_id`) VALUES
(11, 2),
(12, 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zip_code` varchar(15) NOT NULL,
  `country_code` varchar(8) NOT NULL,
  `phone_number` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `locations`
--

INSERT INTO `locations` (`id`, `city`, `address`, `zip_code`, `country_code`, `phone_number`) VALUES
(27, 'Hallo', 'Nieuwezijds Voorburgwal 147', '1102AA', '031', '0612345678'),
(28, 'Amsterdam', 'Postjesweg', '1055AA', '031', '0612345678');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(1, 'Cheese Toastie'),
(2, 'West');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `facilities_tags` (`tag_id`),
  ADD KEY `location` (`location_id`);

--
-- Indexen voor tabel `facilities_tags`
--
ALTER TABLE `facilities_tags`
  ADD UNIQUE KEY `facility_id_2` (`facility_tag_id`,`tag_id`),
  ADD KEY `facility_id` (`facility_tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexen voor tabel `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT voor een tabel `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT voor een tabel `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `facilities`
--
ALTER TABLE `facilities`
  ADD CONSTRAINT `facilities_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Beperkingen voor tabel `facilities_tags`
--
ALTER TABLE `facilities_tags`
  ADD CONSTRAINT `facility_tag_id` FOREIGN KEY (`facility_tag_id`) REFERENCES `facilities` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
