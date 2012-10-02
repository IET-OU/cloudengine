<div class="grid">
    <ul class="options">
    <?php if ($edit_permission): ?>
         <li class="button"><?= anchor('badge/edit/'.$badge->badge_id, t("Edit")) ?></li>
          <li class="button"><?= anchor('badge/manage_verifiers/'.$badge->badge_id, t("Manage Verifiers")) ?></li>
    <?php endif; ?>
  
    </ul>
</div>