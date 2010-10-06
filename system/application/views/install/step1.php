
<h2>Pre-install checks</h2>

<?php if (isset($messages)): ?>
<ol class="messages" id="messages">
<?php foreach ($messages as $type => $message): ?>
    <li class="<?php echo $type ?>"><?php echo $message; ?></li>
<?php endforeach; ?>
</ol>
<?php endif; ?>


<h2>Administrator account</h2>

<p>Please create your administrator account.</p>

<?php echo validation_errors(); ?>

<form method="post" action="">
<input name="nextstep" value="2" type="hidden" />

<ul>
  <li>
  <label for="email">Email address (caution, please use a real one!)</label>
  <input id="email" name="email" value="<?php echo set_value('email'); ?>" size="100" />

  <li>
  <label for="user_name">Username (example 'admin')</label>
  <input id="user_name" name="user_name" value="<?php echo set_value('user_name'); ?>" size="45" autocomplete="off" />

  <li>
  <label for="password">Password</label>
  <input id="password" name="password" type="password" value="<?php echo set_value('password'); ?>" size="50" autocomplete="off" />

  <li>
  <label for="password_2">Confirm password</label>
  <input id="password_2" name="confirm_password" type="password" value="<?php echo set_value('confirm_password'); ?>" size="50" />
</ul>

  <p><br />
  <button>Install</button>

</form>

