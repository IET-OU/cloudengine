<div id="permissions">
  <h1><?=t("Permissions for the cloudscape !title", 
    array('!title'=>"<a href='".base_url()."cloudscape/view/$cloudscape->cloudscape_id'>$cloudscape->title</a>"))?></h1>

	<p>
	<?= anchor('cloudscape/view/'.$cloudscape->cloudscape_id,  t("Back to cloudscape")) ?></p>
	<h2><?=t("Owner")?></h2>
	<p><?= $cloudscape->fullname ?></p>
	
	<?php if ($cloudscape->open): /*/Translators: Cloudscape permission, "Open" for anybody. */ ?>
		<h2><?=t("Open")?></h2>
		<p><?=t("This cloudscape is open for anybody to post clouds to.")?></p>
		<p><?= anchor('cloudscape/close/'. $cloudscape->cloudscape_id , t("Make cloudscape closed")) ?>
		</p>
	<?php else: ?>
		<h2><?=t("Closed")?></h2>
		<p><?=t("This cloudscape is <b>not</b> open for anybody to post clouds to.")?></p>
		<p><?= anchor('cloudscape/open/'. $cloudscape->cloudscape_id , t("Make cloudscape open")) ?></p>
	<?php endif;
	  /*/Translators: "Admins" - Cloudscape administrators. */ ?>
	
	<h2><?=t("Admins")?></h2>
	<?php if(count($admins) != 0): ?>
	    <table>
		    <?php foreach ($admins as $admin): ?>
			    <tr>
			    	<td><?= $admin->fullname ?></td>
			     	<td>
			     	<?= anchor('cloudscape/admin_remove/'. $cloudscape->cloudscape_id.'/'.
			     	  $admin->id, t("Remove as admin")) ?>
			    </td>
			    </tr>
		    <?php endforeach; ?>
	    </table>
	<?php else: ?>
		<p><?=t("No admins set")?></p>
	<?php endif;
	  /*/Translators: Cloudscape "posters" - people who can add clouds to closed Cloudscapes. */ ?>
	
	<h2><?=t("Posters")?></h2>
	<?php if(count($posters) != 0): ?>
	    <table>
		    <?php foreach ($posters as $poster): ?>
			    <tr>
				    <td><?= $poster->fullname ?></td>
				    <td>
				    <?= anchor('cloudscape/poster_remove/'. $cloudscape->cloudscape_id.'/'.
			     	  $poster->id, t("Remove as poster")) ?>
			</td>
			    </tr>
		    <?php endforeach; ?>
	    </table>
	<?php else: ?>
		<p><?=t("No posters set")?></p>
	<?php endif; ?>
	
	<h2><?=t("Add new user")?></h2>
	<?php if($users && count($users) == 0): ?>
		<p><?=t("No results found for !item", array('!item'=>"<b>$user_search_string</b>")) ?></p>
	<?php elseif ($users): ?>
		<p><?=t("Results for !item", array('!item'=>"<b>$user_search_string</b>"))?></p>
		<table>
		    <?php foreach ($users as $user): ?>
			    <tr>
			    	<td> <?= $user->fullname ?></td>
			     	<td>
			     	<?= anchor('cloudscape/admin_add/'. $cloudscape->cloudscape_id.'/'.
			     	  $user->id, t("Add as admin")) ?>
			     	</td>
			     				<td>     	<?= anchor('cloudscape/poster_add/'. $cloudscape->cloudscape_id.'/'.
			     	  $user->id, t("Add as poster")) ?></td>
			    </tr>
		    <?php endforeach; ?>
		</table>
	<?php endif; ?>

	<?=form_open($this->uri->uri_string(), array('id' => 'cloud-permissions-form'))?>
	    <p><label for="user_search_string"><?=t("Search users")?></label>
	 	<input type="text" maxlength="128" name="user_search_string" id="user_search_string"  size="95" value="" />
	 	
	 	<button type="submit" name="submit" class="submit" value="Search"><?=t("Search") ?></button>
	 	</p>
	 <?=form_close()?>
</div>