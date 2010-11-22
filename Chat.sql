-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 22 2010 г., 23:20
-- Версия сервера: 5.1.52
-- Версия PHP: 5.2.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `bezumnoe_bezumnoe`
--

-- --------------------------------------------------------

--
-- Структура таблицы `admin_comments`
--

CREATE TABLE IF NOT EXISTS `admin_comments` (
  `ADMIN_COMMENT_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `USER_ID` bigint(20) DEFAULT NULL,
  `DATE` datetime NOT NULL,
  `CONTENT` text NOT NULL,
  `ADMIN_LOGIN` varchar(20) NOT NULL,
  `SEVERITY` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`ADMIN_COMMENT_ID`),
  KEY `USER_ID` (`USER_ID`),
  KEY `TYPE INDEX` (`SEVERITY`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Admin comments to users' AUTO_INCREMENT=8115 ;

-- --------------------------------------------------------

--
-- Структура таблицы `banned_addresses`
--

CREATE TABLE IF NOT EXISTS `banned_addresses` (
  `BAN_ID` int(11) NOT NULL AUTO_INCREMENT,
  `CONTENT` varchar(200) NOT NULL DEFAULT '127.0.0.1',
  `TYPE` enum('ip','host') NOT NULL DEFAULT 'ip',
  `COMMENT` text,
  `ADMIN_LOGIN` varchar(20) DEFAULT NULL,
  `ADDED` datetime NOT NULL,
  `TILL` datetime DEFAULT NULL,
  `BAN_CHAT` tinyint(1) NOT NULL DEFAULT '0',
  `BAN_FORUM` tinyint(1) NOT NULL DEFAULT '0',
  `BAN_JOURNAL` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`BAN_ID`),
  UNIQUE KEY `UNIQUE CONTENT` (`CONTENT`),
  KEY `CHAT BANS` (`BAN_CHAT`),
  KEY `FORUM BANS` (`BAN_FORUM`),
  KEY `JOURNAL BANS` (`BAN_JOURNAL`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Banned addresses' AUTO_INCREMENT=66 ;

-- --------------------------------------------------------

--
-- Структура таблицы `captchas`
--

CREATE TABLE IF NOT EXISTS `captchas` (
  `GUID` varchar(10) NOT NULL,
  `VALUE` varchar(10) NOT NULL,
  `DATE` datetime NOT NULL,
  PRIMARY KEY (`GUID`),
  KEY `DATE` (`DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `forums`
--

CREATE TABLE IF NOT EXISTS `forums` (
  `FORUM_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TYPE` enum('forum','journal','gallery') NOT NULL DEFAULT 'forum',
  `TITLE` varchar(250) NOT NULL DEFAULT '',
  `DESCRIPTION` text,
  `IS_PROTECTED` tinyint(1) NOT NULL DEFAULT '0',
  `LINKED_ID` bigint(20) DEFAULT NULL,
  `TOTAL_COUNT` bigint(20) NOT NULL DEFAULT '0',
  `RATING` bigint(20) NOT NULL DEFAULT '0',
  `LAST_RATING` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`FORUM_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=514 ;

-- --------------------------------------------------------

--
-- Структура таблицы `forum_records`
--

CREATE TABLE IF NOT EXISTS `forum_records` (
  `RECORD_ID` int(11) NOT NULL AUTO_INCREMENT,
  `FORUM_ID` smallint(6) NOT NULL DEFAULT '1',
  `IND` varchar(250) NOT NULL,
  `TYPE` enum('0','1','2') NOT NULL DEFAULT '0',
  `AUTHOR` varchar(100) DEFAULT NULL,
  `USER_ID` varchar(100) DEFAULT NULL,
  `TITLE` varchar(250) DEFAULT NULL,
  `CONTENT` text,
  `DATE` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `IP` varchar(100) NOT NULL DEFAULT '',
  `CLICKS` smallint(6) NOT NULL DEFAULT '0',
  `GUID` varchar(10) DEFAULT NULL,
  `IS_COMMENTABLE` tinyint(1) NOT NULL DEFAULT '1',
  `IS_DELETED` tinyint(1) NOT NULL DEFAULT '0',
  `UPDATE_DATE` datetime NOT NULL,
  `ANSWERS_COUNT` int(11) NOT NULL DEFAULT '0',
  `DELETED_COUNT` int(11) DEFAULT '0',
  PRIMARY KEY (`RECORD_ID`),
  KEY `Forum Threads` (`IND`(4),`FORUM_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=137135 ;

-- --------------------------------------------------------

--
-- Структура таблицы `forum_records_tags`
--

CREATE TABLE IF NOT EXISTS `forum_records_tags` (
  `RECORD_ID` bigint(20) NOT NULL,
  `TAG_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`RECORD_ID`,`TAG_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `forum_users`
--

CREATE TABLE IF NOT EXISTS `forum_users` (
  `FORUM_ID` bigint(20) NOT NULL,
  `USER_ID` bigint(20) NOT NULL,
  `ACCESS` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`FORUM_ID`,`USER_ID`),
  KEY `User access to forum` (`USER_ID`,`FORUM_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='Protected forums users';

-- --------------------------------------------------------

--
-- Структура таблицы `ignores`
--

CREATE TABLE IF NOT EXISTS `ignores` (
  `IGNORE_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `USER_ID` bigint(20) NOT NULL,
  `IGNORANT_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`IGNORE_ID`),
  UNIQUE KEY `PAIR` (`USER_ID`,`IGNORANT_ID`),
  KEY `I_IGNORE` (`USER_ID`),
  KEY `IGNORE_ME` (`IGNORANT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=55 ;

-- --------------------------------------------------------

--
-- Структура таблицы `journal_relations`
--

CREATE TABLE IF NOT EXISTS `journal_relations` (
  `FORUM_ID` bigint(20) NOT NULL,
  `FRIENDLY_FORUM_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`FORUM_ID`,`FRIENDLY_FORUM_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `journal_settings`
--

CREATE TABLE IF NOT EXISTS `journal_settings` (
  `JOURNAL_SETTINGS_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FORUM_ID` bigint(20) NOT NULL,
  `ALIAS` varchar(20) DEFAULT NULL,
  `REQUESTED_ALIAS` varchar(20) DEFAULT NULL,
  `SKIN_TEMPLATE_ID` int(11) DEFAULT NULL,
  `LAST_MESSAGE_DATE` datetime DEFAULT NULL,
  PRIMARY KEY (`JOURNAL_SETTINGS_ID`),
  UNIQUE KEY `USER_ID` (`FORUM_ID`),
  UNIQUE KEY `ALIAS` (`ALIAS`),
  KEY `LAST_MESSAGE_DATE` (`LAST_MESSAGE_DATE`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=477 ;

-- --------------------------------------------------------

--
-- Структура таблицы `journal_skins`
--

CREATE TABLE IF NOT EXISTS `journal_skins` (
  `SKIN_ID` int(11) NOT NULL AUTO_INCREMENT,
  `CREATED` datetime NOT NULL,
  `TEMPLATE_ID` bigint(20) NOT NULL,
  `TITLE` varchar(250) NOT NULL,
  `AUTHOR` varchar(100) DEFAULT NULL,
  `SCREENSHOT` varchar(100) DEFAULT NULL,
  `IS_DEFAULT` tinyint(1) NOT NULL DEFAULT '0',
  `IS_FRIENDLY` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`SKIN_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `journal_templates`
--

CREATE TABLE IF NOT EXISTS `journal_templates` (
  `TEMPLATE_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FORUM_ID` bigint(20) DEFAULT NULL,
  `TITLE` text NOT NULL,
  `BODY_OLD` text NOT NULL,
  `BODY` text NOT NULL,
  `MESSAGE` text NOT NULL,
  `CSS` text NOT NULL,
  `UPDATED` datetime DEFAULT NULL,
  PRIMARY KEY (`TEMPLATE_ID`),
  KEY `UPDATED` (`UPDATED`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=323 ;

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `MESSAGE_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ROOM_ID` int(11) NOT NULL DEFAULT '0',
  `USER_ID` bigint(20) DEFAULT NULL,
  `TO_USER_ID` bigint(20) DEFAULT NULL,
  `TEXT` text NOT NULL,
  `DATE` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`MESSAGE_ID`),
  KEY `ROOM_ID` (`ROOM_ID`),
  KEY `TO_USER_ID` (`TO_USER_ID`),
  KEY `AUTHOR` (`USER_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='All chat messages' AUTO_INCREMENT=326524 ;

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `OWNER_ID` bigint(20) NOT NULL,
  `TITLE` varchar(250) NOT NULL,
  `DESCRIPTION` text NOT NULL,
  PRIMARY KEY (`OWNER_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `news_records`
--

CREATE TABLE IF NOT EXISTS `news_records` (
  `NEWS_RECORD_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `OWNER_ID` bigint(20) NOT NULL,
  `AUTHOR_ID` bigint(20) NOT NULL,
  `DATE` datetime NOT NULL,
  `TITLE` varchar(250) NOT NULL,
  `CONTENT` text NOT NULL,
  `IS_HIDDEN` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`NEWS_RECORD_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Структура таблицы `nicknames`
--

CREATE TABLE IF NOT EXISTS `nicknames` (
  `NICKNAME_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `USER_ID` bigint(20) NOT NULL DEFAULT '0',
  `NAME` varchar(20) NOT NULL DEFAULT '',
  `IS_SELECTED` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`NICKNAME_ID`),
  KEY `USER NICKNAME` (`USER_ID`,`IS_SELECTED`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Users alternative names' AUTO_INCREMENT=758 ;

-- --------------------------------------------------------

--
-- Структура таблицы `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `USER_ID` bigint(20) NOT NULL,
  `POSTING` tinyint(1) NOT NULL DEFAULT '1',
  `BOLD` tinyint(1) NOT NULL DEFAULT '0',
  `ITALIC` tinyint(1) NOT NULL DEFAULT '0',
  `UNDERLINE` tinyint(1) NOT NULL DEFAULT '0',
  `TOPIC` tinyint(1) NOT NULL DEFAULT '0',
  `TOPIC_LOCK` tinyint(1) NOT NULL DEFAULT '0',
  `KICK` tinyint(1) NOT NULL DEFAULT '0',
  `BAN` tinyint(1) NOT NULL DEFAULT '0',
  `TREE` tinyint(1) NOT NULL DEFAULT '0',
  `BOTS` tinyint(1) NOT NULL DEFAULT '0',
  `ADMIN` tinyint(1) NOT NULL DEFAULT '0',
  `SUPER_ADMIN` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`USER_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='Users permissions';

-- --------------------------------------------------------

--
-- Структура таблицы `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `PROFILE_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `USER_ID` bigint(20) NOT NULL DEFAULT '0',
  `EMAIL` varchar(250) NOT NULL DEFAULT '',
  `NAME` text,
  `GENDER` enum('f','m') DEFAULT NULL,
  `BIRTHDAY` varchar(10) DEFAULT NULL,
  `CITY` varchar(100) DEFAULT NULL,
  `ICQ` varchar(20) DEFAULT NULL,
  `URL` varchar(255) DEFAULT NULL,
  `PHOTO` varchar(100) DEFAULT NULL,
  `PHOTO_UPLOAD_DATE` datetime DEFAULT NULL,
  `AVATAR` varchar(100) DEFAULT NULL,
  `ABOUT` text,
  `REGISTERED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LAST_VISIT` datetime DEFAULT NULL,
  `GENERATION` tinyint(4) DEFAULT '-1',
  `RATING` bigint(20) NOT NULL DEFAULT '0',
  `LAST_RATING` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PROFILE_ID`),
  UNIQUE KEY `USER_ID` (`USER_ID`),
  KEY `Generation` (`GENERATION`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Users profiles' AUTO_INCREMENT=7475 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `IDS` bigint(20) NOT NULL,
  `TYPE` enum('profile','journal') NOT NULL DEFAULT 'profile',
  `RATING` int(11) NOT NULL DEFAULT '0',
  `DATE` date NOT NULL,
  `IP` varchar(15) DEFAULT NULL,
  KEY `USER_ID` (`IDS`),
  KEY `DATE` (`DATE`),
  KEY `TYPE` (`TYPE`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `registry`
--

CREATE TABLE IF NOT EXISTS `registry` (
  `REGISTRY_KEY` varchar(100) NOT NULL,
  `VALUE` varchar(100) NOT NULL,
  PRIMARY KEY (`REGISTRY_KEY`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `rooms`
--

CREATE TABLE IF NOT EXISTS `rooms` (
  `ROOM_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `OWNER_ID` bigint(20) DEFAULT NULL,
  `TITLE` varchar(50) NOT NULL DEFAULT '',
  `TOPIC` varchar(255) DEFAULT '',
  `TOPIC_LOCK` tinyint(1) NOT NULL DEFAULT '0',
  `TOPIC_AUTHOR_ID` bigint(20) NOT NULL DEFAULT '0',
  `IS_LOCKED` tinyint(1) NOT NULL DEFAULT '0',
  `IS_INVITATION_REQUIRED` tinyint(1) NOT NULL DEFAULT '0',
  `IS_DELETED` tinyint(1) NOT NULL,
  `BEEN_VISITED` tinyint(1) NOT NULL,
  `CHECK_SUM` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`ROOM_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Chat rooms' AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

--
-- Структура таблицы `room_users`
--

CREATE TABLE IF NOT EXISTS `room_users` (
  `ROOM_ID` bigint(20) NOT NULL DEFAULT '0',
  `USER_ID` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ROOM_ID`,`USER_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Users who hve access to private rooms';

-- --------------------------------------------------------

--
-- Структура таблицы `scheduled_tasks`
--

CREATE TABLE IF NOT EXISTS `scheduled_tasks` (
  `SCHEDULED_TASK_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TYPE` enum('unban','status','expired_sessions','ratings') NOT NULL,
  `EXECUTION_DATE` datetime NOT NULL,
  `PERIODICITY` int(4) DEFAULT NULL COMMENT 'In minutes',
  `PARAMETER1` varchar(20) DEFAULT NULL,
  `PARAMETER2` varchar(20) DEFAULT NULL,
  `PARAMETER3` varchar(20) DEFAULT NULL,
  `TRANSACTION_GUID` varchar(10) DEFAULT NULL,
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`SCHEDULED_TASK_ID`),
  UNIQUE KEY `Unique type & parameters` (`TYPE`,`PARAMETER1`,`PARAMETER2`,`PARAMETER3`),
  KEY `TYPE` (`TYPE`),
  KEY `EXECUTION_DATE` (`EXECUTION_DATE`),
  KEY `TRANSACTION` (`TRANSACTION_GUID`),
  KEY `IS_ACTIVE` (`IS_ACTIVE`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=2443 ;

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `SETTINGS_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `USER_ID` bigint(20) NOT NULL DEFAULT '0',
  `ENTER_MESSAGE` text,
  `QUIT_MESSAGE` text,
  `STATUS` text,
  `FONT_COLOR` varchar(30) NOT NULL DEFAULT 'white',
  `FONT_SIZE` int(1) NOT NULL DEFAULT '3',
  `FONT_FACE` varchar(40) DEFAULT NULL,
  `FONT_BOLD` tinyint(1) NOT NULL DEFAULT '0',
  `FONT_ITALIC` tinyint(1) NOT NULL DEFAULT '0',
  `FONT_UNDERLINED` tinyint(1) NOT NULL DEFAULT '0',
  `IGNORE_FONTS` tinyint(1) NOT NULL DEFAULT '0',
  `IGNORE_COLORS` tinyint(1) NOT NULL DEFAULT '0',
  `IGNORE_FONT_SIZE` tinyint(1) NOT NULL DEFAULT '0',
  `IGNORE_FONT_STYLE` tinyint(1) NOT NULL DEFAULT '0',
  `RECEIVE_WAKEUPS` tinyint(1) NOT NULL DEFAULT '1',
  `CONFIRM_PRIVATES` tinyint(1) NOT NULL DEFAULT '1',
  `FRAMESET` enum('0','1','2','3') NOT NULL DEFAULT '0',
  PRIMARY KEY (`SETTINGS_ID`),
  UNIQUE KEY `USER_ID` (`USER_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Chat settings' AUTO_INCREMENT=7513 ;

-- --------------------------------------------------------

--
-- Структура таблицы `statuses`
--

CREATE TABLE IF NOT EXISTS `statuses` (
  `STATUS_ID` int(11) NOT NULL AUTO_INCREMENT,
  `RIGHTS` int(3) NOT NULL DEFAULT '1',
  `TITLE` varchar(50) NOT NULL DEFAULT '',
  `COLOR` varchar(30) DEFAULT 'white',
  `IS_SPECIAL` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`STATUS_ID`),
  KEY `IS_SPECIAL` (`IS_SPECIAL`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Chat statuses' AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `TAG_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TITLE` varchar(100) NOT NULL,
  PRIMARY KEY (`TAG_ID`),
  UNIQUE KEY `UNIQUE TITLE` (`TITLE`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Forum/Journal/Gallery records tags (labels)' AUTO_INCREMENT=363 ;

-- --------------------------------------------------------

--
-- Структура таблицы `todo`
--

CREATE TABLE IF NOT EXISTS `todo` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TITLE` text NOT NULL,
  `PRIORITY` tinyint(4) NOT NULL,
  `IS_PLANNED` tinyint(1) NOT NULL,
  `FINISH_DATE` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tree`
--

CREATE TABLE IF NOT EXISTS `tree` (
  `NODE_ID` double NOT NULL AUTO_INCREMENT,
  `FIRST_USER_ID` double NOT NULL DEFAULT '0',
  `SECOND_USER_ID` double NOT NULL DEFAULT '0',
  `RELATION_TYPE` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`NODE_ID`),
  KEY `INDEX FIRST` (`FIRST_USER_ID`),
  KEY `INDEX SECOND` (`SECOND_USER_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2434 ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `USER_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `LOGIN` varchar(20) NOT NULL DEFAULT '',
  `PASSWORD` text NOT NULL,
  `ROOM_ID` bigint(20) DEFAULT NULL,
  `STATUS_ID` int(11) DEFAULT NULL,
  `SESSION` varchar(20) DEFAULT NULL,
  `SESSION_PONG` datetime DEFAULT NULL,
  `SESSION_CHECK` varchar(20) DEFAULT NULL,
  `SESSION_ADDRESS` varchar(200) NOT NULL DEFAULT '',
  `AWAY_MESSAGE` text,
  `AWAY_TIME` datetime DEFAULT NULL,
  `KICK_MESSAGES` text NOT NULL,
  `BANNED_TILL` datetime DEFAULT NULL,
  `BAN_REASON` text,
  `BANNED_BY` bigint(20) DEFAULT NULL,
  `GUID` varchar(11) DEFAULT NULL,
  `CHECK_SUM` bigint(20) DEFAULT NULL,
  `IS_DELETED` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is user deleted',
  PRIMARY KEY (`USER_ID`),
  UNIQUE KEY `LOGIN` (`LOGIN`,`GUID`),
  KEY `ROOM_ID` (`ROOM_ID`),
  KEY `BANNED_BY` (`BANNED_BY`),
  KEY `Get user by session` (`SESSION`,`SESSION_ADDRESS`),
  KEY `EXPIRED SESSIONS` (`SESSION_PONG`),
  KEY `Get by two session keys` (`SESSION`,`SESSION_CHECK`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Chat users' AUTO_INCREMENT=7513 ;

-- --------------------------------------------------------

--
-- Структура таблицы `victorina_vocab`
--

CREATE TABLE IF NOT EXISTS `victorina_vocab` (
  `QUESTION_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `QUESTION` text,
  `ANSWER` text,
  `ASKED_TIMES` int(11) NOT NULL DEFAULT '0' COMMENT 'How many times has been asked',
  PRIMARY KEY (`QUESTION_ID`),
  KEY `ASKED_TIMES` (`ASKED_TIMES`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=33694 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wakeups`
--

CREATE TABLE IF NOT EXISTS `wakeups` (
  `WAKEUP_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `USER_ID` bigint(20) NOT NULL,
  `TO_USER_ID` bigint(20) NOT NULL,
  `DATE` datetime NOT NULL,
  `MESSAGE` text NOT NULL,
  `IS_READ` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`WAKEUP_ID`),
  KEY `TO_USER_ID` (`TO_USER_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Wake-up messages' AUTO_INCREMENT=1101 ;
