-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 17. Jan 2026 um 23:43
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
(1, 2, 'John Pork', 'resources/uploads/verification/verify_2_1767661414.webp', 'approved', '2026-01-06 01:03:34'),
(2, 4, 'Cosima Kostenzer', 'resources/uploads/verification/verify_4_1767781663.jpeg', 'rejected', '2026-01-07 10:27:43');

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
(2, 5, '2026-01-17 14:43:38'),
(5, 1, '2026-01-16 22:39:53'),
(5, 2, '2026-01-17 15:37:18');

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
(7, 'Ananassaft'),
(43, 'Angostura Bitter'),
(14, 'Aperol'),
(40, 'Bourbon Whiskey'),
(19, 'Brombeerlikör'),
(11, 'Campari'),
(8, 'Cocktailkirsche'),
(44, 'Eiweiß'),
(21, 'frische Brombeeren'),
(9, 'Gin'),
(13, 'Ginger Beer'),
(6, 'Kokoscreme'),
(2, 'Limettensaft'),
(4, 'Minzblätter'),
(15, 'Prosecco'),
(3, 'Rohrzucker'),
(10, 'Roter Wermut'),
(5, 'Soda'),
(1, 'Weißer Rum'),
(12, 'Wodka'),
(17, 'Zitronensaft'),
(20, 'Zitronenscheibe'),
(18, 'Zuckersirup');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ratings`
--

CREATE TABLE `ratings` (
  `userid` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `stars` tinyint(4) NOT NULL CHECK (`stars` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rating_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Mojito', 'Klassischer, erfrischender Minz-Cocktail aus Kuba.', 'Limetten auspressen Minze leicht zerdrücken Zucker & Rum hinzufügen Mit Soda auffüllen Eiswürfel hinzufügen Umrühren', 2, '2026-01-16 21:36:24.252499'),
(2, 'Piña Colada', 'Tropischer Klassiker mit Ananas und Kokos.', 'Eis mixen Rum, Kokoscreme und Saft dazu Cremig mixen Garnieren', 2, '2026-01-16 21:36:24.264904'),
(3, 'Negroni', 'Ein herber, italienischer Aperitif für Genießer.', 'Gin, Vermouth und Campari mischen Auf Eis rühren Orangenzeste hinzufügen', 2, '2026-01-16 21:36:24.273908'),
(4, 'Moscow Mule', 'Erfrischend scharf durch Ingwer und Limette.', 'Wodka und Limette ins Glas Eis hinzufügen Mit Ginger Beer auffüllen', 2, '2026-01-16 21:36:24.278508'),
(5, 'Aperol Spritz', 'Der Inbegriff des italienischen Sommers.', 'Eis ins Glas Prosecco und Aperol zugeben Spritzer Soda', 2, '2026-01-16 21:36:24.281448'),
(6, 'Bramble', 'Ein erfrischender, balancierter Gin-Drink mit einer fruchtigen Brombeer-Note. Perfekt serviert auf Crushed Ice.', 'Gin, Zitronensaft und Zuckersirup in einen Shaker geben.\r\n\r\nMit Eiswürfeln füllen und ca. 10-15 Sekunden kräftig schütteln.\r\n\r\nEin Glas (Tumbler) mit Crushed Ice füllen und den Mix durch ein Sieb eingießen.\r\n\r\nDen Brombeerlikör vorsichtig über das Eis träufeln, sodass ein schöner Farbeffekt entsteht.\r\n\r\nMit Zitronenscheibe und Brombeeren garnieren.', 5, '2026-01-16 21:57:06.847334'),
(7, 'Whiskey Sour', 'Ein zeitloser Favorit. Die Kombination aus Bourbon, frischem Zitronensaft und einem Hauch Süße sorgt für ein seidiges Mundgefühl und perfekten Geschmack.', 'Alle flüssigen Zutaten in einen Shaker geben.\r\n\r\nZuerst ohne Eis kräftig schütteln (Dry Shake), um das Eiweiß aufzuschäumen.\r\n\r\nEiswürfel hinzufügen und erneut ca. 15 Sekunden eiskalt schütteln.\r\n\r\nDurch ein Sieb in einen mit frischem Eis gefüllten Tumbler abseihen.\r\n\r\nMit einer Cocktailkirsche garnieren.', 5, '2026-01-16 22:38:50.641994');

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
(1, 1),
(1, 2),
(1, 5),
(2, 1),
(2, 3),
(2, 5),
(3, 1),
(3, 6),
(4, 1),
(4, 2),
(4, 5),
(5, 1),
(5, 2),
(5, 5),
(6, 1),
(6, 3),
(6, 5),
(7, 1),
(7, 2),
(7, 7);

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
(1, 1, 'resources/uploads/recipes/1767660536_2_banner.webp'),
(2, 2, 'resources/uploads/recipes/1767661889_3_pinacolada.jpeg'),
(3, 3, 'resources/uploads/recipes/recipe_22_1767663143.jpg'),
(4, 4, 'resources/uploads/recipes/recipe_23_1767663117.jpg'),
(5, 5, 'resources/uploads/recipes/recipe_28_1767663066.jpg'),
(8, 6, 'resources/uploads/recipes/recipe_6_1768600662.jpg'),
(9, 7, 'resources/uploads/recipes/recipe_7_1768603130.jpg'),
(10, 6, 'resources/uploads/recipes/recipe_6_1768603419_0.jpg'),
(11, 6, 'resources/uploads/recipes/recipe_6_1768603419_1.jpg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `ingredient_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `recipe_ingredients`
--

INSERT INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`, `amount`, `unit`) VALUES
(1, 1, 50.00, 'ml'),
(1, 2, 30.00, 'ml'),
(1, 3, 2.00, 'TL'),
(1, 4, 8.00, 'Stück'),
(1, 5, 100.00, 'ml'),
(2, 1, 50.00, 'ml'),
(2, 6, 50.00, 'ml'),
(2, 7, 100.00, 'ml'),
(2, 8, 1.00, 'Stück'),
(3, 9, 30.00, 'ml'),
(3, 10, 30.00, 'ml'),
(3, 11, 30.00, 'ml'),
(4, 2, 20.00, 'ml'),
(4, 12, 50.00, 'ml'),
(4, 13, 150.00, 'ml'),
(5, 5, 30.00, 'ml'),
(5, 14, 60.00, 'ml'),
(5, 15, 90.00, 'ml'),
(6, 9, 50.00, 'ml'),
(6, 17, 30.00, 'ml'),
(6, 18, 15.00, 'ml'),
(6, 19, 15.00, 'ml'),
(6, 20, 1.00, 'Stück'),
(6, 21, 2.00, 'Stück'),
(7, 8, 1.00, 'Stück'),
(7, 17, 30.00, 'ml'),
(7, 18, 2.00, 'cl'),
(7, 40, 6.00, 'cl'),
(7, 43, 1.00, 'Spritzer'),
(7, 44, 0.50, 'Stück');

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
(1, 1, 1, 'Limetten auspressen'),
(2, 1, 2, 'Minze leicht zerdrücken'),
(3, 1, 3, 'Zucker & Rum hinzufügen'),
(4, 1, 4, 'Mit Soda auffüllen'),
(5, 1, 5, 'Eiswürfel hinzufügen'),
(6, 1, 6, 'Umrühren'),
(7, 2, 1, 'Eis mixen'),
(8, 2, 2, 'Rum, Kokoscreme und Saft dazu'),
(9, 2, 3, 'Cremig mixen'),
(10, 2, 4, 'Garnieren'),
(11, 3, 1, 'Gin, Vermouth und Campari mischen'),
(12, 3, 2, 'Auf Eis rühren'),
(13, 3, 3, 'Orangenzeste hinzufügen'),
(14, 4, 1, 'Wodka und Limette ins Glas'),
(15, 4, 2, 'Eis hinzufügen'),
(16, 4, 3, 'Mit Ginger Beer auffüllen'),
(17, 5, 1, 'Eis ins Glas'),
(18, 5, 2, 'Prosecco und Aperol zugeben'),
(19, 5, 3, 'Spritzer Soda'),
(40, 7, 1, 'Alle flüssigen Zutaten in einen Shaker geben.'),
(41, 7, 3, 'Zuerst ohne Eis kräftig schütteln (Dry Shake), um das Eiweiß aufzuschäumen.'),
(42, 7, 5, 'Eiswürfel hinzufügen und erneut ca. 15 Sekunden eiskalt schütteln.'),
(43, 7, 7, 'Durch ein Sieb in einen mit frischem Eis gefüllten Tumbler abseihen.'),
(44, 7, 9, 'Mit einer Cocktailkirsche garnieren.'),
(50, 6, 1, 'Gin, Zitronensaft und Zuckersirup in einen Shaker geben.'),
(51, 6, 2, 'Mit Eiswürfeln füllen und ca. 10-15 Sekunden kräftig schütteln.'),
(52, 6, 3, 'Ein Glas (Tumbler) mit Crushed Ice füllen und den Mix durch ein Sieb eingießen.'),
(53, 6, 4, 'Den Brombeerlikör vorsichtig über das Eis träufeln, sodass ein schöner Farbeffekt entsteht.'),
(54, 6, 5, 'Mit Zitronenscheibe und Brombeeren garnieren.');

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
(2, 'John Pork', 'ba.ba.boi@outlook.com', 'resources/uploads/profiles/profile_user2_661589.webp', '$2y$10$p930OZRppw21aAwLwBKmue310YKYCq/fiYlnp5PZsKEypphKyhmyC', 1, 1),
(3, 'discordmod', 'uwu@mod.at', 'resources/images/default_profile.png', '$2y$10$fpch2pb.N0unA4p9B8bDnen6yuUyMsOnM3IRWBTlxj9uC2ZDaI8Qm', 3, 0),
(4, 'ichtestegernemodfunkrion', 'mod.moderatur@gmail.com', 'resources/uploads/pfp/user_4_1767774520.jpeg', '$2y$10$u6c435Wpc0Z8FIWZxKbMheBLdWQcz2FgdQp1yU3V5O2.ZxFQZWWDu', 2, 0),
(5, 'admin', 'cocktailadmin@gmail.com', 'resources/uploads/profiles/user_5_Bildschirmfoto 2026-01-17 um 16.37.54.png', '$2y$10$HD8/P19bmyjnViOAUULsXeeiZxdgaKSPbPJo5hGsgc5eoHnRINHgW', 3, 0);

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
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT für Tabelle `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `recipe_images`
--
ALTER TABLE `recipe_images`
  MODIFY `image_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT für Tabelle `recipe_steps`
--
ALTER TABLE `recipe_steps`
  MODIFY `step_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
