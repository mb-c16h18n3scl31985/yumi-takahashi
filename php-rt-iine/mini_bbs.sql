-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost:8889
-- 生成日時: 2020 年 10 月 03 日 06:03
-- サーバのバージョン： 5.7.26
-- PHP のバージョン: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- データベース: `mini_bbs`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `favorite`
--

CREATE TABLE `favorite` (
  `id` int(11) NOT NULL,
  `favorite_post_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `favorite`
--

INSERT INTO `favorite` (`id`, `favorite_post_id`, `member_id`, `created`, `delete_flag`, `deleted`) VALUES
(1, 21, 6, '2020-09-28 18:49:23', 1, '2020-10-03 10:27:12'),
(10, 21, 5, '2020-10-01 16:31:34', 1, '2020-10-03 10:27:12'),
(11, 18, 6, '2020-10-01 16:33:48', 1, '2020-10-02 09:04:14'),
(12, 20, 6, '2020-10-01 16:44:23', 1, '2020-10-02 10:04:46'),
(13, 21, 6, '2020-10-01 17:17:41', 1, '2020-10-03 10:27:12'),
(14, 19, 6, '2020-10-01 17:34:13', 1, '2020-10-02 10:04:35'),
(15, 20, 6, '2020-10-01 21:38:49', 1, '2020-10-02 10:04:46'),
(16, 14, 6, '2020-10-01 21:38:56', 1, '2020-10-02 09:02:58'),
(17, 14, 6, '2020-10-01 21:40:13', 1, '2020-10-02 09:02:58'),
(18, 13, 6, '2020-10-01 21:40:17', 1, '2020-10-02 09:04:33'),
(19, 17, 6, '2020-10-01 21:40:27', 1, '2020-10-02 09:04:19'),
(20, 12, 6, '2020-10-01 21:40:32', 0, '0000-00-00 00:00:00'),
(21, 18, 6, '2020-10-02 08:28:07', 1, '2020-10-02 09:04:14'),
(22, 16, 6, '2020-10-02 09:03:02', 1, '2020-10-02 09:04:27'),
(23, 21, 6, '2020-10-02 09:04:08', 1, '2020-10-03 10:27:12'),
(24, 20, 6, '2020-10-02 09:04:11', 1, '2020-10-02 10:04:46'),
(25, 19, 6, '2020-10-02 09:04:12', 1, '2020-10-02 10:04:35'),
(26, 19, 6, '2020-10-02 09:04:16', 1, '2020-10-02 10:04:35'),
(27, 18, 6, '2020-10-02 09:04:17', 0, '0000-00-00 00:00:00'),
(28, 17, 6, '2020-10-02 09:04:21', 0, '0000-00-00 00:00:00'),
(29, 21, 6, '2020-10-02 09:04:23', 1, '2020-10-03 10:27:12'),
(30, 14, 6, '2020-10-02 09:04:37', 0, '0000-00-00 00:00:00'),
(31, 20, 6, '2020-10-02 10:04:56', 0, '0000-00-00 00:00:00'),
(32, 21, 6, '2020-10-02 11:08:55', 1, '2020-10-03 10:27:12'),
(33, 21, 6, '2020-10-03 10:27:11', 1, '2020-10-03 10:27:12'),
(34, 30, 8, '2020-10-03 14:55:58', 1, '2020-10-03 14:55:58'),
(35, 28, 8, '2020-10-03 14:56:00', 0, '0000-00-00 00:00:00'),
(36, 20, 8, '2020-10-03 14:56:01', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- テーブルの構造 `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `members`
--

INSERT INTO `members` (`id`, `name`, `email`, `password`, `picture`, `created`, `modified`) VALUES
(6, 'first', 'first@mail', 'e0996a37c13d44c3b06074939d43fa3759bd32c1', '20200924095848interview_wanko.jpg', '2020-09-24 18:58:50', '2020-09-24 09:58:50'),
(7, 'second', 'second@email', '352f7829a2384b001cc12b0c2613c756454a1f6a', '20200924095938N08-fKsO_400x400.jpg', '2020-09-24 18:59:40', '2020-09-24 09:59:40'),
(8, 'three', 'three@email', 'b802f384302cb24fbab0a44997e820bf2e8507bb', '20200924100011宇宙猫 (1).jpg', '2020-09-24 19:00:14', '2020-09-24 10:00:14');

-- --------------------------------------------------------

--
-- テーブルの構造 `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `member_id` int(11) NOT NULL,
  `reply_post_id` int(11) NOT NULL,
  `rt_post_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `posts`
--

INSERT INTO `posts` (`id`, `message`, `member_id`, `reply_post_id`, `rt_post_id`, `created`, `modified`) VALUES
(12, 'あいうえお', 6, 0, 0, '2020-09-24 19:41:10', '2020-09-24 10:41:10'),
(13, 'DB前の残ってたの削除した', 6, 0, 0, '2020-09-24 19:49:34', '2020-09-24 10:49:34'),
(14, '見にくいんだもの', 6, 0, 0, '2020-09-24 19:49:40', '2020-09-24 10:49:40'),
(15, 'ぬぬぬ', 7, 0, 0, '2020-09-24 20:25:42', '2020-09-24 11:25:42'),
(16, 'なんだか変よ', 7, 0, 0, '2020-09-24 20:26:02', '2020-09-24 11:26:02'),
(17, 'せやな @first 見にくいんだもの', 7, 14, 0, '2020-09-24 20:29:23', '2020-09-24 11:29:23'),
(18, 'あれまたなんかスペースあるぞ                                      ', 8, 0, 0, '2020-09-24 20:51:56', '2020-09-24 11:51:56'),
(19, '@three あれまたなんかスペースあるぞ 改行消すの忘れておりました                                     ', 8, 18, 0, '2020-09-24 20:52:33', '2020-09-24 11:52:33'),
(20, '@first DB前の残ってたの削除した 結構勇気いるね', 8, 13, 0, '2020-09-24 20:52:46', '2020-09-24 11:52:46'),
(21, 'https://www.php.net/manual/ja/function.mb-ereg-replace', 6, 0, 0, '2020-09-25 16:08:33', '2020-09-25 07:08:33'),
(28, 'RT@three @three あれまたなんかスペースあるぞ 改行消すの忘れておりました                                     ', 6, 0, 19, '2020-10-03 11:17:14', '2020-10-03 02:17:14'),
(30, 'RT@three @first DB前の残ってたの削除した 結構勇気いるね', 6, 0, 20, '2020-10-03 12:02:22', '2020-10-03 03:02:22'),
(31, 'LEAN UX', 6, 0, 0, '2020-10-03 12:04:27', '2020-10-03 03:04:27'),
(32, 'RT@first RT@three @first DB前の残ってたの削除した 結構勇気いるね', 8, 0, 30, '2020-10-03 14:56:06', '2020-10-03 05:56:06'),
(33, 'RT@first https://www.php.net/manual/ja/function.mb-ereg-replace', 8, 0, 21, '2020-10-03 14:56:09', '2020-10-03 05:56:09');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `favorite`
--
ALTER TABLE `favorite`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `favorite`
--
ALTER TABLE `favorite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- テーブルのAUTO_INCREMENT `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルのAUTO_INCREMENT `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
