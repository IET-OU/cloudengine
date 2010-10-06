
<h1><?= t("Statistics for ").anchor('cloudscape/view/'.$cloudscape->cloudscape_id, $cloudscape->title) ?></h1>
<p><?= anchor('/admin/panel', t("Back to admin panel")) ?></p>
<div id="region1">
<h2>Content</h2>
<table>
<tr>
    <td>
    Number of clouds
    </td>
 <td><?= $cloudscape->total_clouds ?></td>
</tr>



<tr>
    <td>
    Number of comments
    </td>
 <td><?= $cloudscape->total_comments?></td>
</tr>

<tr>
    <td>
    Number of embeds
    </td>
 <td><?= $cloudscape->total_embeds ?></td>
</tr>

<tr>
    <td>
    Number of items of extra content
    </td>
 <td><?= $cloudscape->total_content?></td>
</tr>
<tr>
    <td>
    Number of links
    </td>
 <td><?= $cloudscape->total_links?></td>
</tr>
</table>
<h2>People</h2>
<table>
<tr>
    <td>
    Number of followers
    </td>
 <td><?= $cloudscape->total_followers ?></td>
</tr>
<tr>
    <td>
    Number of attendees
    </td>
 <td><?= $cloudscape->total_attendees ?></td>
</tr>


<tr>
    <td>
    Number of distinct people commenting
    </td>
 <td><?= $cloudscape->total_commenters?></td>
</tr>
</table>
<h2>Views</h2>
<table>
<tr>
    <td>
    Number of views of cloudscape
    </td>
 <td>
 <?= $cloudscape->total_views ?></td>
</tr>
<tr>
    <td>
    Number of distinct people logged in and viewing cloudscape clouds
    </td>
 <td><?= $cloudscape->total_logged_in?></td>
</tr>

<tr>
    <td>
    Number of distinct guests (i.e. distinct IP addresses) viewing cloudscape clouds
    </td>
 <td><?= $cloudscape->total_guests?></td>
</tr>

</table>


</div>