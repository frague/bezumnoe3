-- phpMyAdmin SQL Dump
-- version 3.1.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 16, 2009 at 10:27 PM
-- Server version: 5.0.77
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Chat`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_comments`
--

DROP TABLE IF EXISTS `admin_comments`;
CREATE TABLE IF NOT EXISTS `admin_comments` (
  `ADMIN_COMMENT_ID` bigint(20) NOT NULL auto_increment,
  `USER_ID` bigint(20) default NULL,
  `DATE` datetime NOT NULL,
  `CONTENT` text NOT NULL,
  `ADMIN_LOGIN` varchar(20) NOT NULL,
  `SEVERITY` enum('0','1','2') NOT NULL default '0',
  PRIMARY KEY  (`ADMIN_COMMENT_ID`),
  KEY `USER_ID` (`USER_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Admin comments to users' AUTO_INCREMENT=4400 ;

-- --------------------------------------------------------

--
-- Table structure for table `banned_addresses`
--

DROP TABLE IF EXISTS `banned_addresses`;
CREATE TABLE IF NOT EXISTS `banned_addresses` (
  `BAN_ID` int(11) NOT NULL auto_increment,
  `CONTENT` varchar(200) NOT NULL default '127.0.0.1',
  `TYPE` enum('ip','host') NOT NULL default 'ip',
  `COMMENT` text,
  `ADMIN_LOGIN` varchar(20) default NULL,
  `ADDED` datetime NOT NULL,
  `TILL` datetime default NULL,
  `BAN_CHAT` tinyint(1) NOT NULL default '0',
  `BAN_FORUM` tinyint(1) NOT NULL default '0',
  `BAN_JOURNAL` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`BAN_ID`),
  UNIQUE KEY `UNIQUE CONTENT` (`CONTENT`),
  KEY `CHAT BANS` (`BAN_CHAT`),
  KEY `FORUM BANS` (`BAN_FORUM`),
  KEY `JOURNAL BANS` (`BAN_JOURNAL`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Banned addresses' AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Table structure for table `captchas`
--

DROP TABLE IF EXISTS `captchas`;
CREATE TABLE IF NOT EXISTS `captchas` (
  `GUID` varchar(10) NOT NULL,
  `VALUE` varchar(10) NOT NULL,
  `DATE` datetime NOT NULL,
  PRIMARY KEY  (`GUID`),
  KEY `DATE` (`DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Table structure for table `forums`
--

DROP TABLE IF EXISTS `forums`;
CREATE TABLE IF NOT EXISTS `forums` (
  `FORUM_ID` bigint(20) NOT NULL auto_increment,
  `TYPE` enum('forum','journal','gallery') NOT NULL default 'forum',
  `TITLE` varchar(250) NOT NULL default '',
  `DESCRIPTION` text,
  `IS_PROTECTED` tinyint(1) NOT NULL default '0',
  `LINKED_ID` bigint(20) default NULL,
  `TOTAL_COUNT` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`FORUM_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=409 ;

-- --------------------------------------------------------

--
-- Table structure for table `forum_records`
--

DROP TABLE IF EXISTS `forum_records`;
CREATE TABLE IF NOT EXISTS `forum_records` (
  `RECORD_ID` int(11) NOT NULL auto_increment,
  `FORUM_ID` smallint(6) NOT NULL default '1',
  `IND` varchar(250) NOT NULL,
  `TYPE` enum('0','1','2') NOT NULL default '0',
  `AUTHOR` varchar(100) default NULL,
  `USER_ID` varchar(100) default NULL,
  `TITLE` varchar(250) default NULL,
  `CONTENT` text,
  `DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `IP` varchar(100) NOT NULL default '',
  `CLICKS` smallint(6) NOT NULL default '0',
  `GUID` varchar(10) default NULL,
  `IS_COMMENTABLE` tinyint(1) NOT NULL default '1',
  `IS_DELETED` tinyint(1) NOT NULL default '0',
  `UPDATE_DATE` datetime NOT NULL,
  `ANSWERS_COUNT` int(11) NOT NULL default '0',
  `DELETED_COUNT` int(11) default '0',
  PRIMARY KEY  (`RECORD_ID`),
  KEY `Forum Threads` (`IND`(4),`FORUM_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=123343 ;

-- --------------------------------------------------------

--
-- Table structure for table `forum_users`
--

DROP TABLE IF EXISTS `forum_users`;
CREATE TABLE IF NOT EXISTS `forum_users` (
  `FORUM_ID` bigint(20) NOT NULL,
  `USER_ID` bigint(20) NOT NULL,
  `ACCESS` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`FORUM_ID`,`USER_ID`),
  KEY `User access to forum` (`USER_ID`,`FORUM_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='Protected forums users';

-- --------------------------------------------------------

--
-- Table structure for table `ignores`
--

DROP TABLE IF EXISTS `ignores`;
CREATE TABLE IF NOT EXISTS `ignores` (
  `IGNORE_ID` bigint(20) NOT NULL auto_increment,
  `USER_ID` bigint(20) NOT NULL,
  `IGNORANT_ID` bigint(20) NOT NULL,
  PRIMARY KEY  (`IGNORE_ID`),
  UNIQUE KEY `PAIR` (`USER_ID`,`IGNORANT_ID`),
  KEY `I_IGNORE` (`USER_ID`),
  KEY `IGNORE_ME` (`IGNORANT_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `journal_relations`
--

DROP TABLE IF EXISTS `journal_relations`;
CREATE TABLE IF NOT EXISTS `journal_relations` (
  `FORUM_ID` bigint(20) NOT NULL,
  `FRIENDLY_FORUM_ID` bigint(20) NOT NULL,
  PRIMARY KEY  (`FORUM_ID`,`FRIENDLY_FORUM_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Table structure for table `journal_settings`
--

DROP TABLE IF EXISTS `journal_settings`;
CREATE TABLE IF NOT EXISTS `journal_settings` (
  `JOURNAL_SETTINGS_ID` bigint(20) NOT NULL auto_increment,
  `FORUM_ID` bigint(20) NOT NULL,
  `ALIAS` varchar(20) default NULL,
  `REQUESTED_ALIAS` varchar(20) default NULL,
  `SKIN_TEMPLATE_ID` int(11) default NULL,
  `LAST_MESSAGE_DATE` datetime default NULL,
  PRIMARY KEY  (`JOURNAL_SETTINGS_ID`),
  UNIQUE KEY `USER_ID` (`FORUM_ID`),
  UNIQUE KEY `ALIAS` (`ALIAS`),
  KEY `LAST_MESSAGE_DATE` (`LAST_MESSAGE_DATE`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=370 ;

-- --------------------------------------------------------

--
-- Table structure for table `journal_skins`
--

DROP TABLE IF EXISTS `journal_skins`;
CREATE TABLE IF NOT EXISTS `journal_skins` (
  `SKIN_ID` int(11) NOT NULL auto_increment,
  `CREATED` datetime NOT NULL,
  `TEMPLATE_ID` bigint(20) NOT NULL,
  `TITLE` varchar(250) NOT NULL,
  `AUTHOR` varchar(100) default NULL,
  `SCREENSHOT` varchar(100) default NULL,
  `IS_DEFAULT` tinyint(1) NOT NULL default '0',
  `IS_FRIENDLY` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`SKIN_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `journal_templates`
--

DROP TABLE IF EXISTS `journal_templates`;
CREATE TABLE IF NOT EXISTS `journal_templates` (
  `TEMPLATE_ID` bigint(20) NOT NULL auto_increment,
  `FORUM_ID` bigint(20) default NULL,
  `BODY` text NOT NULL,
  `MESSAGE` text NOT NULL,
  `CSS` text NOT NULL,
  `UPDATED` datetime default NULL,
  PRIMARY KEY  (`TEMPLATE_ID`),
  KEY `UPDATED` (`UPDATED`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=208 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `MESSAGE_ID` bigint(20) NOT NULL auto_increment,
  `ROOM_ID` int(11) NOT NULL default '0',
  `USER_ID` bigint(20) default NULL,
  `TO_USER_ID` bigint(20) default NULL,
  `TEXT` text NOT NULL,
  `DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`MESSAGE_ID`),
  KEY `ROOM_ID` (`ROOM_ID`),
  KEY `TO_USER_ID` (`TO_USER_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='All chat messages' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `OWNER_ID` bigint(20) NOT NULL,
  `TITLE` varchar(250) NOT NULL,
  `DESCRIPTION` text NOT NULL,
  PRIMARY KEY  (`OWNER_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Table structure for table `news_records`
--

DROP TABLE IF EXISTS `news_records`;
CREATE TABLE IF NOT EXISTS `news_records` (
  `NEWS_RECORD_ID` bigint(20) NOT NULL auto_increment,
  `OWNER_ID` bigint(20) NOT NULL,
  `AUTHOR_ID` bigint(20) NOT NULL,
  `DATE` datetime NOT NULL,
  `TITLE` varchar(250) NOT NULL,
  `CONTENT` text NOT NULL,
  `IS_HIDDEN` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`NEWS_RECORD_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `nicknames`
--

DROP TABLE IF EXISTS `nicknames`;
CREATE TABLE IF NOT EXISTS `nicknames` (
  `NICKNAME_ID` bigint(20) NOT NULL auto_increment,
  `USER_ID` bigint(20) NOT NULL default '0',
  `NAME` varchar(20) NOT NULL default '',
  `IS_SELECTED` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`NICKNAME_ID`),
  KEY `Selected` (`IS_SELECTED`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Users alternative names' AUTO_INCREMENT=418 ;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `PROFILE_ID` bigint(20) NOT NULL auto_increment,
  `USER_ID` bigint(20) NOT NULL default '0',
  `EMAIL` varchar(250) NOT NULL default '',
  `NAME` text,
  `GENDER` varchar(1) default NULL,
  `BIRTHDAY` varchar(10) default NULL,
  `CITY` varchar(100) default NULL,
  `ICQ` varchar(20) default NULL,
  `URL` varchar(255) default NULL,
  `PHOTO` varchar(100) default NULL,
  `AVATAR` varchar(100) default NULL,
  `ABOUT` text,
  `REGISTERED` datetime NOT NULL default '0000-00-00 00:00:00',
  `LAST_VISIT` datetime default NULL,
  `GENERATION` tinyint(4) default '-1',
  PRIMARY KEY  (`PROFILE_ID`),
  UNIQUE KEY `USER_ID` (`USER_ID`),
  KEY `Generation` (`GENERATION`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Users profiles' AUTO_INCREMENT=6481 ;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `ROOM_ID` bigint(20) NOT NULL auto_increment,
  `OWNER_ID` bigint(20) default NULL,
  `TITLE` varchar(50) NOT NULL default '',
  `TOPIC` varchar(255) default '',
  `TOPIC_LOCK` tinyint(1) NOT NULL default '0',
  `TOPIC_AUTHOR_ID` bigint(20) NOT NULL default '0',
  `IS_LOCKED` tinyint(1) NOT NULL default '0',
  `IS_INVITATION_REQUIRED` tinyint(1) NOT NULL default '0',
  `IS_DELETED` tinyint(1) NOT NULL,
  `BEEN_VISITED` tinyint(1) NOT NULL,
  PRIMARY KEY  (`ROOM_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Chat rooms' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `room_users`
--

DROP TABLE IF EXISTS `room_users`;
CREATE TABLE IF NOT EXISTS `room_users` (
  `ROOM_ID` bigint(20) NOT NULL default '0',
  `USER_ID` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`ROOM_ID`,`USER_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Users who hve access to private rooms';

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_tasks`
--

DROP TABLE IF EXISTS `scheduled_tasks`;
CREATE TABLE IF NOT EXISTS `scheduled_tasks` (
  `SCHEDULED_TASK_ID` bigint(20) NOT NULL auto_increment,
  `TYPE` enum('unban','status') NOT NULL,
  `EXECUTION_DATE` datetime NOT NULL,
  `PERIODICITY` int(4) default NULL COMMENT 'In seconds',
  `PARAMETER1` varchar(20) default NULL,
  `PARAMETER2` varchar(20) default NULL,
  `PARAMETER3` varchar(20) default NULL,
  `TRANSACTION_GUID` varchar(10) default NULL,
  PRIMARY KEY  (`SCHEDULED_TASK_ID`),
  UNIQUE KEY `Unique type & parameters` (`TYPE`,`PARAMETER1`,`PARAMETER2`,`PARAMETER3`),
  KEY `TYPE` (`TYPE`),
  KEY `EXECUTION_DATE` (`EXECUTION_DATE`),
  KEY `TRANSACTION` (`TRANSACTION_GUID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=6066 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `SETTINGS_ID` bigint(20) NOT NULL auto_increment,
  `USER_ID` bigint(20) NOT NULL default '0',
  `ENTER_MESSAGE` text,
  `QUIT_MESSAGE` text,
  `STATUS` text,
  `FONT_COLOR` varchar(30) NOT NULL default 'white',
  `FONT_SIZE` int(1) NOT NULL default '3',
  `FONT_FACE` varchar(40) default NULL,
  `FONT_BOLD` tinyint(1) NOT NULL default '0',
  `FONT_ITALIC` tinyint(1) NOT NULL default '0',
  `FONT_UNDERLINED` tinyint(1) NOT NULL default '0',
  `IGNORE_FONTS` tinyint(1) NOT NULL default '0',
  `IGNORE_COLORS` tinyint(1) NOT NULL default '0',
  `IGNORE_FONT_SIZE` tinyint(1) NOT NULL default '0',
  `IGNORE_FONT_STYLE` tinyint(1) NOT NULL default '0',
  `RECEIVE_WAKEUPS` tinyint(1) NOT NULL default '1',
  `CONFIRM_PRIVATES` tinyint(1) NOT NULL default '1',
  `FRAMESET` enum('0','1','2','3') NOT NULL default '0',
  PRIMARY KEY  (`SETTINGS_ID`),
  UNIQUE KEY `USER_ID` (`USER_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Chat settings' AUTO_INCREMENT=6481 ;

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
CREATE TABLE IF NOT EXISTS `statuses` (
  `STATUS_ID` int(11) NOT NULL auto_increment,
  `RIGHTS` int(3) NOT NULL default '1',
  `TITLE` varchar(50) NOT NULL default '',
  `COLOR` varchar(30) default 'white',
  `IS_SPECIAL` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`STATUS_ID`),
  KEY `IS_SPECIAL` (`IS_SPECIAL`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Chat statuses' AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `todo`
--

DROP TABLE IF EXISTS `todo`;
CREATE TABLE IF NOT EXISTS `todo` (
  `ID` int(11) NOT NULL auto_increment,
  `TITLE` text NOT NULL,
  `PRIORITY` tinyint(4) NOT NULL,
  `IS_PLANNED` tinyint(1) NOT NULL,
  `FINISH_DATE` datetime default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tree`
--

DROP TABLE IF EXISTS `tree`;
CREATE TABLE IF NOT EXISTS `tree` (
  `NODE_ID` double NOT NULL auto_increment,
  `FIRST_USER_ID` double NOT NULL default '0',
  `SECOND_USER_ID` double NOT NULL default '0',
  `RELATION_TYPE` char(1) NOT NULL default '',
  PRIMARY KEY  (`NODE_ID`),
  KEY `INDEX FIRST` (`FIRST_USER_ID`),
  KEY `INDEX SECOND` (`SECOND_USER_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2434 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `USER_ID` bigint(20) NOT NULL auto_increment,
  `LOGIN` varchar(20) NOT NULL default '',
  `PASSWORD` text NOT NULL,
  `ROOM_ID` bigint(20) default NULL,
  `STATUS_ID` int(11) default NULL,
  `SESSION` varchar(20) default NULL,
  `SESSION_PONG` datetime default NULL,
  `SESSION_ADDRESS` varchar(200) NOT NULL default '',
  `AWAY_MESSAGE` text,
  `AWAY_TIME` datetime default NULL,
  `KICK_MESSAGES` text NOT NULL,
  `BANNED_TILL` datetime default NULL,
  `BAN_REASON` text,
  `BANNED_BY` bigint(20) default NULL,
  `GUID` varchar(10) default '',
  PRIMARY KEY  (`USER_ID`),
  UNIQUE KEY `LOGIN` (`LOGIN`,`GUID`),
  KEY `ROOM_ID` (`ROOM_ID`),
  KEY `BANNED_BY` (`BANNED_BY`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Chat users' AUTO_INCREMENT=6481 ;

-- --------------------------------------------------------

--
-- Table structure for table `wakeups`
--

DROP TABLE IF EXISTS `wakeups`;
CREATE TABLE IF NOT EXISTS `wakeups` (
  `WAKEUP_ID` bigint(20) NOT NULL auto_increment,
  `USER_ID` bigint(20) NOT NULL,
  `TO_USER_ID` bigint(20) NOT NULL,
  `DATE` datetime NOT NULL,
  `MESSAGE` text NOT NULL,
  `IS_READ` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`WAKEUP_ID`),
  KEY `TO_USER_ID` (`TO_USER_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='Wake-up messages' AUTO_INCREMENT=1 ;
