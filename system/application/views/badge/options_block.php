<div class="grid">
    <ul class="options">
    <?php if ($edit_permission): ?>
         <li class="button"><?= anchor('badge/edit/'.$badge->badge_id, t("Edit")) ?></li>
    <?php endif; ?>
  
    </ul>
</div>