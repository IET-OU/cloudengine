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
                  <button id="mark" value="message/compose"     name="location"  id="compose"      type="submit" onclick="window.location='message/compose'; return false;" ><?= t("Compose") ?></button />
                  <button id="mark" value="set_unread"  name="thread-action"  id="set-unread"   type="submit" ><?= t("Mark unread") ?></button />
                  <button id="mark" value="set_read"    name="thread-action"  id="set-read"     type="submit" ><?= t("Mark read") ?></button /> 
                  <button id="mark" value="set_deleted" name="thread-action"  id="set-deleted"  type="submit" ><?= t("Delete") ?></button />    
                </div>
                
                <?php if($message_display_content) : ?>
                  <div id="message-info-area" class="<?= $message_display_type ?>">
                    <?= $message_display_content; ?>               
                  </div>
                <?php endif; ?>
                
                <div class="thread-list-head">            
                  <table>
                    <thead>
                      <tr class="message-list-top-line" >
                        <th class="replied">&nbsp;</th>
                        <th class="message-list-checkbox"><input type="checkbox" id="thread_all" title="select all"></th>
                        <th class="last-message-wrapper"><?= t("Last message") ?></th>
                        <th class="subject"><?= t("Subject") ?></th>
                        <th class="participants"><?= t("Participants") ?></th>
                        <th class="message-count"><?= t("Messages") ?></th>
                        <th class="delete-message">&nbsp;</th>
                      </tr>                                     
                    </thead>
                  </table>
                </div>
                
                <?php  foreach ($threads as $thread):?>
                  <div>
                    <div class="thread-list-row <?php if (intval($thread->new_messages)): ?>new-message<?php endif; ?>">
                      <table>
                        <tbody>
                          <tr>
                            <td class="replied">
                              <?php if ($user_id == $thread->last_message_author_id): ?>
                                <img src="<?=base_url()?>_design/replied_arrow.png" />
                              <?php endif; ?>
                            </td>
                            <td class="message-list-checkbox">
                              <input class="thread-checkbox" name="thread_id[]" type="checkbox"  value="<?= $thread->thread_id ?>" />
                            </td>
                            <td class="profile-pic">
                              <div class="thread-profile-pic">
                                <?php if ($thread->picture): ?>
                                    <img src="<?= base_url() ?>image/user/<?= $thread->last_message_author_id ?>" height="50" width="50" class="go2" alt="" />
                                <?php else: ?>
                                    <img src="<?=base_url() ?>_design/avatar-default-32.jpg" class="go2 message-pic-anon" alt="" />
                                <?php endif; ?>
                              </div>
                            </td>
                            <td class="last-message-wrapper">
                              <div class="last-message-author message-list-top-line">
                                <?php if($thread->last_message_author_id == $user_id): ?>
                                  <?=anchor("user/view/$thread->last_message_author_id", 'You') ?>
                                <?php else: ?>
                                  <?=anchor("user/view/$thread->last_message_author_id", $thread->last_message_author_name) ?>
                                <?php endif; ?>
                              </div>
                              <div class="last-message-date message-list-bottom-line">
                                
                                <?= format_date('!date-time!',$thread->last_message_date) ?>
                              </div>
                            </td>       
                            <td class="subject">
                              <?=anchor("message/thread/" .$thread->thread_id, $thread->subject, 'class="message-list-top-line"' ) ?>
                              <div class="preview message-list-bottom-line">
                                <?= $thread->content_preview ?>
                              </div>
                            </td>                        
                            <td class="participants message-list-bottom-line"></a>
                              <?php //this looks a bit of a mess but needed to close code to suppress white space ?>
                              <?= anchor("user/view/" .$user_id, 'You') ?><?php if ($thread->other_participant_count == 1): ?><?= ' and ' .anchor("user/view/" .$thread->other_participants[0]->user_id, trim($thread->other_participants[0]->name)) ?><?php else: ?><?php for($i=0; $i < $thread->other_participant_count; $i++): ?><?php if ($i < 3): ?><?=', '.anchor("user/view/".$thread->other_participants[$i]->user_id, trim($thread->other_participants[$i]->name)) ?><?php endif; ?><?php endfor; ?><?php if ($thread->other_participant_count > 3): ?> + <?php print($thread->other_participant_count - 3); ?> others<?php endif; ?> <?php endif; ?>
                            
                            </td>                                            
                            <td class="message-count message-list-top-line">
                              <?= $thread->total_messages ?> 
                              <?php if (intval($thread->new_messages)): ?>
                                <div class='new-messages message-list-bottom-line'> (<?= $thread->new_messages ?>  new) </div>
                              <?php endif; ?>
                            </td>
                            <td class="delete-message message-list-top-line">
                              <button value="<?= $thread->thread_id ?>" name="delete_thread" class="delete-cross"  type="submit" >
                                <img src="<?=base_url()?>_design/delete_cross.png" />
                              </button>
                            </td> 
                          </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              <?php endforeach; ?>
            <?=form_close()?>
          </div>
      </div>
  </div> 
</div>

<div id="message-region-2">
    <?php $this->load->view('user/user_block'); ?>
</div>