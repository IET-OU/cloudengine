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

  <h1><?=$title ?></h1>
    
  <? if($continue): ?>
    <p><?= t("Upgrade to v$version will perform the following steps:") ?></p>
  <? endif; ?>
    
  <ol class="messages">
  <?php foreach ($messages as $message): ?>
      <li class="<?=$message->class ?>"><?=$message->text ?> <?= ('index'!==$message->context) ? "<code>[function $message->context]</code>" :'' ?></li>
  <?php endforeach; ?>
  </ol>

  <? if($continue): ?>
    <?=form_open($this->uri->uri_string(), array('id' => 'upgrade-confirm-form'))?>
      <br />
      <input type="submit" value="Proceed" name="submit" class="submit" />
      <br /> 
    <?=form_close()?>    
  <? endif; ?>

</div>

<p ><br /><?=anchor('', "Return to home page") ?>.</p>