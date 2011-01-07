
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

<form method="post" action="" class="h5">
<input name="nextstep" value="2" type="hidden" />

<ul>
  <li>
  <label for="email">Email address (caution, please use a real one!)</label> 
  <input id="email" name="email" value="<?php echo set_value('email'); ?>" size="60" type="email"
    placeholder="admin@example.org" title="Invalid email address." required /><!-- x-moz-errormessage. -->

  <li>
  <label for="user_name">Username (example 'admin')</label>
  <input id="user_name" name="user_name" value="<?php echo set_value('user_name'); ?>" size="60" autocomplete="off"
    placeholder="admin" pattern="[a-zA-Z]\w{3,44}\s?" title="Minimum 4 characters, letters and numbers (no spaces)." required />

  <li>
  <label for="password">Password</label>
  <?php /*Password: accept a 'word' character, followed by 5+ non-space, non-control characters \w[^\s\c]  (Was: [\w\$\^&quot;'!\(\)\[\]\|\-_+@%*&amp;] ) */ ?>
  <input id="password" name="password" type="password" value="<?php echo set_value('password'); ?>" size="60" autocomplete="off"
    pattern="\w[^\s\c]{4,49}" title="Minimum 5 characters, letters, numbers and symbols (no spaces)." required />

  <li>
  <label for="password_2">Confirm password</label>
  <input id="password_2" name="confirm_password" type="password" value="<?php echo set_value('confirm_password'); ?>"
    size="60" autocomplete="off" title="The passwords should match."
    oninput="setCustomValidity(value!=password.value ? 'Error, the passwords should match.' : '')" required />
</ul>

  <p><br />
  <button>Install</button>

</form>

