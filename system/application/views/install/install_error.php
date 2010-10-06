
<h1> Installer error </h1>

<div class="error" style="padding-left:1em;">
<?php echo $message ?>

</div>


<form method="post" action="">
<br /><p>
  <input name="nextstep" value="1" type="hidden" />

<?php foreach ($config as $name => $value): ?>
  <input name="<?php echo $name ?>" value="<?php echo $value ?>" type="hidden" />
<?php endforeach; ?>


  <button>Run pre-install checks again</button>
</p>

</form>


