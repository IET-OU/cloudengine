<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Image_lib extends CI_Image_lib {

    function MY_Image_lib() {
        parent::CI_Image_lib();
    }

    /**
     * Crop an image to a square
     *
     * @param string $source_image The path of the source image
     * @param integer $original_height The original height
     * @param integer $original_width The original width
     * @return boolean TRUE if successful, FALSE otherwise
     */
    function crop_to_square($source_image, $original_height, $original_width) {    
        $config['image_library'] = 'gd2';
        $config['source_image'] = $source_image;


        if ($original_width > $original_height) { // Crop left & right
            $config['width'] = $original_height;
            $config['height'] = $original_height;
            $config['x_axis'] = ($original_width - $config['width']) / 2;
        } else { // crop top & bottom
            $config['width'] = $original_width;
            $config['height'] = $original_width;
            $config['y_axis'] = ($original_height - $config['height']) / 2;
        }

        $config['maintain_ratio'] = false;
        $this->initialize($config);
        $result = $this->crop();

        $this->clear();
        return $result;
    }
    
    /**
     * Resize an image to fit the 200x300 panel on the home page
     *
     * @param string $source_image The path of the source image
     * @param integer $original_height The original height
     * @param integer $original_width The original width
     * @return boolean TRUE if successful, FALSE otherwise
     */
    function resize_to_fit_panel($source_image, $original_height, $original_width) {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $source_image;
        $new_dimensions   = $this->calculate_new_dimensions($original_height, 
                               $original_width, 200, 300);
        $config['maintain_ratio'] = TRUE;
        $config['height']   = $new_dimensions->height;
        $config['width']   = $new_dimensions->width;
        $config['image_library'] = 'gd2';
        $this->initialize($config);
        $result = $this->resize();
        $this->clear();
        return $result;  
    }
    
    /**
     * Given the original dimensions of an image and the maximum dimensions, calculate the new
     * dimensions that the image needs to be resized to in order to maintain the ratio of the image
     * and also fit in the maximum dimensions
     *
     * @param integer $original_height The original height
     * @param integer $original_width The original width
     * @param integer $maximum_height The maximum height
     * @param integer $maximum_width The maximum width
     * @return object The new dimensions
     */
    function calculate_new_dimensions($original_height, $original_width, $maximum_height, $maximum_width) {
        $new_height = $original_height;
        $new_width = $original_width;
        if ($original_height > $maximum_height) {
            $new_height = $maximum_height;
            $new_width  = $original_width * $maximum_height / $original_height;
        }
        
        if ($new_width > $maximum_width) {
            $new_height = $new_height * $maximum_width / $new_width;
            $new_width = $maximum_width;
        }     
        
        $new_dimensions->width = $new_width;
        $new_dimensions->height = $new_height;
        return $new_dimensions;   
    }
       
    /** Crop with an aspect ratio of 4:3.
     * @param string $upload_path
     * @param string $file_name
     * @param int    $width  Desired width in pixels.
     * @param string $prefix Prefix for the new file name.
     * @return boolean TRUE if successful, FALSE otherwise
     */
    function crop_landscape($upload_path, $file_name, $width, $prefix) {

        $config['image_library'] = 'gd2';
        $config['source_image'] = $upload_path.$file_name;

        $config['maintain_ratio'] = FALSE;
        $config['width'] =  $width;
        $config['height'] =  ceil(0.75*$width);
        $config['new_image'] = $prefix.$file_name;
        $this->initialize($config);
        $result = $this->crop();
        $this->clear();
        return $result;
    }    

    /**
     * Create an icon for the site
     *
     * @param string $upload_path The path where the image file has been uploaed
     * @param string $file_name The name of the image file
     * @param integer $width The width of the image file
     * @param string $prefix The prefix to give the newly created image file
     * @return boolean TRUE if successful, FALSE otherwise
     */
    function create_icon($upload_path, $file_name, $width, $prefix) {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $upload_path.$file_name;
        $config['maintain_ratio'] = TRUE;
        $config['width'] =  $width;
        $config['height'] =  $width;
        $config['new_image'] = $prefix.$file_name;
        $this->initialize($config);
        $result = $this->resize();
        $this->clear();
        return $result;
    }

    /** A helper to check if the image is landscape.
     * @param int $width
     * @param int $height
     * @return boolean TRUE if the image is landscape, FALSE otherwise
     */
    function is_landscape($width, $height) {
        return ($width > $height);
    }
}