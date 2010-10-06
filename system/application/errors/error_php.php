<?php @header("HTTP/1.1 500 Internal Server Error", TRUE, 500);

// This page is only used for API errors and if debug is turned on in the config file. 
if (function_exists('get_instance')): 
	$CI =& get_instance();
	if ('api'==$CI->uri->segment(1) && config_item('x_api')):
			$CI->load->library('Api_error_lib');
			$CI->api_error_lib->process_error_404();    
?>
<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>A PHP Error was encountered</h4>

<p>Severity: <?php echo $severity; ?></p>
<p>Message:  <?php echo $message; ?></p>
<p>Filename: <?php echo $filepath; ?></p>
<p>Line Number: <?php echo $line; ?></p>

</div>

<?php endif; ?>
<?php endif; ?>