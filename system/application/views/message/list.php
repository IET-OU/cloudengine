<script type="text/javascript" src="<?=base_url()?>_scripts/message.js"></script>

<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Messages")?></h1>
    </div>
</div>

<div id="message-page">
  <div id="message-region-1">
      <div class="grid g1">
          <div class="c1of1">
              <pre><? /*print_r($this->db_session->userdata);*/ /*print_r($this->_ci_cached_vars);*/ ?></pre>
              <?=form_open($this->uri->uri_string(), array('id' => 'thread-list-action-form'))?>

                <div class="message-actions-envelope">
                  <input type="hidden" value="submit" name="submit" /> 
                  <button value="message/compose"     name="location"  id="compose"   type="submit" onclick="window.location='message/compose'; return false;" ><span class="button-message/compose"></span><?= t("Compose") ?></button>
                  <button value="set_unread"  name="thread-action"  id="set-unread"   type="submit" ><span class="button-set_unread"></span><?= t("Mark unread") ?></button>
                  <button value="set_read"    name="thread-action"  id="set-read"     type="submit" ><span class="button-set_read"></span><?= t("Mark read") ?></button> 
                  <button value="set_deleted" name="thread-action"  id="set-deleted"  type="submit" ><span class="button-set_deleted"></span><?= t("Delete") ?></button>    
                </div>

                <?php if($message_display_content) : ?>
                  <div id="message-info-area" class="<?= $message_display_type ?>">
                    <?= $message_display_content; ?>               
                  </div>
                <?php endif; ?>

                <div class="thread-list-head">
                  <?php //Accessibility: better to only have a single table (BB issue #142). ?>
                  <table <?php/*summary="<?=plural(_('Table of !N discussion thread'), _('Table of !N discussion threads'), count($threads)) ?>"*/?>>
                    <thead>
                      <tr class="message-list-top-line" >
                        <th scope="col" class="replied">&nbsp;</th>
                        <th scope="col" class="message-list-checkbox"><input type="checkbox" id="thread_all" title="Select all threads" /></th>
                        <th scope="col" class="last-message-wrapper" colspan="1"><?= t("Last message") ?></th>
                        <th scope="col" class="subject"><?= t("Subject") ?></th>
                        <th scope="col" class="participants"><?= t("Participants") ?></th>
                        <th scope="col" class="message-count"><?= t("Messages") ?></th>
                        <th scope="col" class="delete-message">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody>
                <?php  foreach ($threads as $thread):?>
                          <tr id="<?= $thread->thread_id ?>" class="thread-list-row <?php if (intval($thread->new_messages)): ?>new-message<?php endif; ?>">
                            <td class="cell-click replied">
                              <?php if ($user_id == $thread->last_message_author_id): //A11y: title, no ALT attribute. ?>
                                <img src="<?=base_url()?>_design/replied_arrow.png" title="<?=t('You replied') ?>" />
                              <?php endif; ?>
                            </td>
                            <td class="message-list-checkbox">
                              <input class="thread-checkbox" name="thread_id[]" type="checkbox"  value="<?= $thread->thread_id ?>" title="<?=t("Select thread '!subject'", array('!subject'=>$thread->subject)) ?>" />
                            </td>
                            <?php /*Run 2 <td> cells together.*/?>
                            <td class="cell-click last-message-wrapper profile-pic">
                              <div class="thread-profile-pic">
                                <?php if ($thread->picture): ?>
                                    <?=anchor("user/view/$thread->last_message_author_id", img(array('src'=>base_url().'image/user/'.$thread->last_message_author_id, 'height' => '50', 'width' => '50', 'alt' => $thread->last_message_author_name))) ?>
                                <?php else: ?>
                                    <?=anchor("user/view/$thread->last_message_author_id", img(array('src'=>base_url().'_design/avatar-default-32.jpg', 'alt' => $thread->last_message_author_name, 'class' => 'go2 message-pic-anon'))) ?>                                    
                                <?php endif; ?>
                              </div>
                              <div class="last-message-author message-list-top-line">
                                <?php if($thread->last_message_author_id == $user_id): ?>
                                  <?=anchor("user/view/$thread->last_message_author_id", 'You') ?>
                                <?php else: ?>
                                  <?=anchor("user/view/$thread->last_message_author_id", $thread->last_message_author_name) ?>
                                <?php endif; ?>
                              </div>
                              <div class="last-message-date message-list-bottom-line">
                                <?= format_date('!date-time-abbr!', $thread->last_message_date) ?>
                              </div>
                            </td>
                            <td class="cell-click subject">
                              <?=anchor("message/thread/" .$thread->thread_id, $thread->subject, 'class="message-list-top-line"' ) ?>
                              <div class="preview message-list-bottom-line">
                                <?= $thread->content_preview ?>
                              </div>
                            </td>
                            <td class="cell-click participants message-list-bottom-line"></a>
                              <?php //this looks a bit of a mess but needed to close code to suppress white space ?>
                              <?= anchor("user/view/" .$user_id, 'You') ?><?php if ($thread->other_participant_count == 1): ?><?= ' and ' .anchor("user/view/" .$thread->other_participants[0]->user_id, trim($thread->other_participants[0]->name)) ?><?php else: ?><?php for($i=0; $i < $thread->other_participant_count; $i++): ?><?php if ($i < 3): ?><?=', '.anchor("user/view/".$thread->other_participants[$i]->user_id, trim($thread->other_participants[$i]->name)) ?><?php endif; ?><?php endfor; ?><?php if ($thread->other_participant_count > 3): ?> + <?php print($thread->other_participant_count - 3); ?> others<?php endif; ?> <?php endif; ?>

                            </td>
                            <td class="cell-click message-count message-list-top-line">
                              <?= $thread->total_messages ?><span class="accesshide"> <?=t('messages'); //Accessibility: hidden text. ?></span>
                              <?php if (intval($thread->new_messages)): ?>
                                <div class='new-messages message-list-bottom-line'> <?= $thread->new_messages ?> <?=t('new')?> </div>
                              <?php endif; ?>
                            </td>
                            <td class="delete-message message-list-top-line">
                              <button value="<?= $thread->thread_id ?>" name="delete_thread" class="delete-cross"  type="submit" title="<?=t('Delete message')?>" >
                                <span class="button-<?= $thread->thread_id ?>"></span><img src="<?=base_url()?>_design/delete_cross.png" alt="" />
                              </button>
                            </td>
                          </tr>
                <?php endforeach; ?>

              </tbody></table>

            <?=form_close()?>
          </div>
      </div>
  </div>
</div>

<div id="message-region-2">
    <?php $this->load->view('user/user_block'); ?>
</div>