
DROP TABLE IF EXISTS `api_client`;

-- command split --

CREATE TABLE `api_client` (
  `client_id` int(11) NOT NULL auto_increment COMMENT 'Potentially more than 1 key per user.',
  `user_id` int(11) NOT NULL COMMENT 'Tie the API key to an existing user.',
  `api_key` varchar(50) collate utf8_unicode_ci NOT NULL COMMENT 'The key, probably 16 characters.',
  `client_url` varchar(250) collate utf8_unicode_ci default NULL COMMENT 'Tie the API key to a URL, or IP address?',
  PRIMARY KEY  (`client_id`),
  UNIQUE KEY `key_id` (`client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `api_log`;

-- command split --

CREATE TABLE `api_log` (
  `client_id` int(11) default NULL,
  `timestamp` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `message` varchar(250) collate utf8_unicode_ci default NULL,
  `request` varchar(250) collate utf8_unicode_ci default NULL,
  `user_agent` varchar(250) collate utf8_unicode_ci default NULL,
  `referrer` varchar(250) collate utf8_unicode_ci default NULL,
  `ip` varchar(30) collate utf8_unicode_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `ci_sessions`;

-- command split --

CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) collate utf8_unicode_ci NOT NULL default '0',
  `ip_address` varchar(16) collate utf8_unicode_ci NOT NULL default '0',
  `user_agent` varchar(50) collate utf8_unicode_ci NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `session_data` text collate utf8_unicode_ci,
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloud`;

-- command split --

CREATE TABLE `cloud` (
  `cloud_id` int(10) NOT NULL auto_increment,
  `title` varchar(165) collate utf8_unicode_ci NOT NULL,
  `body` text collate utf8_unicode_ci,
  `summary` longtext collate utf8_unicode_ci,
  `url` varchar(2048) collate utf8_unicode_ci default NULL,
  `user_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `contact` varchar(128) collate utf8_unicode_ci default NULL,
  `moderate` tinyint(1) NOT NULL default '0',
  `omit_from_new_list` tinyint(1) NOT NULL default '0',
  `primary_url` varchar(2048) collate utf8_unicode_ci default NULL,
  `call_deadline` int(10) default NULL,
  PRIMARY KEY  (`cloud_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloud_content`;

-- command split --

CREATE TABLE `cloud_content` (
  `content_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `cloud_id` int(11) NOT NULL,
  `body` text collate utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) default NULL,
  `moderate` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloud_embed`;

-- command split --

CREATE TABLE `cloud_embed` (
  `embed_id` int(11) unsigned NOT NULL auto_increment,
  `cloud_id` int(11) unsigned NOT NULL,
  `url` varchar(1000) collate utf8_unicode_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  `title` varchar(280) collate utf8_unicode_ci default NULL,
  `moderate` tinyint(1) NOT NULL,
  `accessible_alternative` text collate utf8_unicode_ci,
  PRIMARY KEY  (`embed_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloud_followed`;

-- command split --

CREATE TABLE `cloud_followed` (
  `cloud_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `timestamp` int(10) default NULL,
  PRIMARY KEY  (`cloud_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloud_gadget`;

-- command split --

CREATE TABLE `cloud_gadget` (
  `cloud_id` int(11) unsigned NOT NULL,
  `gadget_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`cloud_id`,`gadget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloud_link`;

-- command split --

CREATE TABLE `cloud_link` (
  `cloud_id` int(11) default NULL,
  `url` varchar(2048) default NULL,
  `user_id` int(11) default NULL,
  `timestamp` int(11) default NULL,
  `link_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(140) default NULL,
  `moderate` tinyint(1) NOT NULL default '0',
  `type` varchar(20) default NULL,
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloud_popular`;

-- command split --

CREATE TABLE `cloud_popular` (
  `cloud_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`cloud_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloud_reference`;

-- command split --

CREATE TABLE `cloud_reference` (
  `reference_id` int(11) unsigned NOT NULL auto_increment,
  `cloud_id` int(11) NOT NULL,
  `reference_text` varchar(500) default NULL,
  `user_id` int(11) NOT NULL,
  `moderate` tinyint(1) default '0',
  `timestamp` int(11) default NULL,
  PRIMARY KEY  (`reference_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloudscape`;

-- command split --

CREATE TABLE `cloudscape` (
  `cloudscape_id` int(10) NOT NULL auto_increment,
  `title` varchar(128) collate utf8_unicode_ci NOT NULL,
  `summary` tinytext collate utf8_unicode_ci,
  `body` longtext collate utf8_unicode_ci,
  `open` tinyint(4) default '1',
  `twitter_tag` varchar(140) collate utf8_unicode_ci default NULL,
  `user_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `featured` bit(1) default NULL,
  `image_path` varchar(140) collate utf8_unicode_ci default NULL,
  `moderate` tinyint(1) NOT NULL default '0',
  `image_attr_name` varchar(500) collate utf8_unicode_ci default NULL,
  `image_attr_link` varchar(140) collate utf8_unicode_ci default NULL,
  `start_date` int(10) default NULL,
  `end_date` int(10) default NULL,
  `location` varchar(140) collate utf8_unicode_ci default NULL,
  `omit_from_new_list` tinyint(1) NOT NULL default '0',
  `shortcut` varchar(30) collate utf8_unicode_ci default NULL,
  `display_show_all_sections` tinyint(1) NOT NULL default '1',
  `colour` varchar(10) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`cloudscape_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloudscape_admin`;

-- command split --

CREATE TABLE `cloudscape_admin` (
  `cloudscape_id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`cloudscape_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloudscape_attended`;

-- command split --

CREATE TABLE `cloudscape_attended` (
  `cloudscape_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `timestamp` int(10) default NULL,
  PRIMARY KEY  (`cloudscape_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloudscape_cloud`;

-- command split --

CREATE TABLE `cloudscape_cloud` (
  `cloud_id` int(10) default NULL,
  `cloudscape_id` int(10) default NULL,
  `user_id` int(10) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloudscape_email`;

-- command split --

CREATE TABLE `cloudscape_email` (
  `cloudscape_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `subject` varchar(100) collate utf8_unicode_ci NOT NULL,
  `body` text collate utf8_unicode_ci NOT NULL,
  `timestamp` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloudscape_followed`;

-- command split --

CREATE TABLE `cloudscape_followed` (
  `cloudscape_id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(10) default NULL,
  PRIMARY KEY  (`cloudscape_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloudscape_popular`;

-- command split --

CREATE TABLE `cloudscape_popular` (
  `cloudscape_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`cloudscape_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloudscape_poster`;

-- command split --

CREATE TABLE `cloudscape_poster` (
  `cloudscape_id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`cloudscape_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `cloudscape_section`;

-- command split --

CREATE TABLE `cloudscape_section` (
  `section_id` int(10) unsigned NOT NULL auto_increment,
  `cloudscape_id` int(10) unsigned NOT NULL,
  `title` varchar(40) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`section_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `comment`;

-- command split --

CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL auto_increment,
  `cloud_id` int(10) NOT NULL,
  `body` text collate utf8_unicode_ci,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) default NULL,
  `moderate` tinyint(1) NOT NULL default '0',
  `modified` int(11) default NULL,
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `event`;

-- command split --

CREATE TABLE `event` (
  `event_id` int(10) NOT NULL auto_increment,
  `event_type` varchar(30) collate utf8_unicode_ci NOT NULL,
  `follow_item_id` int(10) NOT NULL,
  `event_item_id` int(10) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `follow_item_type` varchar(50) collate utf8_unicode_ci NOT NULL,
  `omit_from_site_cloudstream` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`event_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `country`;

-- command split --

CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `iso` char(2) collate utf8_unicode_ci NOT NULL,
  `name` varchar(80) collate utf8_unicode_ci NOT NULL,
  `iso3` char(3) collate utf8_unicode_ci default NULL,
  `numcode` smallint(6) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `user`;

-- command split --

CREATE TABLE `user` (
  `id` int(11) NOT NULL auto_increment,
  `user_name` varchar(45) collate utf8_unicode_ci NOT NULL,
  `country_id` int(11) default NULL,
  `password` varchar(100) collate utf8_unicode_ci NOT NULL,
  `email` varchar(120) collate utf8_unicode_ci NOT NULL,
  `role` varchar(50) collate utf8_unicode_ci NOT NULL default 'user',
  `banned` tinyint(1) NOT NULL default '0',
  `forgotten_password_code` varchar(50) collate utf8_unicode_ci default NULL,
  `last_visit` datetime default NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `user_profile`;

-- command split --

CREATE TABLE `user_profile` (
  `id` int(11) NOT NULL,
  `fullname` varchar(140) collate utf8_unicode_ci NOT NULL,
  `institution` varchar(140) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  `twitter_username` varchar(140) collate utf8_unicode_ci default NULL,
  `homepage` varchar(140) collate utf8_unicode_ci default NULL,
  `department` varchar(140) collate utf8_unicode_ci default NULL,
  `rss` varchar(140) collate utf8_unicode_ci default NULL,
  `email_follow` int(1) NOT NULL default '1',
  `email_comment` int(1) NOT NULL default '1',
  `email_comment_followup` int(1) NOT NULL default '1',
  `email_news` int(1) NOT NULL default '1',
  `display_email` int(1) NOT NULL default '0',
  `whitelist` int(1) NOT NULL default '0',
  `email_events_attending` int(1) NOT NULL default '1',
  `do_not_use_editor` int(1) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `user_temp`;

-- command split --

CREATE TABLE `user_temp` (
  `id` int(11) NOT NULL auto_increment,
  `user_name` varchar(45) collate utf8_unicode_ci NOT NULL,
  `country_id` int(11) default NULL,
  `password` varchar(100) collate utf8_unicode_ci NOT NULL,
  `email` varchar(120) collate utf8_unicode_ci NOT NULL,
  `activation_code` varchar(50) collate utf8_unicode_ci NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `fullname` varchar(140) collate utf8_unicode_ci default NULL,
  `institution` varchar(140) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_FI_1` (`country_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `favourite`;

-- command split --

CREATE TABLE `favourite` (
  `user_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `item_type` varchar(30) collate utf8_unicode_ci NOT NULL,
  `timestamp` int(10) NOT NULL,
  PRIMARY KEY  (`user_id`,`item_id`,`item_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `featured_cloudscape`;

-- command split --

CREATE TABLE `featured_cloudscape` (
  `cloudscape_id` int(10) NOT NULL,
  `order` int(3) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `gadget`;

-- command split --

CREATE TABLE `gadget` (
  `gadget_id` int(11) unsigned NOT NULL auto_increment,
  `url` varchar(200) collate utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  `title` varchar(100) collate utf8_unicode_ci NOT NULL,
  `accessible_alternative` text collate utf8_unicode_ci,
  PRIMARY KEY  (`gadget_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `logs`;

-- command split --

CREATE TABLE `logs` (
  `item_id` int(10) default NULL,
  `item_type` varchar(50) collate utf8_unicode_ci default NULL,
  `timestamp` int(11) default NULL,
  `user_id` int(11) default NULL,
  `ip` varchar(30) collate utf8_unicode_ci default NULL,
  `search_term` varchar(200) collate utf8_unicode_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `blog_post`;

-- command split --

CREATE TABLE `blog_post` (
  `post_id` int(10) NOT NULL auto_increment,
  `title` varchar(140) collate utf8_unicode_ci default NULL,
  `body` longtext collate utf8_unicode_ci,
  `created` int(11) default NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`post_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `blog_comment`;

-- command split --

CREATE TABLE `blog_comment` (
  `comment_id` int(11) NOT NULL auto_increment,
  `post_id` int(11) NOT NULL,
  `body` text collate utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `moderate` tinyint(1) default '0',
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `page`;

-- command split --

CREATE TABLE `page` (
  `section` varchar(20) collate utf8_unicode_ci NOT NULL,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `body` text collate utf8_unicode_ci,
  `title` varchar(200) collate utf8_unicode_ci default NULL,
  `lang` varchar(10) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`section`,`name`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `section_cloud`;

-- command split --

CREATE TABLE `section_cloud` (
  `section_id` int(11) unsigned NOT NULL,
  `cloud_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`section_id`,`cloud_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `settings`;

-- command split --

CREATE TABLE `settings` (
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Settings and configuration variables';

-- command split --

DROP TABLE IF EXISTS `shortcut`;

-- command split --

CREATE TABLE `shortcut` (
  `shortcut` varchar(20) collate utf8_unicode_ci NOT NULL,
  `URl` varchar(2048) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`shortcut`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `site_news`;

-- command split --

CREATE TABLE `site_news` (
  `body` text collate utf8_unicode_ci,
  `user_id` int(11) unsigned NOT NULL,
  `timestamp` int(11) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `tag`;

-- command split --

CREATE TABLE `tag` (
  `item_id` int(10) NOT NULL,
  `tag` varchar(120) collate utf8_unicode_ci NOT NULL,
  `item_type` varchar(50) collate utf8_unicode_ci default NULL,
  `user_id` int(11) default NULL,
  `timestamp` int(11) default NULL,
  `tag_id` int(11) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`tag_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `user_followed`;

-- command split --

CREATE TABLE `user_followed` (
  `followed_user_id` int(11) NOT NULL,
  `following_user_id` int(11) NOT NULL,
  `timestamp` int(10) default NULL,
  PRIMARY KEY  (`followed_user_id`,`following_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `user_gadget`;

-- command split --

CREATE TABLE `user_gadget` (
  `user_id` int(11) NOT NULL,
  `gadget_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`gadget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- command split --

DROP TABLE IF EXISTS `user_picture`;

-- command split --

CREATE TABLE `user_picture` (
  `user_id` int(10) NOT NULL,
  `picture` varchar(140) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
