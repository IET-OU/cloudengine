<h1><?= t("Unactivated Users") ?></h1>
<p><?= t("Please do not activate users unless you know what you are doing!") ?>
<?php if ($users): ?>
<table>
<thead>
<tr>
<th><?= t("Username") ?></th>
<th><?= t("Email") ?></th>
<th><?= t("Full Name") ?></th>
<th><?= t("Activation Code") ?></th>
<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php foreach($users as $user): ?>
<tr>
<td><?= $user->user_name ?></td>
<td><?= $user->email ?></td>
<td><?= $user->fullname ?></td>
<td><?= $user->activation_code ?></td>
<td>
<?= form_open($this->uri->uri_string(), array('id' => 'user-activate-form-'+$user->id))?>
<?= form_hidden('temp_user_id', $user->id); ?>
<?= form_hidden('activation_code', $user->activation_code) ?>
<button type="submit" name="submit" class="submit" value="Activate"><?=t("Activate")?></button>
<?= form_close(); ?>

</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<?= t("No unactivated users") ?>
<?php endif; ?>
