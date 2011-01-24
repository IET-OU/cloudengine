<h1><?= t("Admin Panel") ?></h1>
<h2><?= t("Site Content") ?></h2>
<ul class="arrows">
<li><?= anchor('admin/moderate', t("Moderate clouds, cloudscapes and comments")) ?></li>
<li><?= anchor('blog/add', t("Create new blog post")) ?></li>
<li><?= anchor('admin/featured_cloudscapes', t("Manage featured cloudscapes")) ?></li>
<li><?= anchor('admin/update_site_news', t("Update Site News")) ?></li>
<li><?= anchor('admin/manage_pages', t("Manage Pages")) ?></li>
</ul>


<h2><?= t("Statistics and Admin Data") ?> </h2>
<ul class="arrows">
<li><?= anchor('event/admin', t("Admin Cloudstream")) ?></li>
<li><?= anchor('statistics/stats', t("Site Statistics")) ?></li>
<li><?= anchor('statistics/stats_dates', t("Site Statistics By Date")) ?></li>
<li><?= anchor('statistics/cloudscape', t("Cloudscape Statistics")) ?></li>
<li><?= anchor('statistics/cloudscape_date', t("Cloudscape Statistics By Date")) ?></li>
</ul>

<h2>Technical</h2>

<ul class="arrows">
<li><?= anchor('search/create', t("Update search index")) ?></li>
<li><?= anchor('admin/recalculate_popular', t("Recalculate Popular Clouds and Cloudscapes")) ?></li>
<li><?= anchor('admin/site_settings', t('Site settings and status')) ?></li>
<li><?= anchor('admin/phpinfo', t('Display PHP configuration info')) ?></li>
</ul>


