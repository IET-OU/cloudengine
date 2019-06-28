                   </div>
                    <div id="site-footer">
				        <div class="grid">
				        <p class="left">
				        <?= anchor('https://www.open.ac.uk', '©', array('title' => '© 2009-2019 The Open University (IET)', 'class' => 'cp')) ?> |
				        <?= anchor('about/about', t("About")) ?> |
				        <?= anchor('blog/archive', t("Blog")) ?> |
				        <?= anchor('about/tandc', t("Terms and Conditions"), array(
				                'class' => 'rdfa tandc',
				                'rel' => 'license',
				        )) ?> |
				        <?= anchor('about/privacy', t('Privacy notice')) /* GDPR/privacy */ ?> |
				        <?php /* <a href="https://www.open.ac.uk/privacy">Privacy and cookies</a> | */ ?>
				    <?php $this->load->view('layout/capret') ?>
				        <?php $powered = t('Powered by !name, open-source social software', array('!name'=>'CloudEngine')); ?>
                <a href="https://github.com/IET-OU/cloudengine" class="gh"
                  ><img src="<?= base_url()?>/_design/cloudengine-sm.gif" alt="<?= $powered ?>" />
                </a>
				          </p>
				      <?php $this->load->view('layout/language_menu.php') ?>

				        </div>
				    </div>
				</div>
			</div>
		</div>
		<?php $this->load->view('layout/google_analytics.php') ?>
    <?php $this->load->view('layout/footer-javascript.php') ?>
	</body>
</html>
