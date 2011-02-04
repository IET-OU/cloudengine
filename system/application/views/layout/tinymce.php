<?php
    //Accessibility: encourage authors to use headings to structure longer texts.
    $editor_headings =TRUE;

    $use_editor = TRUE; 
    if ($this->auth_lib->is_logged_in()) {
            $user_id = $this->db_session->userdata('id');
            $this->CI = & get_instance();
            $this->CI->load->model('user_model');
            $profile = $this->CI->user_model->get_user($user_id);
            if ($profile->do_not_use_editor) {
                $use_editor = FALSE;
            }
    }


	// Disable the rich-editor for most mobiles and tablets. EXPERIMENTAL (BB issue #131).
	//
	//$config['device_no_richedit_pattern'] = 'iPhone|iPod|iPad|Android|IEMobile|Opera Mini|--Firefox';
	$device_pattern = config_item('device_no_richedit_pattern');
	if ($device_pattern) {
	    // Sanitize the $config variable.
		$device_pattern = addcslashes(trim($device_pattern, '|'), '.*+?^$()<{[\>');
		if (preg_match("/($device_pattern)/i", $_SERVER['HTTP_USER_AGENT'])) {
			$use_editor = FALSE;
		}
	}

?>
<?php if ($use_editor): ?>
<script type="text/javascript" src="<?= base_url()?>_scripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
        tinyMCE.init({
            language : "<?=$this->lang->lang_code() /*@i18n: Tiny MCE. */ ?>",
            mode : "textareas",
            theme : "advanced",
            plugins : "paste,fullscreen",
            theme_advanced_buttons1 : "<?= ($editor_headings)? 'formatselect,' : ''
                ?>bold,italic,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,|,code,|,pasteword,fullscreen",
            theme_advanced_buttons2 : "",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "none",
            theme_advanced_resize_horizontal : false,
            theme_advanced_resizing : true,
        <?php if ($editor_headings):
        ?>    //Accessibility: when we have 'formatselect', discourage use of [H1] (in site template).
            theme_advanced_blockformats : "p,pre,h2,h3,h4",<?php //address,blockquote?
        endif; ?>

            apply_source_formatting : true,
            relative_urls: false,
            remove_script_host: false,
            document_base_url: "<?=base_url() ?>",
            content_css: "<?=base_url() ?>_design/tinymce.css",
            invalid_elements: "span,font"});
</script>
<?php endif; ?>

