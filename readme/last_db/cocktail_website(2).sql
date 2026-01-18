-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 07. Jan 2026 um 08:04
-- Server-Version: 10.4.28-MariaDB
-- PHP-Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `cocktail_website`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `barkeeper_applications`
--

CREATE TABLE `barkeeper_applications` (
  `app_id` int(11) NOT NULL,
  `userid` int(11) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `barkeeper_applications`
--

INSERT INTO `barkeeper_applications` (`app_id`, `userid`, `full_name`, `document_path`, `status`, `created_at`) VALUES
(1, 2, 'John Pork', 'resources/uploads/verification/verify_2_1767661414.webp', 'approved', '2026-01-06 01:03:34');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(4, 'Alkoholfrei'),
(2, 'Erfrischend'),
(3, 'Fruchtig'),
(1, 'Klassiker'),
(7, 'Party'),
(5, 'Sommer'),
(6, 'Winter');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `favorites`
--

INSERT INTO `favorites` (`user_id`, `recipe_id`, `created_at`) VALUES
(2, 11, '2026-01-06 00:54:04');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredient_id` int(10) UNSIGNED NOT NULL,
  `ingredient_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `ingredient_name`) VALUES
(64, '1 Barlöffel Zucker'),
(18, '100ml Ananassaft'),
(45, '10ml Mandelsirup'),
(76, '120ml Orangensaft'),
(52, '150ml Ginger Beer'),
(69, '15ml Gin'),
(44, '15ml Orangenlikör'),
(72, '15ml Tequila'),
(73, '15ml Triple Sec'),
(71, '15ml weißer Rum'),
(70, '15ml Wodka'),
(63, '2 Spritzer Angostura Bitter'),
(13, '2 TL Zucker'),
(46, '20ml frischer Limettensaft'),
(77, '20ml Grenadine'),
(58, '20ml Kaffeelikör'),
(51, '20ml Limettensaft'),
(55, '20ml Zuckersirup'),
(49, '30ml Campari'),
(57, '30ml frischer Espresso'),
(47, '30ml Gin'),
(12, '30ml Limettensaft'),
(48, '30ml Roter Wermut'),
(54, '30ml Zitronensaft'),
(17, '50ml Kokoscreme'),
(11, '50ml weißer Rum'),
(22, '50ml Wodka'),
(66, '60ml Aperol'),
(53, '60ml Bourbon Whiskey'),
(43, '60ml brauner Rum'),
(75, '60ml Tequila'),
(59, '60ml weißer Rum'),
(67, '90ml Prosecco'),
(20, 'Ananasstück zum Garnieren'),
(30, 'Banane'),
(21, 'Cocktailkirsche zum Garnieren'),
(74, 'Cola'),
(25, 'e'),
(19, 'Eiswürfel'),
(29, 'Erdbeeren'),
(31, 'frische Minzblätter'),
(33, 'Honig'),
(1, 'Limetten'),
(14, 'Minzblätter'),
(32, 'Naturjoghurt'),
(10, 'rtert'),
(68, 'Schuss Mineralwasser'),
(65, 'Schuss Soda'),
(15, 'Soda'),
(28, 'we'),
(6, 'wewerwe'),
(3, 'wterz'),
(2, 'wtwet'),
(35, 'Zitronensaft');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ratings`
--

CREATE TABLE `ratings` (
  `userid` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `stars` tinyint(4) NOT NULL CHECK (`stars` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `ratings`
--

INSERT INTO `ratings` (`userid`, `recipe_id`, `stars`, `created_at`) VALUES
(2, 10, 3, '2026-01-06 00:45:01'),
(2, 11, 1, '2026-01-06 00:41:04'),
(2, 20, 5, '2026-01-06 00:43:40'),
(3, 11, 5, '2026-01-06 00:39:12');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `recipes`
--

CREATE TABLE `recipes` (
  `recipe_id` int(11) UNSIGNED NOT NULL,
  `recipe_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `beschreibung` varchar(255) DEFAULT NULL,
  `anleitung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `recipes`
--

INSERT INTO `recipes` (`recipe_id`, `recipe_name`, `beschreibung`, `anleitung`, `created_by`, `created_at`) VALUES
(10, 'Mojito', 'Klassischer, erfrischender Minz-Cocktail aus Kuba.', 'Limetten auspressen, Minze leicht zerdrücken, Zucker & Rum hinzufügen, Mit Soda auffüllen, Eiswürfel hinzufügen, Umrühren', 2, '2026-01-05 01:11:18.985594'),
(11, 'Piña Colada', 'Ein tropischer Klassiker mit Ananas, Kokos und Rum – perfekt für den Sommer.', 'Eiswürfel in den Mixer geben, Rum, Kokoscreme und Ananassaft hinzufügen, Alles mixen bis cremig, In ein Glas füllen, Mit Ananasstück und Cocktailkirsche garnieren, Sofort servieren', 2, '2026-01-05 15:46:23.681071'),
(20, 'Sommerlicher Erdbeer-Minz-Smoothie', 'Ein erfrischender, fruchtiger Smoothie mit süßen Erdbeeren und frischer Minze – perfekt für heiße Sommertage.', 'Erdbeeren waschen und halbieren, Banane schälen und in Stücke schneiden, Minzblätter grob hacken, alle Zutaten in einen Mixer geben, gut pürieren bis eine cremige Konsistenz entsteht, bei Bedarf mit Honig nachsüßen, in Gläser füllen, mit Minzblatt garnieren, sofort servieren', 2, '2026-01-05 17:17:48.651291'),
(21, 'Mai Tai', 'Der ultimative Tiki-Klassiker mit intensivem Mandelaroma.', 'Rum, Limette und Mandelsirup shaken, Auf Crushed Ice abseihen, Mit Minze garnieren', 2, '2026-01-06 01:17:39.359470'),
(22, 'Negroni', 'Ein herber, italienischer Aperitif für Genießer.', 'Gin, Vermouth und Campari mischen, Auf Eis rühren, Orangenzeste hinzufügen', 2, '2026-01-06 01:17:39.376650'),
(23, 'Moscow Mule', 'Erfrischend scharf durch Ingwer und Limette.', 'Wodka und Limette ins Glas, Eis hinzufügen, Mit Ginger Beer auffüllen', 2, '2026-01-06 01:17:39.384748'),
(24, 'Whiskey Sour', 'Die perfekte Balance zwischen süß und sauer.', 'Whiskey, Zitrone und Zucker shaken, Eiswürfel dazu, In den Tumbler abseihen', 2, '2026-01-06 01:17:39.392223'),
(25, 'Espresso Martini', 'Der perfekte Wachmacher für die Nacht.', 'Espresso kochen und kühlen, Mit Wodka und Likör hart shaken, In Martini-Schale füllen', 2, '2026-01-06 01:17:39.396060'),
(26, 'Daiquiri', 'Minimalistisch und erfrischend – Rum pur erleben.', 'Rum, Limette, Sirup in den Shaker, Eiskalt shaken, Ohne Eis servieren', 2, '2026-01-06 01:17:39.400688'),
(27, 'Old Fashioned', 'Der zeitlose Klassiker unter den Whiskey-Drinks.', 'Zucker mit Bitter auflösen, Whiskey und Eis zugeben, Lange rühren', 2, '2026-01-06 01:17:39.405001'),
(28, 'Aperol Spritz', 'Der Inbegriff des italienischen Sommers.', 'Glas mit Eis füllen, Prosecco und Aperol eingießen, Spritzer Soda dazu', 2, '2026-01-06 01:17:39.407723'),
(29, 'Long Island Ice Tea', 'Vielseitig, stark und überraschend erfrischend.', 'Alle 5 klaren Geister mischen, Zitronensaft dazu, Mit Cola auffüllen', 2, '2026-01-06 01:17:39.411489'),
(30, 'Tequila Sunrise', 'Wunderschöner Farbverlauf wie ein Sonnenaufgang.', 'Tequila und O-Saft mischen, Eis ins Glas, Grenadine vorsichtig einsinken lassen', 2, '2026-01-06 01:17:39.416825');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `recipe_categories`
--

CREATE TABLE `recipe_categories` (
  `recipe_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `recipe_categories`
--

INSERT INTO `recipe_categories` (`recipe_id`, `category_id`) VALUES
(10, 1),
(10, 2),
(10, 5),
(11, 1),
(11, 3),
(11, 5),
(11, 6),
(20, 2),
(20, 3),
(20, 4),
(20, 5),
(20, 7),
(21, 1),
(21, 5),
(21, 7),
(22, 1),
(22, 6),
(23, 1),
(23, 2),
(23, 5),
(24, 1),
(24, 3),
(25, 1),
(25, 7),
(26, 1),
(26, 2),
(26, 5),
(27, 1),
(27, 6),
(28, 1),
(28, 2),
(28, 5),
(29, 1),
(29, 5),
(29, 7),
(30, 3),
(30, 5),
(30, 7);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `recipe_images`
--

CREATE TABLE `recipe_images` (
  `image_id` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `recipe_images`
--

INSERT INTO `recipe_images` (`image_id`, `recipe_id`, `image_path`) VALUES
(1, 10, 'resources/images/user_uploads/mojito_20260105_021118.jpg'),
(10, 20, 'resources/images/user_uploads/Sommerlicher_Erdbeer_Minz_Smoothie_Bild_1_User_2_1767633468.jpg'),
(11, 10, 'resources/uploads/recipes/1767660536_2_banner.webp'),
(12, 11, 'resources/uploads/recipes/1767661889_3_pinacolada.jpeg'),
(23, 30, 'resources/uploads/recipes/recipe_30_1767663045.jpg'),
(24, 29, 'resources/uploads/recipes/recipe_29_1767663059.webp'),
(25, 28, 'resources/uploads/recipes/recipe_28_1767663066.jpg'),
(26, 27, 'resources/uploads/recipes/recipe_27_1767663075.jpg'),
(27, 26, 'resources/uploads/recipes/recipe_26_1767663083.webp'),
(28, 25, 'resources/uploads/recipes/recipe_25_1767663093.jpg'),
(29, 24, 'resources/uploads/recipes/recipe_24_1767663104.jpg'),
(30, 23, 'resources/uploads/recipes/recipe_23_1767663117.jpg'),
(31, 21, 'resources/uploads/recipes/recipe_21_1767663127.webp'),
(32, 22, 'resources/uploads/recipes/recipe_22_1767663143.jpg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `ingredient_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `recipe_ingredients`
--

INSERT INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`) VALUES
(10, 11),
(10, 12),
(10, 13),
(10, 14),
(10, 15),
(11, 11),
(11, 17),
(11, 18),
(11, 19),
(11, 20),
(11, 21),
(20, 19),
(20, 29),
(20, 30),
(20, 31),
(20, 32),
(20, 33),
(20, 35),
(21, 43),
(21, 44),
(21, 45),
(21, 46),
(22, 47),
(22, 48),
(22, 49),
(23, 22),
(23, 51),
(23, 52),
(24, 53),
(24, 54),
(24, 55),
(25, 22),
(25, 57),
(25, 58),
(26, 12),
(26, 55),
(26, 59),
(27, 53),
(27, 63),
(27, 64),
(27, 65),
(28, 66),
(28, 67),
(28, 68),
(29, 69),
(29, 70),
(29, 71),
(29, 72),
(29, 73),
(29, 74),
(30, 75),
(30, 76),
(30, 77);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `recipe_steps`
--

CREATE TABLE `recipe_steps` (
  `step_id` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `step_number` int(11) NOT NULL,
  `instruction` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `recipe_steps`
--

INSERT INTO `recipe_steps` (`step_id`, `recipe_id`, `step_number`, `instruction`) VALUES
(4, 10, 1, 'Limetten auspressen'),
(5, 10, 2, 'Minze leicht zerdrücken'),
(6, 10, 3, 'Zucker & Rum hinzufügen'),
(7, 10, 4, 'Mit Soda auffüllen'),
(8, 10, 5, 'Eiswürfel hinzufügen'),
(9, 10, 6, 'Umrühren'),
(10, 11, 1, 'Eiswürfel in den Mixer geben'),
(11, 11, 2, 'Rum'),
(12, 11, 3, 'Kokoscreme und Ananassaft hinzufügen'),
(13, 11, 4, 'Alles mixen bis cremig'),
(14, 11, 5, 'In ein Glas füllen'),
(15, 11, 6, 'Mit Ananasstück und Cocktailkirsche garnieren'),
(16, 11, 7, 'Sofort servieren'),
(26, 20, 1, 'Erdbeeren waschen und halbieren'),
(27, 20, 2, 'Banane schälen und in Stücke schneiden'),
(28, 20, 3, 'Minzblätter grob hacken'),
(29, 20, 4, 'alle Zutaten in einen Mixer geben'),
(30, 20, 5, 'gut pürieren bis eine cremige Konsistenz entsteht'),
(31, 20, 6, 'bei Bedarf mit Honig nachsüßen'),
(32, 20, 7, 'in Gläser füllen'),
(33, 20, 8, 'mit Minzblatt garnieren'),
(34, 20, 9, 'sofort servieren'),
(68, 30, 1, 'Tequila und O-Saft mischen'),
(69, 30, 2, 'Eis ins Glas'),
(70, 30, 3, 'Grenadine vorsichtig einsinken lassen'),
(71, 29, 1, 'Alle 5 klaren Geister mischen'),
(72, 29, 2, 'Zitronensaft dazu'),
(73, 29, 3, 'Mit Cola auffüllen'),
(74, 28, 1, 'Glas mit Eis füllen'),
(75, 28, 2, 'Prosecco und Aperol eingießen'),
(76, 28, 3, 'Spritzer Soda dazu'),
(77, 27, 1, 'Zucker mit Bitter auflösen'),
(78, 27, 2, 'Whiskey und Eis zugeben'),
(79, 27, 3, 'Lange rühren'),
(80, 26, 1, 'Rum, Limette, Sirup in den Shaker'),
(81, 26, 2, 'Eiskalt shaken'),
(82, 26, 3, 'Ohne Eis servieren'),
(89, 25, 1, 'Espresso kochen und kühlen'),
(90, 25, 2, 'Mit Wodka und Likör hart shaken'),
(91, 25, 3, 'In Martini-Schale füllen'),
(95, 24, 1, 'Whiskey, Zitrone und Zucker shaken'),
(96, 24, 2, 'Eiswürfel dazu'),
(97, 24, 3, 'In den Tumbler abseihen'),
(98, 23, 1, 'Wodka und Limette ins Glas'),
(99, 23, 2, 'Eis hinzufügen'),
(100, 23, 3, 'Mit Ginger Beer auffüllen'),
(104, 21, 1, 'Rum, Limette und Mandelsirup shaken'),
(105, 21, 2, 'Auf Crushed Ice abseihen'),
(106, 21, 3, 'Mit Minze garnieren'),
(110, 22, 1, 'Gin, Vermouth und Campari mischen'),
(111, 22, 2, 'Auf Eis rühren'),
(112, 22, 3, 'Orangenzeste hinzufügen');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'user'),
(2, 'moderator'),
(3, 'admin');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `userid` int(11) UNSIGNED NOT NULL,
  `benutzername` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'resources/images/default_profile.png',
  `passwort` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 1,
  `is_barkeeper` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`userid`, `benutzername`, `email`, `profile_image`, `passwort`, `role`, `is_barkeeper`) VALUES
(1, 'test name', 'email@mail.at', 'resources/images/default_profile.png', 'dwefewfkeulidjewhfghkdjskamsndbhjas', 1, 0),
(2, 'John Pork', 'ba.ba.boi@outlook.com', 'resources/images/profiles/user_2_1767642567.webp', '$2y$10$p930OZRppw21aAwLwBKmue310YKYCq/fiYlnp5PZsKEypphKyhmyC', 1, 1),
(3, 'discordmod', 'uwu@mod.at', 'resources/images/default_profile.png', '$2y$10$fpch2pb.N0unA4p9B8bDnen6yuUyMsOnM3IRWBTlxj9uC2ZDaI8Qm', 3, 0);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `barkeeper_applications`
--
ALTER TABLE `barkeeper_applications`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `userid` (`userid`);

--
-- Indizes für die Tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `categroy_name` (`category_name`);

--
-- Indizes für die Tabelle `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indizes für die Tabelle `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredient_id`),
  ADD UNIQUE KEY `unique_ingredient` (`ingredient_name`);

--
-- Indizes für die Tabelle `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`userid`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indizes für die Tabelle `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`recipe_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indizes für die Tabelle `recipe_categories`
--
ALTER TABLE `recipe_categories`
  ADD PRIMARY KEY (`recipe_id`,`category_id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indizes für die Tabelle `recipe_images`
--
ALTER TABLE `recipe_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indizes für die Tabelle `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD PRIMARY KEY (`recipe_id`,`ingredient_id`),
  ADD KEY `fk_ingredient` (`ingredient_id`);

--
-- Indizes für die Tabelle `recipe_steps`
--
ALTER TABLE `recipe_steps`
  ADD PRIMARY KEY (`step_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indizes für die Tabelle `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `benutzername` (`benutzername`),
  ADD UNIQUE KEY `benutzername_2` (`benutzername`,`email`),
  ADD KEY `fk_user_role` (`role`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `barkeeper_applications`
--
ALTER TABLE `barkeeper_applications`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT für Tabelle `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT für Tabelle `recipe_images`
--
ALTER TABLE `recipe_images`
  MODIFY `image_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT für Tabelle `recipe_steps`
--
ALTER TABLE `recipe_steps`
  MODIFY `step_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `barkeeper_applications`
--
ALTER TABLE `barkeeper_applications`
  ADD CONSTRAINT `barkeeper_applications_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `recipe_categories`
--
ALTER TABLE `recipe_categories`
  ADD CONSTRAINT `category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `recipe_categories_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `recipe_images`
--
ALTER TABLE `recipe_images`
  ADD CONSTRAINT `recipe_images_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD CONSTRAINT `fk_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `recipe_steps`
--
ALTER TABLE `recipe_steps`
  ADD CONSTRAINT `recipe_steps_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `user_role` FOREIGN KEY (`role`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
