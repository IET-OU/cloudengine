<?php 
/**
 * This page should mostly not be reached - should be handled by show_error
 * in MY_Exceptions.php. It is reached for missing config. file errors. 
 */
?>
<!DOCTYPE html><html lang="en"><meta charset=utf-8 ><title>500 Unknown Server Error</title>
<?php if ($message):
    echo $message;
else: ?>
  <h1>An error has occurred</h1>
  <a href="<?php echo config_item('base_url') ?>">Home</a>.
<?php endif; ?>
</html>