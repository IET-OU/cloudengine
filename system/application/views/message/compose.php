<script type="text/javascript" src="<?=base_url()?>_scripts/message.js"></script>

<script>
//jQuery for the recipient 'To' dropdown list
$(function() {

  function split( val ) {
		return val.split( /,\s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}

	$( "#recipients" ).autocomplete({
		source: function( request, response ) {
			$.getJSON( "<?=base_url()?>message/get_message_recipients", {
				term: extractLast( request.term )
			}, response );
		},
		search: function() {
			// custom minLength
			var term = extractLast( this.value );
			if ( term.length < 2 ) {
				return false;
			}
		},
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		select: function( event, ui ) {
			var terms = split( this.value );
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push( ui.item.value );
			// add placeholder to get the comma-and-space at the end
			terms.push( "" );
			this.value = terms.join( ", " );
			return false;
		}
	});


});
</script>

<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Compose message") ?></h1>
    </div>
</div>

<div id="message-page">
  <div id="compose-region-1">
      <div class="grid g1">
          <div class="c1of1">

            <div class="message-actions-envelope">
              <?=form_open($this->uri->uri_string(), array('id' => 'message-action-form1'))?>
                <input type="hidden" value="submit" name="submit" />
                <button class="green" id="mark" value="message" name="location" id="message" type="submit"  ><span class="button-message"></span><?= t("All messages") ?></button />
              <?=form_close()?>
            </div>

            <?php if($message_display_content): ?>
              <div id="message-info-area" class="<?= $message_display_type ?>">
                <?= $message_display_content; ?>
              </div>
            <?php elseif(validation_errors()): ?>
              <div id="message-info-area" class="error">
                <?= validation_errors() ?>
              </div>
            <?php endif; ?>

            <div id="compose">
              <div id="message-compose-envelope">
                <?=form_open($this->uri->uri_string(), array('id' => 'compose-message-form'))?>

                  <p id="compose-to-help"><?= t("Search for recipients using the user's full name.") ?></p>
                  <label for="recipients" class="compose-label"><?= t("To") ?>:</label>
                  <input type="text" name="recipients" id="recipients" class="compose-field" value="<?= implode(', ',$valid_recipients) ?>" />
                  <hr class="compose-input-divider" />

                  <label for="subject" class="compose-label"><?= t("Subject") ?>:</label>
                  <input type="text" name="subject" id="subject" class="compose-field" value="<?= $subject ?>" />
                  <hr class="compose-input-divider" />

                  <label for="compose-message-compose-box" class="compose-label"><?= t("Message") ?>:</label>
                  <textarea id="compose-message-compose-box" name="content" class="compose-field"><?= $content ?></textarea>
                  <hr class="compose-input-divider" />

                  <div id="compose-message-submit-envelope">&nbsp;
                    <input type="submit" value="<?= t("Cancel") ?>" id="compose-message-cancel-button" name="cancel" onclick="window.location='message';" />
                    <input type="submit" value="<?= t("Send message") ?>" id="compose-message-submit-button" name="submit" />
                  </div>

                <?=form_close()?>
              </div>
            </div>

            <pre><?php //print_r($this->_ci_cached_vars); ?></pre>

          </div>

      </div>
  </div>
</div>

<div id="compose-region-2">
    <?php $this->load->view('user/user_block'); ?>
</div>
