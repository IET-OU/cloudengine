				    <div id="site-footer">
				        <div class="grid">
				        <p class="left">
				        <?= anchor('about/about', t("About")) ?> | 
				        <?= anchor('blog/archive', t("Blog")) ?> | 
				        <?= anchor('about/tandc', t("Terms and Conditions")) ?> |
				        <?php $powered = t('Powered by !name, open-source social software', array('!name'=>'CloudEngine')); ?>
				        <a href="http://getcloudengine.org/" rel="bookmark"
				          class="poweredby" title="<?= $powered ?>"
				          ><img src="<?= base_url()?>/_design/cloudengine-sm2.gif" alt="<?= $powered ?>" /></a>
				          </p>
				      <?php $this->load->view('layout/language_menu.php') ?>
				        
				        </div>
				    </div>
				</div>
			</div>
		</div>
		<?php $this->load->view('layout/google_analytics.php') ?>
	</body>
</html>
