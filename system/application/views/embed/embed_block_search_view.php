
<?php if ($embeds): ?>
<?php $i = 0; ?>
<?php foreach($embeds as $embed): ?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>  
<script type="text/javascript" src="/_scripts/jquery.oembed.js"></script>

<script type="text/javascript">
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
