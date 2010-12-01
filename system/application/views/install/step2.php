
<h1>Installing</h1>

<?php if (isset($messages)): ?>
<ol class="messages" id="messages">
<?php foreach ($messages as $class => $message): ?>
    <li class="<?php echo $class ?>"><?php echo $message; ?></li>
<?php endforeach; ?>
</ol>
<?php endif; ?>



<p class="complete"><strong>Congratulations, installation is complete.</strong> <?php echo anchor("", "Go to the home page and log in") ?>.</p>

<p>Note, you will probably want to change your "fullname" and "organization" in your user-profile.
  <?php /*BB bug #82: provide help with creating a search index. */ ?>
  <a href="http://getcloudengine.org//wiki/Config#wiki">Read how to configure features, for instance <em>search</em> and <em>analytics</em></a>.</p>

