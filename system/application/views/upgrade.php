<?php
// Upgrade view.
?>
<style>
.upgrade {font-size:1.2em}
.messages li{margin:.4em 0; padding:3px}
.error{border:1px solid red}
.info {border:1px solid green}
</style>
<div class="upgrade">

<h1> Upgrade outcome </h1>

<ol class="messages">
<?php foreach ($messages as $message): ?>
    <li class="<?=$message->class ?>"><?=$message->text ?> <?= ('index'!==$message->context) ? "<code>[function $message->context]</code>" :'' ?></li>
<?php endforeach; ?>

<?php if ($functions):
  foreach ($functions as $method => $result):
      $result = TRUE===$result ? 'success' : 'error'; ?>
    <li class="fn <?=$result ?>"><?=ucfirst($result) ?>, function <?=$method ?></li>
<?php
  endforeach; endif; ?>
</ol>

<p><br/><strong><?=$message_final ?></strong></p>
</div>

<p ><?=anchor('', t("Return to home page")) ?>.</p>