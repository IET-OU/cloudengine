
INSERT INTO `cloud`
(`cloud_id`,`title`,`body`,`summary`,`url`,`user_id`,`created`,`modified`,`contact`,`moderate`,`omit_from_new_list`,`primary_url`,`call_deadline`)
    VALUES  (1,'My first cloud - Example',
'<h3>About Clouds</h3>\n<p>A Cloud can be anything related to your site and interests. Each Cloud is \'social\' in that it is possible to have a conversation around the Cloud. A Cloud could be: a short description of a learning and teaching idea, information about resources or tools for learning and teaching, detailed learning designs or case studies of practice or a question as a starting point for a discussion.</p>','This is an example cloud - you may want to delete it',
    NULL, __USER_ID__, __TIME__, 0, NULL,0,0,'',NULL);

-- command split --

INSERT INTO `cloud_content`(`content_id`,`user_id`,`cloud_id`,`body`,`created`,`modified`,`moderate`)
    VALUES (1, __USER_ID__, 1,
'<p>This is some example \"extra content\" - you may want to delete it.</p>\n<p>Another person can colloborate by contributing this content to \"your\" cloud.</p>', __TIME__, NULL,0);

-- command split --

INSERT INTO `cloud_link`(`cloud_id`,`url`,`user_id`,`timestamp`,`link_id`,`title`,`moderate`,`type`)
    VALUES (1,'http://cloudworks.ac.uk/', __USER_ID__, __TIME__, 1,'Cloudworks - example link',0,'external');

-- command split --

INSERT INTO `cloudscape`
(`cloudscape_id`,`title`,`summary`,`body`,`open`,`twitter_tag`,`user_id`,`created`,`modified`,`featured`,`image_path`,`moderate`,`image_attr_name`,`image_attr_link`,`start_date`,`end_date`,`location`)
    VALUES (1, 'Welcome to CloudEngine - Example', 'This is an example Cloudscape - you may want to delete it',
'<h3>About Cloudscapes</h3>\n<p>Clouds can be aggregated into \'Cloudscapes\' associated with a particular event, purpose or interest. For example you can have Cloudscapes associated with a conference aggregating Clouds about conference presentations or tools and resources referenced. A Cloudscape can be set up for a workshop where Clouds might include workshop resources, tools or activities. Cloudscapes can also be more general for example to stimulate debate about a particular teaching approach. Clouds can be associated with more than one Cloudscape.</p>',
    1,'', __USER_ID__, __TIME__, 0, NULL,'__EXAMPLE_IMAGE__',0,'nick_russill','http://flickr.com/photos/nickrussill/146743083/', NULL,NULL,'');

-- command split --

INSERT INTO `cloudscape`
(`cloudscape_id`,`title`,`summary`,`body`,`open`,`twitter_tag`,`user_id`,`created`,`modified`,`featured`,`image_path`,`moderate`,`image_attr_name`,`image_attr_link`,`start_date`,`end_date`,`location`)
    VALUES (2, 'A workshop on open source software - Example', 'This is an example event-cloudscape - you may want to delete it',
'<p>This is an imaginary workshop we are organizing about how to open source your project.</p>',
    1,'', __USER_ID__, __TIME__, 0, NULL,NULL,0,NULL,NULL, __EVENT_TIME__, NULL, 'Walton Hall, Milton Keynes, UK');

-- command split --

INSERT INTO `cloudscape_attended`(`cloudscape_id`,`user_id`,`timestamp`) VALUES (2, __USER_ID__, __TIME__);

-- command split --

INSERT INTO `cloudscape_cloud`(`cloud_id`,`cloudscape_id`,`user_id`) VALUES (1,1, __USER_ID__);

-- command split --

INSERT INTO `cloudscape_followed`(`cloudscape_id`,`user_id`,`timestamp`) VALUES (1, __USER_ID__, __TIME__);

-- command split --

INSERT INTO `cloudscape_followed`(`cloudscape_id`,`user_id`,`timestamp`) VALUES (2, __USER_ID__, __TIME__);

-- command split --

INSERT INTO `comment` (`comment_id`,`cloud_id`,`body`,`user_id`,`timestamp`,`moderate`,`modified`)
    VALUES (1,1,'<p>This is an example comment - you may want to delete it.</p>',__USER_ID__, __TIME__, 0, NULL);

-- command split --

INSERT INTO `event`(`event_type`,`follow_item_id`,`event_item_id`,`timestamp`,`follow_item_type`,`omit_from_site_cloudstream`)
    VALUES ('cloud', __USER_ID__, 1, __TIME__, 'user',0);

-- command split --

INSERT INTO `event`(`event_type`,`follow_item_id`,`event_item_id`,`timestamp`,`follow_item_type`,`omit_from_site_cloudstream`)
    VALUES ('cloudscape', __USER_ID__, 1, __TIME__, 'user',0);

-- command split --

INSERT INTO `event`(`event_type`,`follow_item_id`,`event_item_id`,`timestamp`,`follow_item_type`,`omit_from_site_cloudstream`)
    VALUES ('cloud',1, 1, __TIME__, 'cloudscape',0);

-- command split --

INSERT INTO `event`(`event_type`,`follow_item_id`,`event_item_id`,`timestamp`,`follow_item_type`,`omit_from_site_cloudstream`)
    VALUES ('comment', __USER_ID__, 1, __TIME__, 'user',0);

-- command split --

INSERT INTO `event`(`event_type`,`follow_item_id`,`event_item_id`,`timestamp`,`follow_item_type`,`omit_from_site_cloudstream`) 
    VALUES ('comment',1, 1, __TIME__, 'cloud',0);

-- command split --

INSERT INTO `event`(`event_type`,`follow_item_id`,`event_item_id`,`timestamp`,`follow_item_type`,`omit_from_site_cloudstream`)
    VALUES ('cloudscape', __USER_ID__, 2, __TIME__, 'user',0);

-- command split --

INSERT INTO `event`(`event_type`,`follow_item_id`,`event_item_id`,`timestamp`,`follow_item_type`,`omit_from_site_cloudstream`)
    VALUES ('link',1, 1, __TIME__, 'cloud',0);

-- command split --

INSERT INTO `event`(`event_type`,`follow_item_id`,`event_item_id`,`timestamp`,`follow_item_type`,`omit_from_site_cloudstream`)
    VALUES ('link', __USER_ID__, 1, __TIME__, 'user',0);

-- command split --

INSERT INTO `event`(`event_type`,`follow_item_id`,`event_item_id`,`timestamp`,`follow_item_type`,`omit_from_site_cloudstream`)
    VALUES ('content',1, 1, __TIME__, 'cloud',0);

-- command split --

INSERT INTO `event`(`event_type`,`follow_item_id`,`event_item_id`,`timestamp`,`follow_item_type`,`omit_from_site_cloudstream`)
    VALUES ('content', __USER_ID__, 1, __TIME__, 'user',0);

-- command split --

INSERT INTO `featured_cloudscape`(`cloudscape_id`,`order`) VALUES (1,1);

-- command split --

INSERT INTO `featured_cloudscape`(`cloudscape_id`,`order`) VALUES (2,2);

-- command split --

INSERT INTO `tag`(`item_id`,`tag`,`item_type`,`user_id`,`timestamp`,`tag_id`) VALUES (1,'example','cloudscape', __USER_ID__, __TIME__, 1);

-- command split --

INSERT INTO `tag`(`item_id`,`tag`,`item_type`,`user_id`,`timestamp`,`tag_id`) VALUES (1,'example','cloud', __USER_ID__, __TIME__, 2);

-- command split --

INSERT INTO `tag`(`item_id`,`tag`,`item_type`,`user_id`,`timestamp`,`tag_id`) VALUES (2,'example','cloudscape', __USER_ID__, __TIME__, 3);

-- command split --

INSERT INTO `tag`(`item_id`,`tag`,`item_type`,`user_id`,`timestamp`,`tag_id`) VALUES (2,'event','cloudscape', __USER_ID__, __TIME__, 4);

-- command split --

INSERT INTO `settings` VALUES ('site_live', '1', 'Site status', 'When \'Online\' all users can browse the site normally. When \'Offline\' only users with the administrator rights can access the site to perform maintenance, all other visitors will see the site \'offline public message\' below. Authorized users can log in during \'Offline\' mode via the <a href=\"../auth/login\">user login page</a>.', 'site_status', 'boolean', '');

-- command split --

INSERT INTO `settings` VALUES ('offline_message_public', '!site-name! is undergoing maintenance and will be back online soon. Please accept our apologies. \n\nTo contact !site-name! please email: !site-email!\n\n(Last update to message: 10:30am on 16/11/2010)', 'Site offline public message', 'Message to display to public on site holding page whilst offline', 'site_status', 'text', '');

-- command split --

INSERT INTO `settings` VALUES ('offline_message_admin', '!site-name! is offline', 'Site offline admin message', 'Message to display in banner
 to admin whilst site is offline', 'site_status', 'text', '');

-- command split --

INSERT INTO `settings` VALUES ('debug', '1', 'Show debug output', 'Show PHP errors and Firephp output', 'debug', 'select_list', '0 debug is off, 1 debug for admin users, 2 debug for all users (emergency use only)');
