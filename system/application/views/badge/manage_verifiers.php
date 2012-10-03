<h1><?= $badge->name ?></h1>
<p><?= anchor('badge/view/'.$badge->badge_id, t("Back to badge")) ?></p>
    <h2><?=t("Verifiers")?></h2>
    <?php if(count($verifiers) != 0): ?>
        <table>
            <?php foreach ($verifiers as $verifier): ?>
                <tr>
                    <td><?= $verifier->fullname ?></td>
                     <td>
                     <?= anchor('badge/verifier_remove/'. $badge->badge_id.'/'.
                       $verifier->id, t("Remove as verifier")) ?>
                </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p><?=t("No verifiers set")?></p>
    <?php endif; ?>
    
    <h2><?=t("Add new verifier")?></h2>
    <?php if($users && count($users) == 0): ?>
        <p><?=t("No results found for !item", array('!item'=>"<b>$user_search_string</b>")) ?></p>
    <?php elseif ($users): ?>
        <p><?=t("Results for !item", array('!item'=>"<b>$user_search_string</b>"))?></p>
        <table>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td> <?= $user->fullname ?></td>
                     <td>
                     <?= anchor('badge/verifier_add/'. $badge->badge_id.'/'.
                       $user->id, t("Add as verifier")) ?>
                     </td>
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
    