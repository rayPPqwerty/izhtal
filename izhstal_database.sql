-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 25 2026 г., 03:09
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `izhstal_database`
--

-- --------------------------------------------------------

--
-- Структура таблицы `facilities`
--

CREATE TABLE `facilities` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `legacy_id` int DEFAULT NULL COMMENT 'Старый идентификатор объекта из параметра icebox, если известен',
  `address` varchar(255) DEFAULT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `facilities`
--

INSERT INTO `facilities` (`id`, `name`, `short_name`, `legacy_id`, `address`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Крытый каток «Олимпиец»', 'Олимпиец', 2, 'г. Ижевск', NULL, 1, 1, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(2, 'Центр адаптивного спорта «Удмуртия»', 'ЦАС «Удмуртия»', 3, 'г. Ижевск', NULL, 1, 2, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(3, 'Ледовая арена «Можга»', 'ЛА «Можга»', NULL, 'г. Можга', NULL, 1, 3, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(4, 'Ледовый дворец «Ижсталь»', 'ЛД «Ижсталь»', NULL, 'г. Ижевск, ул. Удмуртская, 222А', 'Объект находится на реконструкции (по информации сайта на момент проектирования).', 1, 4, '2026-11-07 05:31:55', '2026-11-07 05:31:55');

-- --------------------------------------------------------

--
-- Структура таблицы `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `rental_categories`
--

CREATE TABLE `rental_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `color` char(7) NOT NULL DEFAULT '#808080',
  `text_color` char(7) NOT NULL DEFAULT '#FFFFFF',
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `rental_categories`
--

INSERT INTO `rental_categories` (`id`, `name`, `color`, `text_color`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Льготная аренда', '#4ea1d3', '#FFFFFF', NULL, 1, 1, '2026-11-07 05:31:55', '2026-11-23 10:59:59'),
(2, 'Школа фигурного катания', '#6366f1', '#FFFFFF', NULL, 1, 2, '2026-11-07 05:31:55', '2026-11-23 10:58:51'),
(3, 'Школа хоккея', '#2ca87a', '#FFFFFF', NULL, 1, 3, '2026-11-07 05:31:55', '2026-11-23 10:59:59'),
(4, 'ВХЛ', '#e0a428', '#FFFFFF', NULL, 1, 4, '2026-11-07 05:31:55', '2026-11-23 10:58:51'),
(5, 'Коммерческая аренда', '#a569bd', '#FFFFFF', NULL, 1, 5, '2026-11-07 05:31:55', '2026-11-23 10:59:59'),
(6, 'Начальная подготовка', '#d93b3b', '#FFFFFF', NULL, 1, 6, '2026-11-07 05:31:55', '2026-11-23 10:58:51'),
(7, 'Другое', '#64748b', '#FFFFFF', NULL, 1, 7, '2026-11-07 05:31:55', '2026-11-23 10:59:59');

-- --------------------------------------------------------

--
-- Структура таблицы `rental_requests`
--

CREATE TABLE `rental_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `space_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `preferred_date` date NOT NULL,
  `preferred_start_time` time NOT NULL,
  `duration_minutes` smallint UNSIGNED NOT NULL DEFAULT '60',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `renter_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `renter_phone` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `renter_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `admin_comment` text COLLATE utf8mb4_unicode_ci,
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `rental_requests`
--

INSERT INTO `rental_requests` (`id`, `space_id`, `category_id`, `preferred_date`, `preferred_start_time`, `duration_minutes`, `title`, `renter_name`, `renter_phone`, `renter_email`, `notes`, `status`, `admin_comment`, `processed_by`, `processed_at`, `created_at`, `updated_at`) VALUES
(13, 1, 1, '2026-11-27', '15:30:00', 240, 'wertwe', 'Пестерев Роман Вячеславович', '+7 (950) 173-40-18', 'raymain2000@gmail.com', 'wert', 'cancelled', NULL, 1, '2026-11-24 21:03:54', '2026-11-24 21:01:54', '2026-11-24 21:03:54'),
(14, 2, 1, '2026-11-28', '15:00:00', 60, 'выфавыа', 'Пестерев Роман Вячеславович', '+7 (950) 173-40-18', 'raymain2000@gmail.com', 'фыва', 'approved', NULL, 1, '2026-11-24 21:04:24', '2026-11-24 21:04:15', '2026-11-24 21:04:24');

-- --------------------------------------------------------

--
-- Структура таблицы `renters`
--

CREATE TABLE `renters` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `contact_person` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `notes` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `renters`
--

INSERT INTO `renters` (`id`, `name`, `contact_person`, `phone`, `email`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ППП', NULL, '76768678', 'hfidug@gmail.com', NULL, 1, '2026-11-20 08:22:46', '2026-11-23 08:13:28'),
(2, 'папвпр', NULL, 'аврва', NULL, NULL, 1, '2026-11-23 08:09:54', '2026-11-23 08:09:54'),
(3, 'Фёдоров Андрей Николаевич', NULL, '+7 (912) 000-11-22', 'school2008@izhstal-hockey.ru', NULL, 1, '2026-11-24 19:28:22', '2026-11-24 19:28:22'),
(4, 'Сидоров Максим Алексеевич', NULL, '+7 (922) 005-11-22', NULL, NULL, 1, '2026-11-24 19:29:17', '2026-11-24 19:29:17'),
(5, 'Петрова Анна Викторовна', NULL, '+7 (950) 112-33-44', 'a.petrova@mail.ru', NULL, 1, '2026-11-24 19:32:36', '2026-11-24 19:32:36'),
(6, 'Пестерев Роман Вячеславович', NULL, '+7 (950) 173-40-18', 'raymain2000@gmail.com', NULL, 1, '2026-11-24 20:13:01', '2026-11-24 21:04:24');

-- --------------------------------------------------------

--
-- Структура таблицы `schedule_entries`
--

CREATE TABLE `schedule_entries` (
  `id` bigint UNSIGNED NOT NULL,
  `space_id` bigint UNSIGNED NOT NULL,
  `renter_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `entry_type` enum('rental','ice_resurfacing','blocked') NOT NULL DEFAULT 'rental',
  `title` varchar(255) NOT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `schedule_entries`
--

INSERT INTO `schedule_entries` (`id`, `space_id`, `renter_id`, `category_id`, `created_by`, `entry_type`, `title`, `start_at`, `end_at`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, NULL, 2, 1, 'rental', 'yjhgjghjghj', '2026-11-16 07:30:00', '2026-11-16 08:45:00', NULL, '2026-11-20 07:29:33', '2026-11-20 11:30:02', NULL),
(2, 1, NULL, NULL, 1, 'ice_resurfacing', 'в', '2026-11-16 06:30:00', '2026-11-16 07:30:00', NULL, '2026-11-20 07:46:08', '2026-11-20 10:57:58', '2026-11-20 10:57:58'),
(3, 1, 1, 6, 1, 'rental', 'ППП', '2026-11-16 06:30:00', '2026-11-16 07:30:00', 'равр', '2026-11-20 07:59:33', '2026-11-23 08:13:28', NULL),
(4, 1, NULL, 4, 1, 'rental', 'ССССС', '2026-11-16 09:00:00', '2026-11-16 11:15:00', NULL, '2026-11-20 08:01:52', '2026-11-20 08:01:52', NULL),
(5, 1, NULL, 5, 1, 'rental', 'мяу', '2026-11-17 12:00:00', '2026-11-17 16:00:00', NULL, '2026-11-20 08:12:04', '2026-11-20 08:12:04', NULL),
(6, 1, 1, 3, 1, 'rental', 'прватаисм', '2026-11-09 06:45:00', '2026-11-09 10:45:00', NULL, '2026-11-20 09:53:31', '2026-11-20 09:53:31', NULL),
(7, 1, NULL, 2, 1, 'rental', 'gfgfgdgdf', '2026-11-16 11:15:00', '2026-11-16 12:15:00', NULL, '2026-11-20 10:10:54', '2026-11-20 10:10:54', NULL),
(8, 1, NULL, 3, 1, 'rental', 'прпрпарарпа', '2026-11-16 12:30:00', '2026-11-16 14:15:00', NULL, '2026-11-20 11:24:34', '2026-11-20 11:24:34', NULL),
(9, 1, NULL, 1, 1, 'rental', 'uj8tyu', '2026-11-16 14:45:00', '2026-11-16 15:45:00', NULL, '2026-11-20 11:31:02', '2026-11-20 11:31:02', NULL),
(10, 1, NULL, 1, 1, 'rental', 'klhnfdkjlhngk.ldhglhndkjfghnkdfhngjdnofihogdfjfgjhftj', '2026-11-16 16:30:00', '2026-11-16 17:30:00', NULL, '2026-11-20 11:32:35', '2026-11-20 11:32:35', NULL),
(11, 1, 2, 1, 1, 'rental', 'папвпр', '2026-11-16 16:00:00', '2026-11-16 16:30:00', 'равр', '2026-11-23 08:09:54', '2026-11-23 08:09:54', NULL),
(12, 1, NULL, 3, 1, 'rental', 'кекеуке', '2026-11-16 06:00:00', '2026-11-16 06:30:00', NULL, '2026-11-23 08:13:08', '2026-11-23 08:13:08', NULL),
(13, 1, 3, 1, 1, 'rental', 'Сборы детской школы «Ижсталь-2008»', '2026-12-09 09:00:00', '2026-12-09 13:00:00', 'Полный лёд с 09:00 до 13:00, нужны раздевалки на всю команду.', '2026-11-24 19:28:22', '2026-11-24 19:28:22', NULL),
(14, 2, 4, 2, 1, 'rental', 'Утренняя зарядка — сборная по фигурному катанию', '2026-11-27 08:00:00', '2026-11-27 09:00:00', 'Нужны маты и степ-платформы.', '2026-11-24 19:29:17', '2026-11-24 19:29:17', NULL),
(15, 1, 5, 3, 1, 'rental', 'Массовое катание — субботняя сессия', '2026-11-30 12:00:00', '2026-11-30 14:00:00', 'Ожидается группа около 40 человек (школьники).', '2026-11-24 19:32:36', '2026-11-24 19:32:36', NULL),
(16, 1, 6, 2, 1, 'rental', 'ХК Прогресс', '2026-11-27 15:00:00', '2026-11-27 15:45:00', 'Нужны 2 раздевалки', '2026-11-24 20:13:01', '2026-11-24 20:20:27', NULL),
(17, 2, 6, 1, 1, 'rental', 'выфавыа', '2026-11-28 15:00:00', '2026-11-28 16:00:00', 'фыва', '2026-11-24 21:04:24', '2026-11-24 21:04:24', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `schedule_publications`
--

CREATE TABLE `schedule_publications` (
  `id` bigint UNSIGNED NOT NULL,
  `space_id` bigint UNSIGNED NOT NULL,
  `schedule_date` date NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `published_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `schedule_publications`
--

INSERT INTO `schedule_publications` (`id`, `space_id`, `schedule_date`, `is_published`, `published_at`, `published_by`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-11-16', 1, '2026-11-20 10:46:21', 1, '2026-11-20 07:29:39', '2026-11-20 07:46:21'),
(2, 1, '2026-11-17', 1, '2026-11-20 14:25:55', 1, '2026-11-20 08:12:08', '2026-11-20 11:25:55'),
(3, 1, '2026-11-09', 1, '2026-11-20 12:53:38', 1, '2026-11-20 09:53:38', '2026-11-20 09:53:38'),
(4, 1, '2026-11-27', 0, NULL, 1, '2026-11-24 19:29:26', '2026-11-24 19:29:44');

-- --------------------------------------------------------

--
-- Структура таблицы `spaces`
--

CREATE TABLE `spaces` (
  `id` bigint UNSIGNED NOT NULL,
  `facility_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `space_type` enum('ice','hall','gym','pool','other') NOT NULL DEFAULT 'other',
  `description` text,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `spaces`
--

INSERT INTO `spaces` (`id`, `facility_id`, `name`, `space_type`, `description`, `opening_time`, `closing_time`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ледовая арена', 'ice', NULL, NULL, NULL, 1, 1, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(2, 1, 'Хореографический зал', 'hall', NULL, NULL, NULL, 1, 2, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(3, 1, 'Зал ОФП', 'gym', NULL, NULL, NULL, 1, 3, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(4, 2, 'Ледовая арена', 'ice', NULL, NULL, NULL, 1, 1, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(5, 2, 'Бассейн', 'pool', NULL, NULL, NULL, 1, 2, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(6, 2, 'Тренажёрный зал', 'gym', NULL, NULL, NULL, 1, 3, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(7, 2, 'Хореографический зал', 'hall', NULL, NULL, NULL, 1, 4, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(8, 3, 'Ледовая арена', 'ice', NULL, NULL, NULL, 1, 1, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(9, 3, 'Зал хореографии', 'hall', NULL, NULL, NULL, 1, 2, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(10, 3, 'Зал единоборств', 'hall', NULL, NULL, NULL, 1, 3, '2026-11-07 05:31:55', '2026-11-07 05:31:55'),
(11, 3, 'Спортивный зал', 'gym', NULL, NULL, NULL, 1, 4, '2026-11-07 05:31:55', '2026-11-07 05:31:55');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `login` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` enum('admin','editor') NOT NULL DEFAULT 'editor',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `login`, `email`, `password`, `remember_token`, `role`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 'admin@izhstal.ru', '$2y$10$SlUcak69rPH.G76Sgm3tdOW2KZeAv17CLrIugaKGRRh.O3eMrMW4m', 'ubuuzpaby4wIehpRLwOIj9mvwESkkrdYplTJQvF6EnXeSVVg6Es8eHvXje1I', 'admin', 1, '2026-11-20 14:16:15', '2026-11-20 10:17:05', '2026-11-24 22:35:11');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `legacy_id` (`legacy_id`);

--
-- Индексы таблицы `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `rental_categories`
--
ALTER TABLE `rental_categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `rental_requests`
--
ALTER TABLE `rental_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_created` (`status`,`created_at`),
  ADD KEY `idx_space_date` (`space_id`,`preferred_date`),
  ADD KEY `fk_rr_category` (`category_id`),
  ADD KEY `fk_rr_user` (`processed_by`);

--
-- Индексы таблицы `renters`
--
ALTER TABLE `renters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_renters_name` (`name`);

--
-- Индексы таблицы `schedule_entries`
--
ALTER TABLE `schedule_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_entries_renter` (`renter_id`),
  ADD KEY `fk_entries_user` (`created_by`),
  ADD KEY `idx_entries_space_time` (`space_id`,`start_at`,`end_at`),
  ADD KEY `idx_entries_dates` (`start_at`,`end_at`),
  ADD KEY `idx_entries_category` (`category_id`);

--
-- Индексы таблицы `schedule_publications`
--
ALTER TABLE `schedule_publications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_publication_space_date` (`space_id`,`schedule_date`),
  ADD KEY `fk_publications_user` (`published_by`),
  ADD KEY `idx_publication_date` (`schedule_date`,`is_published`);

--
-- Индексы таблицы `spaces`
--
ALTER TABLE `spaces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_spaces_facility` (`facility_id`,`is_active`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `rental_categories`
--
ALTER TABLE `rental_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `rental_requests`
--
ALTER TABLE `rental_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `renters`
--
ALTER TABLE `renters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `schedule_entries`
--
ALTER TABLE `schedule_entries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `schedule_publications`
--
ALTER TABLE `schedule_publications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `spaces`
--
ALTER TABLE `spaces`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `rental_requests`
--
ALTER TABLE `rental_requests`
  ADD CONSTRAINT `fk_rr_category` FOREIGN KEY (`category_id`) REFERENCES `rental_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_rr_space` FOREIGN KEY (`space_id`) REFERENCES `spaces` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rr_user` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `schedule_entries`
--
ALTER TABLE `schedule_entries`
  ADD CONSTRAINT `fk_entries_category` FOREIGN KEY (`category_id`) REFERENCES `rental_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entries_renter` FOREIGN KEY (`renter_id`) REFERENCES `renters` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entries_space` FOREIGN KEY (`space_id`) REFERENCES `spaces` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entries_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `schedule_publications`
--
ALTER TABLE `schedule_publications`
  ADD CONSTRAINT `fk_publications_space` FOREIGN KEY (`space_id`) REFERENCES `spaces` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_publications_user` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `spaces`
--
ALTER TABLE `spaces`
  ADD CONSTRAINT `fk_spaces_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
