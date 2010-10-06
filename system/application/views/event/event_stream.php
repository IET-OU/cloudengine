<?php if ($events): ?>
<ul class="cloudstream">

<?php foreach ($events as $event): ?>
    <?= $event ?>
<?php endforeach; ?>

</ul>
<?php else: ?>
<p><?=t("No events")?></p>
<?php endif; ?>