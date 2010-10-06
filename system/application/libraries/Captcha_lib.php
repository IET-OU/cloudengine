<?php

/* -----------------------------------------------------------------------------
 * Copyright (C) 2007  Daniel Vecchiato (4webby.com)
 * -----------------------------------------------------------------------------
 *This library is free software; you can redistribute it and/or
 *modify it under the terms of the GNU Lesser General Public
 *License as published by the Free Software Foundation; either
 *version 2.1 of the License, or (at your option) any later version.
 *
 *This library is distributed in the hope that it will be useful,
 *but WITHOUT ANY WARRANTY; without even the implied warranty of
 *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *Lesser General Public License for more details.
 *
 *You should have received a copy of the GNU Lesser General Public
 *License along with this library; if not, write to the Free Software
 *Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *------------------------------------------------------------------------------
 * @package     FreakAuth_light
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Daniel Vecchiato (danfreak) & Christophe Gragnic (grahack)
 * @copyright   Copyright (c) 2007, 4webby.com
 * @license		http://www.gnu.org/licenses/lgpl.html
 * @link 		http://4webby.com/freakauth
 * @version 	1.1
 */


class Captcha_lib {
	
    function Captcha_lib() {
        $this->CI =& get_instance();
    }
    
    /**
     * Checks if Captcha is required
     * if it is required in the config settings recalls function _generateCaptcha()
     * to build it
     */
    function captcha_init($action)
    {	
    
        //ELSE unsets userdata from session table
        $this->CI->db_session->unset_userdata('FreakAuth_captcha');
        
        //loads the captcha plugin
        //$this->CI->load->plugin('captcha');
        list($usec, $sec) = explode(" ", microtime());
        $now = ((float)$usec + (float)$sec);
        
        //deletes captcha images
        $this->_deleteOldCaptcha($now);
        
        //generates security code image
        $this->_generateCaptcha($now);
    }

    
    /**
     * Deletes the captcha images generated
     * it deletes them if they "expired". The "expiration" (in seconds)
     * signifies how long an image will remain in the root/tmp folder before it
     * will be deleted.  The default is 10 minutes. Change the value
     * of $expiration if you want them to be deleted more or less often
     *
     * @param float $now
     * @todo move expiration time in a config variable
     */
    function _deleteOldCaptcha($now)
    {
    	list($usec, $sec) = explode(" ", microtime());
		
    	// sets the expiration time of the captcha image
    	$expiration=60*10; //10 min
			
		$current_dir = @opendir($this->CI->config->item('FAL_captcha_image_path'));
		
		while($filename = @readdir($current_dir))
		{
			if ($filename != "." AND $filename != ".." AND $filename != "index.html")
			{
				$name = str_replace(".jpg", "", $filename);
			
				if (($name + $expiration) < $now)
				{
					@unlink($this->CI->config->item('FAL_captcha_image_path').$filename);
				}
			}
		}
		
		@closedir($current_dir);
    }
    
    
    
    /**
     * Creates a random security code image (Captcha).
     *
     * @return unknown
     */
    function _generateCaptcha($now)
    {
        
            $securityCode = $this->_generateRandomString($this->CI->config->item('FAL_captcha_min'), $this->CI->config->item('FAL_captcha_max'));
			//$image = 'security-'.$this->_generateRandomString(16, 32).'.jpg';
			$image = $now.'.jpg';
            $this->CI->config->set_item('FAL_captcha_image', $image);
            
            $config['image_library'] = $this->CI->config->item('FAL_captcha_image_library');
            $config['source_image'] = $this->CI->config->item('FAL_captcha_base_image_path').$this->CI->config->item('FAL_captcha_image_base_image');
            $config['new_image'] = $this->CI->config->item('FAL_captcha_image_path').$image;
            $config['wm_text'] = $securityCode;
            $config['wm_type'] = 'text';
            $config['wm_font_path'] = $this->CI->config->item('FAL_captcha_image_font');
            $config['wm_font_size'] = $this->CI->config->item('FAL_captcha_image_font_size');
            $config['wm_font_color'] = $this->CI->config->item('FAL_captcha_image_font_color');
            $config['wm_vrt_alignment'] = 'top';
			$config['wm_hor_alignment'] = 'left';
			$config['wm_padding'] = '10';

            $image =& get_instance();
            $image->load->library('image_lib');
            $image->image_lib->initialize($config); 
            if ( ! $image->image_lib->watermark())
			{
			    echo $image->image_lib->display_errors();
			};
            $this->CI->db_session->set_userdata('FreakAuth_captcha', $securityCode);			
            return $this->CI->config->item('FAL_captcha_image');
            
        
    }
	   
    /**
     * Generates a random string.
     *
     * @param integer $minLength
     * @param integer $maxLength
     * @param boolean $useUpper
     * @param boolean $useNumbers
     * @param boolean $useSpecial
     * @return $key random string
     */
    function _generateRandomString()
    {
        $charset = "abcdefghijklmnopqrstuvwxyz";
        if ($this->CI->config->item('FAL_captcha_upper_lower_case'))
            $charset .= "ABCDEFGHIJKLMNPQRSTUVWXYZ";
        if ($this->CI->config->item('FAL_captcha_use_numbers'))
            $charset .= "23456789";
		if ($this->CI->config->item('FAL_captcha_use_specials'))
            $charset .= "~@#$%^*()_+-={}|][";
            
        $length = mt_rand($this->CI->config->item('FAL_captcha_min'), $this->CI->config->item('FAL_captcha_max'));
        if ($this->CI->config->item('FAL_captcha_min') > $this->CI->config->item('FAL_captcha_max'))
            $length = mt_rand($this->CI->config->item('FAL_captcha_max'), $this->CI->config->item('FAL_captcha_min'));

        $key = '';
        for ($i = 0; $i < $length; $i++)
            $key .= $charset[(mt_rand(0, (strlen($charset)-1)))];

        return $key;
    }
}