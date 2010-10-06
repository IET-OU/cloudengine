				    <div id="site-footer">
				        <div class="grid">
				        <p class="left">
				        <?= anchor('about/about', t("About")) ?> | 
				        <?= anchor('blog/archive', t("Blog")) ?> | 
				        <?= anchor('about/tandc', t("Terms and Conditions")) ?></p>
				      <?php $this->load->view('layout/language_menu.php') ?>
				        
				        </div>
				    </div>
				</div>
			</div>
		</div>
		<?php $this->load->view('layout/google_analytics.php') ?>
	</body>
</html>
