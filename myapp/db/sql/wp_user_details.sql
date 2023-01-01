-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost:8889
-- 生成日時: 2022 年 6 月 19 日 04:32
-- サーバのバージョン： 5.7.34
-- PHP のバージョン: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `wp06`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `wp_user_details`
--

CREATE TABLE `wp_user_details` (
  `id` int(11) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `perfomer_id` int(11) DEFAULT NULL,
  `birthday` timestamp NULL DEFAULT NULL,
  `gender` tinyint(10) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `partner` tinyint(10) DEFAULT NULL,
  `sports_history` text,
  `body_height` float DEFAULT NULL,
  `body_weight` float DEFAULT NULL,
  `dominant_hand` tinyint(10) DEFAULT NULL,
  `max_blood_preasure` float DEFAULT NULL,
  `min_blood_preasure` float DEFAULT NULL,
  `blood_type` varchar(255) DEFAULT NULL,
  `right_sight` float DEFAULT NULL,
  `left_sight` float DEFAULT NULL,
  `teeth_bite` tinyint(10) DEFAULT NULL,
  `drinking` tinyint(10) DEFAULT '0',
  `smoking` tinyint(10) DEFAULT '0',
  `sleeping_time` tinyint(10) DEFAULT '0',
  `symptom_site` text,
  `killing_movement` text,
  `onset_time` timestamp NULL DEFAULT NULL,
  `casuse` text,
  `allergy` text,
  `birth` text CHARACTER SET armscii8,
  `birthing_time` int(11) DEFAULT NULL,
  `birth_weight` float DEFAULT NULL,
  `birth_memo` text CHARACTER SET armscii8,
  `birth_icu_day` int(11) DEFAULT NULL,
  `medical_history` text,
  `applicable_disease` text,
  `applicable_other` text,
  `updatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(10) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `wp_user_details`
--
ALTER TABLE `wp_user_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_id` (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `user_id_2` (`user_id`,`member_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `wp_user_details`
--
ALTER TABLE `wp_user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
