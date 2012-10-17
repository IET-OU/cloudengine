
<div class="grid-badge" >
    <ul class="options">
    <?php if ($edit_permission): ?>
          <li class="button"><?= anchor('badge/edit/'.$badge->badge_id, t("Edit")) ?></li>
          <li class="button"><?= anchor('badge/edit_image/'.$badge->badge_id, t("Edit Image")) ?></li>
          
          <?php if ($badge->type == 'verifier'): ?>
          <li class="button"><?= anchor('badge/manage_verifiers/'.$badge->badge_id, t("Manage Verifiers")) ?></li>
          <?php endif;?>  
          <?php endif; ?>
  
    </ul>
</div>