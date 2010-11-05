

<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Compose message") ?></h1>
    </div>
</div>

<div id="compose-region-1">
    <div class="grid g1">
        <div class="c1of1">
        
          <div class="message-actions-envelope">
            <?=form_open($this->uri->uri_string(), array('id' => 'message-action-form1'))?>
              <input type="hidden" value="submit" name="submit" /> 
              <button id="mark" value="message"     name="location"  id="message"      type="submit"  >All messages</button />
            <?=form_close()?>         
          </div>

          <?php echo '<b>'.validation_errors().'</b>'; ?>

          <div id="compose">
            <div id="message-compose-envelope">
              <?=form_open($this->uri->uri_string(), array('id' => 'compose-message-form'))?>
               
                <label for="recipients" class="compose-label">To:</label>              
                <input type="text" name="recipients" id="recipients" class="compose-field" />
                <hr class="compose-input-divider" />               
                 
                <label for="subject" class="compose-label">Subject:</label>              
                <input type="text" name="subject" id="subject" class="compose-field" />
                <hr class="compose-input-divider" />    
                             
                <label for="content" class="compose-label">Message:</label>
                <textarea id="compose-message-compose-box" name="content" class="compose-field"></textarea>
                <hr class="compose-input-divider" />
                
                <div id="compose-message-submit-envelope">&nbsp;
                  <input type="submit" value="Cancel" id="compose-message-cancel-button" name="cancel" onclick="window.location='message';" /> 
                  <input type="submit" value="Send message" id="compose-message-submit-button" name="submit" /> 
                </div>
                
              <?=form_close()?>
            </div>
          </div>            
          
          <pre><? //print_r($this->_ci_cached_vars); ?></pre>
        
        </div>
        
        
        
    </div>
</div> 

<div id="compose-region-2">
    <?php $this->load->view('user/user_block'); ?>
</div>