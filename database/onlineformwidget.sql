-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 28 Nis 2021, 11:57:13
-- Sunucu sürümü: 10.4.6-MariaDB
-- PHP Sürümü: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `onlineformwidget`
--
CREATE DATABASE IF NOT EXISTS `onlineformwidget` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `onlineformwidget`;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `element_id` mediumint(9) NOT NULL,
  `value` varchar(250) NOT NULL,
  `respondents_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `forms`
--

CREATE TABLE `forms` (
  `id` smallint(6) NOT NULL,
  `title` varchar(150) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `form_html` text NOT NULL,
  `form_xml` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_date` datetime DEFAULT NULL,
  `source_ip` varchar(60) DEFAULT NULL,
  `public` tinyint(4) NOT NULL DEFAULT 0,
  `hidden_url` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `form_elements`
--

CREATE TABLE `form_elements` (
  `id` mediumint(9) NOT NULL,
  `form_id` smallint(6) NOT NULL,
  `title` varchar(250) NOT NULL,
  `element_order` smallint(6) NOT NULL,
  `element_name` varchar(250) NOT NULL,
  `type` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `respondents`
--

CREATE TABLE `respondents` (
  `id` int(11) NOT NULL,
  `form_id` smallint(6) NOT NULL,
  `ip` varchar(60) NOT NULL DEFAULT '-.-.-.-',
  `answer_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `element_id` (`element_id`),
  ADD KEY `respondents_id` (`respondents_id`);

--
-- Tablo için indeksler `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `form_elements`
--
ALTER TABLE `form_elements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`);

--
-- Tablo için indeksler `respondents`
--
ALTER TABLE `respondents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `forms`
--
ALTER TABLE `forms`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `form_elements`
--
ALTER TABLE `form_elements`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `respondents`
--
ALTER TABLE `respondents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`element_id`) REFERENCES `form_elements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`respondents_id`) REFERENCES `respondents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `form_elements`
--
ALTER TABLE `form_elements`
  ADD CONSTRAINT `form_elements_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `respondents`
--
ALTER TABLE `respondents`
  ADD CONSTRAINT `respondents_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
