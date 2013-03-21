<h1><?= t("Admin Panel") ?></h1>
<h2><?= t("Site Content") ?></h2>
<ul class="arrows">
<li><?= anchor('admin/moderate', t("Moderate clouds, cloudscapes and comments")) ?></li>
<li><?= anchor('blog/add', t("Create new blog post")) ?></li>
<li><?= anchor('admin/featured_cloudscapes', t("Manage featured cloudscapes")) ?></li>
<li><?= anchor('admin/update_site_news', t("Update Site News")) ?></li>
<li><?= anchor('admin/manage_pages', t("Manage Pages")) ?></li>
<?php if ($this->config->item('x_flag')): ?>
<li><?= anchor('admin/flagged', t("Items flagged as spam")) ?></li>
<?php endif; ?>
</ul>


<h2><?= t("Statistics and Admin Data") ?> </h2>
<ul class="arrows">
<li><?= anchor('user/people', t("People")) ?></li>
<li><?= anchor('admin/unactivated_users', t("Unactivated Users")) ?></li>
<li><?= anchor('event/admin', t("Admin Cloudstream")) ?></li>
<li><?= anchor('statistics/stats', t("Site Statistics")) ?></li>
<li><?= anchor('statistics/stats_dates', t("Site Statistics By Date")) ?></li>
<li><?= anchor('statistics/user_stats', t("Site User Statistics (takes a while)")) ?></li>
<li><?= anchor('statistics/cloudscape', t("Cloudscape Statistics")) ?></li>
<li><?= anchor('statistics/cloudscape_date', t("Cloudscape Statistics By Date")) ?></li>
</ul>

<h2>Technical</h2>

<ul class="arrows">
<li><?= anchor('upgrade', t("Run site upgrade")) ?></li>
<li><?= anchor('search/create', t("Update search index")) ?></li>
<li><?= anchor('admin/recalculate_popular', t("Recalculate Popular Clouds and Cloudscapes")) ?></li>
<li><?= anchor('admin/site_settings', t('Site settings and status')) ?></li>
<li><?= anchor('admin/phpinfo', t('Display PHP configuration info')) ?></li>
</ul>


