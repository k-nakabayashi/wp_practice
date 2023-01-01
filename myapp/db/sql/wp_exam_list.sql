-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost:8889
-- 生成日時: 2022 年 6 月 19 日 04:30
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
-- テーブルの構造 `wp_exam_list`
--

CREATE TABLE `wp_exam_list` (
  `id` int(11) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(10) NOT NULL DEFAULT '0',
  `perfomer_id` text NOT NULL,
  `patient_id` text NOT NULL,
  `symptom` text,
  `symptom_change` text,
  `today_meal` text,
  `meal` text,
  `alcohol` int(11) NOT NULL DEFAULT '0',
  `cigarettes` int(11) NOT NULL DEFAULT '0',
  `sleeping_time` int(11) NOT NULL DEFAULT '0',

  `check_memo` text,
  `direction_1` text NOT NULL,
  `direction_2` text NOT NULL,
  `direction_3` text NOT NULL,
  `direction_4` text NOT NULL,
  `direction_5` text NOT NULL,
  `direction_6` text NOT NULL,
  `direction_7` text,
  `direction_8` text NOT NULL,
  `score_1` int(11) NOT NULL DEFAULT '0',
  `score_2` int(11) NOT NULL DEFAULT '0',
  `score_3` int(11) NOT NULL DEFAULT '0',
  `score_4` int(11) NOT NULL DEFAULT '0',
  `score_5` int(11) NOT NULL DEFAULT '0',
  `score_6` int(11) NOT NULL DEFAULT '0',
  `score_7` int(11) NOT NULL DEFAULT '0',
  `score_8` int(11) NOT NULL DEFAULT '0',
  `perform_memo` text,
  `perform_site` text,
  `perform_effect` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `wp_exam_list`
--
ALTER TABLE `wp_exam_list`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `wp_exam_list`
--
ALTER TABLE `wp_exam_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
