

<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Conversation - " .$thread[0]->subject) ?></h1>
    </div>
</div>

<div id="thread-region-1">
    <div class="grid g1">
        <div class="c1of1">
          
          <div class="message-actions-envelope">
            <?=form_open($this->uri->uri_string(), array('id' => 'message-action-form1'))?>
              <input type="hidden" value="submit" name="submit" /> 
              <input type="hidden" value="<?= $thread_id ?>" name="thread_id" />
              <button id="mark" value="message"   name="location"       id="message"      type="submit"  onclick="window.location='message'; return false;" >All messages</button />
              <button id="mark" value="set_unread"  name="thread-action"  id="set-unread"   type="submit" >Mark unread</button />
              <button id="mark" value="set_deleted" name="thread-action"  id="set-deleted"  type="submit" >Delete</button />     
            <?=form_close()?>         
          </div>
          
          <div class="thread-participants">
            <span class="strong">Participants:</span> &nbsp; <?= anchor("user/view/".$user_id, 'You') ?><?php  foreach ($participants as $participant):?><?= ',&nbsp; ' .anchor("user/view/".$participant->user_id, $participant->name) ?><?php endforeach; ?>
          </div>
          <div id="thread-messages">
            <?php  foreach ($thread as $message):?>
              <div class="thread-message">
                <div class="thread-message-profile-pic">
                  <?php if ($message->picture): ?>
                      <img src="<?= base_url() ?>image/user/<?= $message->author_user_id ?>" height="50" width="50" class="go2" alt="" />
                  <?php else: ?>
                      <img src="<?=base_url() ?>_design/avatar-default-32.jpg" class="go2 message-pic-anon" alt="" />
                  <?php endif; ?>                
                </div>
                <div class="thread-message-envelope">
                  <div class="thread-message-header">
                    <span class="thread-message-author"><?= anchor("user/view/".$message.author_user_id, $message->author_name) ?></span>
                    <span class="thread-message-date"><?= date("j F Y \a\\t g:i", $message->created) ?></span>
                  </div>
                  <div class="thread-message-content">
                    <?= $message->content ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
            <div id="thread-message-compose-envelope">
              <div id="thread-message-compose-box-label">
              Reply:
              </div>
              <div id="thread-message-compose-area">
                <?=form_open($this->uri->uri_string(), array('id' => 'thread-reply-form'))?>
                <textarea id="thread-message-compose-box" name="content"></textarea>
                <div id="thread-message-submit-envelope">
                  <input type="submit" value="Submit reply" id="thread-message-submit-button" name="submit" /> 
                </div>
                <?=form_close()?>
              </div>
            </div>
         
          </div>
          <div class="message-actions-envelope">
            <?=form_open($this->uri->uri_string(), array('id' => 'message-action-form2'))?>
              <input type="hidden" value="submit" name="submit" /> 
              <input type="hidden" value="<?= $thread_id ?>" name="thread_id" />              
              <button id="mark" value="all-messages" name="thread-action" id="all-messages" type="button" onclick="window.location='message';" >All messages</button />
              <button id="mark" value="set_unread"  name="thread-action"  id="set-unread"   type="submit" >Mark unread</button />
              <button id="mark" value="set_deleted" name="thread-action"  id="set-deleted"  type="submit" >Delete</button />     
            <?=form_close()?>         
          </div>      
          <pre><? //print_r($this->_ci_cached_vars); ?></pre>
        </div>
        
        
        
    </div>
</div> 

<div id="thread-region-2">
    <?php $this->load->view('user/user_block'); ?>
</div>