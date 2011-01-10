
<h1><?= t("Statistics for !title from !start-date to !end-date",
    array('!title'=>anchor("cloudscape/view/$cloudscape->cloudscape_id", $cloudscape->title),
	      '!start-date'=>date('H:i j F Y', $starttime), '!end-date'=>date('H:i j F Y', $endtime))) ?></h1>
<p><?= anchor('admin/panel', t("Back to admin panel")) ?></p>
<div id="region1">
<h2>Number distinct logged in visitors to clouds in this cloudscape</h2>
<p><?= $visitors_logged_in ?></p>
<h2>Number distinct guest visitors (i.e. different IP addresses) to clouds in this cloudscape</h2>
<p><?= $visitors_guest ?></p>
</div>