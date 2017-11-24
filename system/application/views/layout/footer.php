                   </div>
                    <div id="site-footer">
				        <div class="grid">
				        <p class="left">
				        <?= anchor('about/about', t("About")) ?> |
				        <?= anchor('blog/archive', t("Blog")) ?> |
				        <?= anchor('about/tandc', t("Terms and Conditions"), array(
				                'class' => 'rdfa',
				                'rel' => 'license',
				        )) ?> |
				    <?php $this->load->view('layout/capret') ?>
				        <?php $powered = t('Powered by !name, open-source social software', array('!name'=>'CloudEngine')); ?>
				        <img src="<?= base_url()?>/_design/cloudengine-sm.gif" alt="<?= $powered ?>" />
				          </p>
				      <?php $this->load->view('layout/language_menu.php') ?>

				        </div>
				    </div>
				</div>
			</div>
		</div>
		<?php $this->load->view('layout/google_analytics.php') ?>

    <script> console.warn('nofollow:', <?= Nofollow::get_count() ?>); </script>
	</body>
</html>
