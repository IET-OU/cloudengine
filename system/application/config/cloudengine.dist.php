<?php 

/*
|--------------------------------------------------------------------------
| Site details
|--------------------------------------------------------------------------
*/

// Basic site info

$config['site_name']        = 'Your Site';
$config['site_email']       = '';
$config['tag_line']         = '';
$config['default_language'] = 'en';

// Set the SMTP host so that the site can send out registration related and notification 
// e-mails
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
$config['theme_favicon']    = 'themes/aurora/favicon-aurora.ico.gif';


// Automatically flag whether this is a LIVE or developer/test install.
$config['x_live'] = TRUE;
$config['test_install_message'] = 'This is a test install.'; // Message to display for test install


// This overrides the settings in the index.php file (assuming CodeIgniter gets as far
// as reading this file)
$debug = FALSE;
if ($debug) {
	ini_set("display_errors", 'On');
	error_reporting(E_ALL & ~E_NOTICE);
} else {
	ini_set("display_errors", 'Off');
	error_reporting(0);
}

// Set a proxy here if you want to use features that use external services and need to set a proxy 
// setting 
$config['proxy'] = '';


/**
 * Use secure password hashing. For new sites, this should be set to TRUE. Do not change to 
 * FALSE (otherwise nobody will be able to login any longer!). The option is available for 
 * reasons of backwards-compatibility. 
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

// Feature flag for internationalisation
$config['x_translate'] =FALSE;

// Feature flag for search 
$config['x_search'] = FALSE;

// Feature flag for event e-mails from admins for events
$config['x_email_events_attending'] = FALSE;
$config['email_event_attending_limit_per_hour'] = 2; // Maximum number of e-mails an admin may send to 
// attendees of a particular event per hour

// Enable and configure the API 
$config['x_api']                = FALSE;
$config['x_api_suggest']        = FALSE;
$config['x_api_debug']          = TRUE;
$config['x_api_key_required']   = TRUE;
$config['x_api_max_results']    = 200;
$config['x_api_stream_default'] = 15;
$config['x_api_formats']        = "json|js"; // Delimiter "|".

// Configure the number  of days used to calculate active/popular items on the site
$config['active_clouds_days']       = 10;
$config['popular_clouds_days']      = 100;
$config['popular_cloudscapes_days'] = 100;

// Configure google gadgets
$config['x_gadgets'] = FALSE;
$config['x_gadgets_gfc_key'] = ''; // Google Friend Connect key to use for gadgets -
// this key needs to be obtained for the site from Google Friend Connect and entered here for Google
// Gadgets to work on the site. It will be a string of approx 20 digits.

// Config variable containing IDs of users regarded as 'team' for stats purposes. 
$config['team'] = '';

// Feature flag for spam-checking of new items added to the site using Mollom
// If you want to use this you need to copy mollom.dist.php in the config directory to 
// mollom.php and put in your Mollom settings there. 
$config['x_moderation'] = FALSE;

// Feature flag and config for twitter hash tag and displaying tweets for a cloudscape
$config['x_twitter']          = FALSE;
$config['x_twitter_username'] = '';
$config['x_twitter_password'] = '';

// Feature flag for captchas on registration form 
$config['x_captcha'] = FALSE;
$config['whitelist_domains'] = '.ac.uk:.edu:.ac.jp:.ac.ae:.ac.nz:.edu.au:.ac.za:.ac.be';

$config['expire_temp_users_time'] = 3600*168;

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

// Folder of the Base image needed to generate Captcha
$config['FAL_captcha_base_image_path'] = $config['base_url'].'_design/';

// Base image name for captcha
$config['FAL_captcha_image_base_image'] = 'captcha_base_image.jpg';

// captcha font location
$config['FAL_captcha_image_font'] = BASEPATH.'fonts/Jester.ttf';

// Folder to save the captcha background image (relative BASEPATH)
// this folder must be writable by php
$config['FAL_captcha_image_path'] = $config['data_dir'].'tmp/';

//name of the generate image (leave it blank!!!!!!)
$config['FAL_captcha_image'] = '';