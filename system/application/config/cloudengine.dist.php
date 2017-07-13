<?php

/*
|--------------------------------------------------------------------------
| Site details
|--------------------------------------------------------------------------
*/

// Basic site info

$config['site_name']        = 'Your Site';
$config['site_email']       = '';
// 'tag_line' should NOT use the t() function. It may use !site-name! etc. (i18n).
$config['tag_line']         = '';
$config['default_language'] = 'en';

// Set the SMTP host so that the site can send out registration related
// and notification e-mails.
$config['smtp_host'] = '';

// Data directory paths
// (Please check/set $config['log_path'] in config.php)

$config['data_dir'] = '';

$config['upload_path']             = $config['data_dir'].'uploads/';
$config['upload_path_user']        = $config['upload_path'].'user/';
$config['upload_path_cloudscape']  = $config['upload_path'].'cloudscape/';
$config['search_index_path']       = $config['data_dir'].'search/index';


// Locations of files to customise the theme
$config['theme_stylesheet'] = 'themes/aurora/styles.css';
$config['theme_logo']       = 'themes/aurora/cloudengine-logo.gif';
$config['theme_banner']     = 'themes/aurora/header-bg-aurora.jpg';
$config['theme_favicon']    = 'themes/aurora/favicon-aurora.ico';

// Flag whether this is a LIVE or developer/test install.
$config['x_live'] = TRUE;
$config['test_install_message'] = 'This is a test install.'; // Message to display for test install

// Set the path to Mercurial if this CloudEngine is hg-clone'd, to show
// revision data on the admin/phpinfo page.
$config['hg_path'] = '';

// Set a proxy here if you want to use features that use external services
// and your network requires one.
$config['proxy'] = '';
$config['proxy_port'] = '';

// A list of devices for which the rich editor (Tiny MCE) should be disabled
// (pipe | separated).  EXPERIMENTAL.
$config['device_no_richedit'] = 'iPhone|iPod|iPad|Android|IEMobile|Opera Mini';

/**
 * Use secure password hashing. For new sites, this should be set to TRUE. Do
 * not change to FALSE (otherwise nobody will be able to login any longer!).
 * The option is available for reasons of backwards-compatibility.
 */
$config['use_password_hash'] = TRUE;

/*
|--------------------------------------------------------------------------
| Feature flags and feature-specific settings
|--------------------------------------------------------------------------
*/

// Google Analystics
$config['x_google_analytics'] = FALSE;
$config['google_analytics'] = ''; // Set this to your google analytics code


// CaPRéT/ Track OER. EXPERIMENTAL.
$config['x_capret'] = FALSE;
$config['capret_variant'] = 'piwik';  // Or 'ga', or 'classic'
$config['capret_analytics_id'] = 6;   // Or 'UA-12345-6'
$config['capret_about_url'] = 'support/capret'; // Relative/absolute URL fed to CodeIgniter 'anchor()' function.

// Feature flag for internationalisation.
$config['x_translate'] =FALSE;

// Feature flag for search.
$config['x_search'] = FALSE;
$config['x_google_site_search'] = FALSE;
$config['x_google_site_search_cx'] = '';

// Feature flag for event e-mails from admins for events.
$config['x_email_events_attending'] = FALSE;
$config['email_event_attending_limit_per_hour'] = 2; // Maximum number of e-mails
// an admin may send to attendees of a particular event per hour.

// Enable and configure the API
$config['x_api']                = FALSE;
$config['x_api_suggest']        = FALSE;
$config['x_api_debug']          = TRUE;
$config['x_api_key_required']   = TRUE;
$config['x_api_max_results']    = 200;
$config['x_api_stream_default'] = 15;
$config['x_api_formats']        = "json|js|xml"; // Delimiter "|".

// Configure the number  of days used to calculate active/popular items on the site
$config['active_clouds_days']       = 10;
$config['popular_clouds_days']      = 100;
$config['popular_cloudscapes_days'] = 100;

// Configure google gadgets
$config['x_gadgets'] = FALSE;
$config['x_gadgets_gfc_key'] = ''; // Google Friend Connect key to use for gadgets -
// this key needs to be obtained for the site from Google Friend Connect and entered
// here for Google Gadgets to work on the site. Expect a string of approx 20 digits.

// Config variable containing IDs of users regarded as 'team' for stats purposes.
$config['team'] = '';

// User IDs for people whose description should be shown. (Eg. deceased users)
$config['users_show_description'] = [ /* 1230, 6789 */ ];


// Feature flag and config for twitter hash tag and displaying tweets for a cloudscape.
$config['x_twitter']          = FALSE;
$config['x_twitter_username'] = '';
$config['x_twitter_password'] = '';

// Feature flag for direct messaging (Beta default: FALSE).
$config['x_message'] = FALSE;

// Feature flag for captchas on registration form (colon : separated).
$config['x_captcha'] = FALSE;
$config['whitelist_domains'] = '.ac.uk:.edu:.ac.jp:.ac.ae:.ac.nz:.edu.au:.ac.za:.ac.be';

$config['expire_temp_users_time'] = 3600*168;

// Maximum number of login attempts in the last ten minutes for a single user
$config['max_login_attempts'] = 10;


// An array of jQuery-oEmbed options.
// @link http://code.google.com/p/jquery-oembed/
$config['oembed_options'] = array(

  // OU Podcasts/ OU Media Player.
  'oupodcast' => array(
    //('theme' => 'ouice-dark'), # 2011 legacy
    'theme' => 'oup-light',
  ),

  // @link http://iSpot.org.uk
  'ispot' => array(
    'count' => 3,
    'postmessage' => 1,
  ),

  // Global configuration.
  'maxWidth' => 800,
);


/*-------------------+
|  CAPTCHA SETTINGS  |
+-------------------*/

/*
|-------------------------------------------------------------------------------
| Should the visitor input check be case sensitive or not
|-------------------------------------------------------------------------------
*/
$config['FAL_captcha_case_sensitive'] = TRUE;

/*
|-------------------------------------------------------------------------------
| What to use in the CAPTCHA string
|-------------------------------------------------------------------------------
*/
// upper and lower case (lower case by default)
$config['FAL_captcha_upper_lower_case'] = FALSE;
$config['FAL_captcha_use_numbers'] = FALSE;

// use special characters (if true, use a font that support it)
$config['FAL_captcha_use_specials'] = FALSE;

$config['FAL_captcha_min'] = 5;      //min captcha length
$config['FAL_captcha_max'] = 5;      //max captcha length

/*
|-------------------------------------------------------------------------------
| How to display the CAPTCHA string
|-------------------------------------------------------------------------------
*/
$config['FAL_captcha_image_font_size'] = 20;
$config['FAL_captcha_image_font_color'] = '33CC33';

/*
|-------------------------------------------------------------------------------
| What to use for generation of the CAPTCHA
|-------------------------------------------------------------------------------
*/
$config['FAL_captcha_image_library'] = 'GD2';

// System path/folder of the Base image needed to generate the Captcha.
$config['FAL_captcha_base_image_path'] = BASEPATH.'../_design/';

// Base image name for captcha
$config['FAL_captcha_image_base_image'] = 'captcha_base_image.jpg';

// captcha font location
$config['FAL_captcha_image_font'] = BASEPATH.'fonts/Jester.ttf';

// Folder to save the captcha background image (relative BASEPATH)
// this folder must be writable by php
$config['FAL_captcha_image_path'] = $config['data_dir'].'tmp/';

//name of the generated image (leave it blank!!!!!!)
$config['FAL_captcha_image'] = '';

// Feature flag and config for badges
$config['x_badge']          = TRUE;
$config['upload_path_badge']       = $config['upload_path'].'badge/';
$config['badge_salt']          = 'cloudengine';
$config['badge_issuer_name']  = 'Cloudworks';
$config['badge_issuer_org'] = '';
$config['badge_issuer_contact'] = '';

// Feature flag for flagging items as spam
$config['x_flag'] = TRUE;

// Feature flag for spam-checking of new items added to the site using the moderation provider specified by the 'moderation_provider' config. This can currently be 'akismet'
// or 'none'
$config['x_moderation'] = FALSE;
$config['x_moderation'] = TRUE;
$config['moderation_provider'] = 'none';
$config['akismet_key'] = '';
$config['akismet_url'] = '';
