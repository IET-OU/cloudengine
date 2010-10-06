
<h1> Welcome to the <?php echo $site_name ?></h1>

<p> Please press the button to run pre-installation tests. </p>

<form method="post" action="">
<p>
  <input name="nextstep" value="1" type="hidden" />

<?php foreach ($config as $name => $value): ?>
  <input name="<?php echo $name ?>" value="<?php echo $value ?>" type="hidden" />
<?php endforeach; ?>

  <button>Run pre-install checks</button>
</p>

</form>
