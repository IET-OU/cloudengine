
<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Messages")?>        <?php echo '<b>'.validation_errors().'</b>'; ?></h1>
    </div>
</div>

<div id="message-region-1">
    <div class="grid g1">
        <div class="c1of1">
            <pre><? //print_r($this->_ci_cached_vars); ?></pre>
            <?=form_open($this->uri->uri_string(), array('id' => 'thread-list-action-form'))?>
              <div class="message-actions-envelope">
                <input type="hidden" value="submit" name="submit" /> 
                <button id="mark" value="message/compose"     name="location"  id="compose"      type="submit" onclick="window.location='message/compose'; return false;" >Compose</button />
                <button id="mark" value="set_unread"  name="thread-action"  id="set-unread"   type="submit" >Mark unread</button />
                <button id="mark" value="set_read"    name="thread-action"  id="set-read"     type="submit" >Mark read</button /> 
                <button id="mark" value="set_deleted" name="thread-action"  id="set-deleted"  type="submit" >Delete</button />    
              </div>
              <div class="thread-list-head">            
                <table>
                  <thead>
                    <tr class="message-list-top-line" >
                      <th class="replied">&nbsp;</th>
                      <th class="message-list-checkbox">&nbsp;</th>
                      <th class="last-message-wrapper">Last message</th>
                      <th class="subject">Subject</th>
                      <th class="participants">Participants</th>
                      <th class="message-count">Messages</th>
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
                          <td class="message-list-checkbox"><input name="thread_id[]" type="checkbox"  value="<?= $thread->thread_id ?>" /></td>
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
                              <?=anchor("user/view/$thread->last_message_author_id", $thread->last_message_author_name) ?>
                            </div>
                            <div class="last-message-date message-list-bottom-line">
                              <?= date("j M Y \a\\t g:i", $thread->last_message_date) ?>
                            </div>
                          </td>       
                          <td class="subject">
                            <?=anchor("message/thread/" .$thread->thread_id, $thread->subject, 'class="message-list-top-line"' ) ?>
                            <div class="preview message-list-bottom-line">
                              <?= $thread->content_preview ?>
                            </div>
                          </td>                        
                          <td class="participants message-list-bottom-line"></a>
                            <?= anchor("user/view/" .$user_id, 'You') ?><?php if ($thread->participant_count > 2): ?><?php for($i=0; $i < $thread->participant_count; $i++): ?><?php if ($i < 3): ?><?=', ' .anchor("user/view/".$thread->participants[$i]->user_id, $thread->participants[$i]->name) ?><?php endif; ?><?php endfor; ?><?php if ($thread->participant_count_over_4): ?> + <?= $thread->participant_count_over_4 ?> others<?php endif; ?>
                            <?php else: ?><?= ' and ' .anchor("user/view/" .$thread->participants[0]->user_id, $thread->participants[0]->name) ?>
                            <?php endif; ?>
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

<div id="message-region-2">
    <?php $this->load->view('user/user_block'); ?>
</div>