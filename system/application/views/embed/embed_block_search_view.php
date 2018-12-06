
<?php if ($embeds): ?>
<?php $i = 0; ?>
<?php foreach($embeds as $embed): ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="/_scripts/jquery.oembed.js"></script>

<script>
    $(document).ready(function() {
       $("#oembed<?= $i ?>").oembed("<?= $embed->url ?>");
    });
  </script>
    <h3><?= $embed->title ?></h3>
  <div id="oembed<?= $i ?>"></div>

  <small>
  <?= anchor('user/view/'.$embed->user_id, $embed->fullname) ?>
  </small>

<?php $i++; ?>
<br /><br />
<?php endforeach; ?>
<?php endif; ?>
