<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/
define('EXT_HOOKS', '../../extensions/hooks/');


    $hook['__display_override'] = array(
      'class'    => 'oembed',
      'function' => 'filter',
      'filename' => 'oembed_hook.php',
      'filepath' => 'hooks',
      'params'   => ($mode = 'link')  #OR 'braces'.
    );

    $hook['display_override'][] = array(
      'class'   => 'Project_hook',
      'function'=> 'display',
      'filename'=> 'project_hook.php',
      'filepath'=> EXT_HOOKS,
      'params'  => array('final'=>false) //NULL,
    );

    $hook['display_override'][] = array(
      'class'   => 'Wiki_hook',
      'function'=> 'display',
      'filename'=> 'wiki_hook.php',
      'filepath'=> EXT_HOOKS,
      'params'  => array('final'=>true),
    );

/* End of file hooks.php */
/* Location: ./system/application/config/hooks.php */