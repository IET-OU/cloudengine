<h1>Statistics for <?= $startdate ?> to <?= $enddate ?></h1>
<p><?= anchor('statistics/stats', t("Back to statistics")) ?></p>
<p><strong>Total new users registered in this period:</strong> <?= $user_total ?></p>
<p><strong>Total distinct logged in users in this period:</strong> <?= $active_total ?></p>
<table width="200px">
<tr><th></th><th>Everyone</th><th>Team</th><th>Non-team</th><th>% Non-team</th></tr>
<tr><td>Clouds</td> <td><?= $cloud_total ?></td><td><?= $cloud_team_total ?></td><td><?= $cloud_total - $cloud_team_total?></td>

<td><?= 0==$cloud_total? '-' :round(($cloud_total - $cloud_team_total)*100/$cloud_total, 1) ?>%</td></tr>


<tr><td>Cloudscapes: </td><td><?= $cloudscape_total ?></td><td><?= $cloudscape_team_total ?></td>
<td><?= $cloudscape_total - $cloudscape_team_total?></td>

<td><?= 0==$cloudscape_total? '-' :round(($cloudscape_total - $cloudscape_team_total)*100/$cloudscape_total, 1) ?>%</td></tr>

<tr><td>Comments:</td> <td><?= $comment_total ?></td><td><?= $comment_team_total ?></td><td><?= $comment_total - $comment_team_total?></td>

<td><?= 0==$comment_total? '-' :round(($comment_total - $comment_team_total)*100/$comment_total, 1) ?>%</td></tr>

<tr><td>Links:</td><td> <?= $link_total ?></td><td> <?= $link_team_total ?></td><td><?= $link_total - $link_team_total?></td>

<td><?= 0==$link_total? '-' :round(($link_total - $link_team_total)*100/$link_total, 1) ?>%</td></tr>

<tr><td>Extra Content:</td> <td><?= $content_total ?></td><td><?= $content_team_total ?></td><td><?= $content_total - $content_team_total?></td>

<td><?= 0==$content_total? '-' :round(($content_total - $content_team_total)*100/$content_total, 1) ?>%</td></tr>

<tr><td>Embeds:</td> <td><?= $embed_total ?></td><td><?= $embed_team_total ?></td><td><?= $embed_total - $embed_team_total?></td>

<td><?= 0==$embed_total? '-' :round(($embed_total - $embed_team_total)*100/$embed_total, 1) ?>%</td></tr>

</table>

<p><?= anchor('statistics/stats_dates', t("View data between specific dates")) ?></p>