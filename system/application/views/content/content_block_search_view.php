

<?php if(count($contents) > 0):?>
    <?php foreach ($contents as $content):?>
		<div class="extra-content">
			<?= $content->body ?>
			<div class="posted-by">
					<p class="date-stamp">
					<?= anchor('user/view/'.$content->id, $content->fullname) ?>
					<br /> <?=format_date(NULL, $content->created) ?>
					<?php if ($content->modified): ?>
						<?php #=t("Edited !date") ?>
						(<?=format_date(_("Edited !date-time!"), $content->modified) ?>)
					<?php endif; ?>
					</p>
			</div>
		</div>
    <?php endforeach; ?>
<?php endif; ?>
