-- MySQL dump 10.13  Distrib 5.6.44, for Linux (x86_64)
--
-- Host: localhost    Database: cloudworks_live
-- ------------------------------------------------------
-- Server version	5.6.44-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `altc_tmp`
--

DROP TABLE IF EXISTS `altc_tmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `altc_tmp` (
  `title` varchar(3000) DEFAULT NULL,
  `authors_names` varchar(3000) DEFAULT NULL,
  `abstract` varchar(3000) DEFAULT NULL,
  `crowd_vine_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_client`
--

DROP TABLE IF EXISTS `api_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_client` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Potentially more than 1 key per user.',
  `user_id` int(11) NOT NULL COMMENT 'Tie the API key to an existing user.',
  `api_key` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The key, probably 16 characters.',
  `client_url` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Tie the API key to a URL, or IP address?',
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `key_id` (`client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_log`
--

DROP TABLE IF EXISTS `api_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_log` (
  `client_id` int(11) DEFAULT NULL,
  `timestamp` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `message` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referrer` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge`
--

DROP TABLE IF EXISTS `badge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badge` (
  `badge_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `criteria` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `type` enum('verifier','crowdsource') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'verifier',
  `num_approves` int(11) DEFAULT NULL,
  `issuer_name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`badge_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge_application`
--

DROP TABLE IF EXISTS `badge_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badge_application` (
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  `evidence_URL` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `issued` int(11) DEFAULT NULL,
  PRIMARY KEY (`application_id`)
) ENGINE=InnoDB AUTO_INCREMENT=565 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge_decision`
--

DROP TABLE IF EXISTS `badge_decision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badge_decision` (
  `application_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `feedback` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `decision` enum('approved','rejected') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge_verifier`
--

DROP TABLE IF EXISTS `badge_verifier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badge_verifier` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blog_comment`
--

DROP TABLE IF EXISTS `blog_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `moderate` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=932 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blog_post`
--

DROP TABLE IF EXISTS `blog_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_post` (
  `post_id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` longtext COLLATE utf8_unicode_ci,
  `created` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1532 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `session_data` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloud`
--

DROP TABLE IF EXISTS `cloud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloud` (
  `cloud_id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(165) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `summary` longtext COLLATE utf8_unicode_ci,
  `url` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `moderate` tinyint(1) NOT NULL DEFAULT '0',
  `omit_from_new_list` tinyint(1) NOT NULL DEFAULT '0',
  `primary_url` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `call_deadline` int(10) DEFAULT NULL,
  `event_date` int(10) DEFAULT NULL,
  `display_event` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`cloud_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12034 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloud_content`
--

DROP TABLE IF EXISTS `cloud_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloud_content` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cloud_id` int(11) NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `moderate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2584 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloud_embed`
--

DROP TABLE IF EXISTS `cloud_embed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloud_embed` (
  `embed_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cloud_id` int(11) unsigned NOT NULL,
  `url` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  `title` varchar(280) COLLATE utf8_unicode_ci DEFAULT NULL,
  `moderate` tinyint(1) NOT NULL,
  `accessible_alternative` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`embed_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3341 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloud_followed`
--

DROP TABLE IF EXISTS `cloud_followed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloud_followed` (
  `cloud_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `timestamp` int(10) DEFAULT NULL,
  PRIMARY KEY (`cloud_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloud_gadget`
--

DROP TABLE IF EXISTS `cloud_gadget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloud_gadget` (
  `cloud_id` int(11) unsigned NOT NULL,
  `gadget_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`cloud_id`,`gadget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloud_link`
--

DROP TABLE IF EXISTS `cloud_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloud_link` (
  `cloud_id` int(11) DEFAULT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `link_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(140) DEFAULT NULL,
  `moderate` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`link_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5518 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloud_popular`
--

DROP TABLE IF EXISTS `cloud_popular`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloud_popular` (
  `cloud_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`cloud_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloud_reference`
--

DROP TABLE IF EXISTS `cloud_reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloud_reference` (
  `reference_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cloud_id` int(11) NOT NULL,
  `reference_text` varchar(500) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `moderate` tinyint(1) DEFAULT '0',
  `timestamp` int(11) DEFAULT NULL,
  PRIMARY KEY (`reference_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1349 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloudscape`
--

DROP TABLE IF EXISTS `cloudscape`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloudscape` (
  `cloudscape_id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `summary` tinytext COLLATE utf8_unicode_ci,
  `body` longtext COLLATE utf8_unicode_ci,
  `open` tinyint(4) DEFAULT '1',
  `twitter_tag` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `featured` bit(1) DEFAULT NULL,
  `image_path` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `moderate` tinyint(1) NOT NULL DEFAULT '0',
  `image_attr_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_attr_link` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` int(10) DEFAULT NULL,
  `end_date` int(10) DEFAULT NULL,
  `location` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `omit_from_new_list` tinyint(1) NOT NULL DEFAULT '0',
  `shortcut` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `display_show_all_sections` tinyint(1) NOT NULL DEFAULT '1',
  `colour` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `display_event` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`cloudscape_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3034 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloudscape_admin`
--

DROP TABLE IF EXISTS `cloudscape_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloudscape_admin` (
  `cloudscape_id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`cloudscape_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloudscape_attended`
--

DROP TABLE IF EXISTS `cloudscape_attended`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloudscape_attended` (
  `cloudscape_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `timestamp` int(10) DEFAULT NULL,
  PRIMARY KEY (`cloudscape_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloudscape_cloud`
--

DROP TABLE IF EXISTS `cloudscape_cloud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloudscape_cloud` (
  `cloud_id` int(10) DEFAULT NULL,
  `cloudscape_id` int(10) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloudscape_email`
--

DROP TABLE IF EXISTS `cloudscape_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloudscape_email` (
  `cloudscape_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `subject` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloudscape_followed`
--

DROP TABLE IF EXISTS `cloudscape_followed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloudscape_followed` (
  `cloudscape_id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(10) DEFAULT NULL,
  PRIMARY KEY (`cloudscape_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloudscape_popular`
--

DROP TABLE IF EXISTS `cloudscape_popular`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloudscape_popular` (
  `cloudscape_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`cloudscape_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloudscape_poster`
--

DROP TABLE IF EXISTS `cloudscape_poster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloudscape_poster` (
  `cloudscape_id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`cloudscape_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cloudscape_section`
--

DROP TABLE IF EXISTS `cloudscape_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cloudscape_section` (
  `section_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cloudscape_id` int(10) unsigned NOT NULL,
  `title` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=MyISAM AUTO_INCREMENT=194 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `cloud_id` int(10) NOT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `moderate` tinyint(1) NOT NULL DEFAULT '0',
  `modified` int(11) DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13174 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `iso` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `iso3` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `event_id` int(10) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `follow_item_id` int(10) NOT NULL,
  `event_item_id` int(10) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `follow_item_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `omit_from_site_cloudstream` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM AUTO_INCREMENT=106685 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `favourite`
--

DROP TABLE IF EXISTS `favourite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favourite` (
  `user_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `item_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(10) NOT NULL,
  PRIMARY KEY (`user_id`,`item_id`,`item_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `featured_cloudscape`
--

DROP TABLE IF EXISTS `featured_cloudscape`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `featured_cloudscape` (
  `cloudscape_id` int(10) NOT NULL,
  `order` int(3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flagged_spam`
--

DROP TABLE IF EXISTS `flagged_spam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flagged_spam` (
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gadget`
--

DROP TABLE IF EXISTS `gadget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gadget` (
  `gadget_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `accessible_alternative` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`gadget_id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `item_id` int(10) DEFAULT NULL,
  `item_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `search_term` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  KEY `cloudstream_index` (`item_type`,`item_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `message_id` int(10) NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) NOT NULL,
  `author_user_id` int(10) NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `created` int(10) NOT NULL,
  `moderate` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2700 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message_recipient`
--

DROP TABLE IF EXISTS `message_recipient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_recipient` (
  `message_id` int(10) NOT NULL DEFAULT '0',
  `recipient_user_id` int(10) NOT NULL,
  `is_new` tinyint(1) NOT NULL DEFAULT '1',
  `is_spam` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`,`recipient_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message_thread`
--

DROP TABLE IF EXISTS `message_thread`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_thread` (
  `thread_id` int(10) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `author_user_id` int(10) NOT NULL,
  `created` int(10) DEFAULT NULL,
  `moderate` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`thread_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2519 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message_thread_participant`
--

DROP TABLE IF EXISTS `message_thread_participant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_thread_participant` (
  `thread_id` int(10) NOT NULL DEFAULT '0',
  `participant_user_id` int(10) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`thread_id`,`participant_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page` (
  `section` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `title` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lang` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`section`,`name`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `section_cloud`
--

DROP TABLE IF EXISTS `section_cloud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section_cloud` (
  `section_id` int(11) unsigned NOT NULL,
  `cloud_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`section_id`,`cloud_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `title` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_output_section` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Settings and configuration variables';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shortcut`
--

DROP TABLE IF EXISTS `shortcut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shortcut` (
  `shortcut` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `URl` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`shortcut`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_news`
--

DROP TABLE IF EXISTS `site_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_news` (
  `body` text COLLATE utf8_unicode_ci,
  `user_id` int(11) unsigned NOT NULL,
  `timestamp` int(11) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `item_id` int(10) NOT NULL,
  `tag` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `item_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `tag_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23428 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `forgotten_password_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_visit` datetime DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `change_email_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_email` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `do_not_delete` int(1) NOT NULL DEFAULT '0' COMMENT 'Emeritus, founders, past & present significant people.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11687 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_followed`
--

DROP TABLE IF EXISTS `user_followed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_followed` (
  `followed_user_id` int(11) NOT NULL,
  `following_user_id` int(11) NOT NULL,
  `timestamp` int(10) DEFAULT NULL,
  PRIMARY KEY (`followed_user_id`,`following_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_gadget`
--

DROP TABLE IF EXISTS `user_gadget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_gadget` (
  `user_id` int(11) NOT NULL,
  `gadget_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`gadget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_picture`
--

DROP TABLE IF EXISTS `user_picture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_picture` (
  `user_id` int(10) NOT NULL,
  `picture` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profile` (
  `id` int(11) NOT NULL,
  `fullname` varchar(140) COLLATE utf8_unicode_ci NOT NULL,
  `institution` varchar(140) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `twitter_username` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `homepage` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rss` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_follow` int(1) NOT NULL DEFAULT '1',
  `email_comment` int(1) NOT NULL DEFAULT '1',
  `email_comment_followup` int(1) NOT NULL DEFAULT '1',
  `email_news` int(1) NOT NULL DEFAULT '1',
  `display_email` int(1) NOT NULL DEFAULT '0',
  `whitelist` int(1) NOT NULL DEFAULT '0',
  `email_events_attending` int(1) NOT NULL DEFAULT '1',
  `do_not_use_editor` int(1) NOT NULL DEFAULT '0',
  `email_message_notify` int(1) NOT NULL DEFAULT '1',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `moderate` tinyint(1) DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_temp`
--

DROP TABLE IF EXISTS `user_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `activation_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fullname` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `institution` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_FI_1` (`country_id`)
) ENGINE=MyISAM AUTO_INCREMENT=101771 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-07-09 13:54:33
