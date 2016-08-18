<?php $this->load->view('install/install_header') ?>
<?php echo $content_for_layout ?>


        <div id="site-footer">
	        <div class="grid">
		        <p class="left">
		        <?php $powered = t('Powered by !name, open-source social software', array('!name'=>'CloudEngine')); ?>
		        <img src="<?= base_url()?>/_design/cloudengine-sm.gif" alt="<?= $powered ?>" />
		          </p>

	        </div>
	    </div>

	</div></div></div>

</body></html>
