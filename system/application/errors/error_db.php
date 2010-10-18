<?php @header("HTTP/1.1 503 Service Unavailable", TRUE, 503);
/**
 * This page should never be reached - should be handled by show_error in MY_Exceptions.php
 */
?>
<!DOCTYPE html><html lang="en"><meta charset=utf-8 ><title>503 Service Unavailable</title>
<h1>A database error occurred</h1> <?php echo $message ?> -
<a href="<?php echo config_item('base_url') ?>">Home</a>. </html>
