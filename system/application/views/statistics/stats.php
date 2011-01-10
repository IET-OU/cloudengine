<h1><?= t("Site Statistics") ?></h1>
<p><?= anchor('admin/panel', t("Back to admin panel")) ?></p>
<p><strong>Total Users:</strong> <?= $user_total ?></p>
<table width="200px">
<tr><th></th><th>Everyone</th><th>Team</th><th>Non-team</th><th>% Non-team</th></tr>
<tr><td><?=t('Clouds') ?></td> <td><?= $cloud_total ?></td><td><?= $cloud_team_total ?></td><td><?= $cloud_total - $cloud_team_total?></td>
<td><?= 0==$cloud_total? '-' :round(($cloud_total - $cloud_team_total)*100/$cloud_total, 1) ?>%</td>
</tr>

<tr><td><?=t('Cloudscapes')?></td><td><?= $cloudscape_total ?></td><td><?= $cloudscape_team_total ?></td><td><?= $cloudscape_total - $cloudscape_team_total?></td>
<td><?= 0==$cloudscape_total? '-' :round(($cloudscape_total - $cloudscape_team_total)*100/$cloudscape_total, 1) ?>%</td></tr>
<tr><td><?=t('Tags')?></td> <td><?= $tag_total ?></td><td></td><td></td>
<td>&nbsp;</td></tr>
<tr><td><?=t('Comments')?></td> <td><?= $comment_total ?></td><td><?= $comment_team_total ?></td><td><?= $comment_total - $comment_team_total?></td>
<td><?= 0==$comment_total? '-' :round(($comment_total - $comment_team_total)*100/$comment_total, 1) ?>%</td></tr>
<tr><td><?=t('Links')?></td><td> <?= $link_total ?></td><td> <?= $link_team_total ?></td><td><?= $link_total - $link_team_total?></td>
<td><?= 0==$link_total? '-' :round(($link_total - $link_team_total)*100/$link_total, 1) ?>%</td></tr>
<tr><td><?=t('Extra content')?></td> <td><?= $content_total ?></td><td><?= $content_team_total ?></td><td><?= $content_total - $content_team_total?></td>
<td><?= 0==$content_total? '-' :round(($content_total - $content_team_total)*100/$content_total, 1) ?>%</td></tr>
<tr><td><?=t('Embeds')?></td> <td><?= $embed_total ?></td><td><?= $embed_team_total ?></td><td><?= $embed_total - $embed_team_total?></td>
<td><?= 0==$embed_total? '-' :round(($embed_total - $embed_team_total)*100/$embed_total, 1) ?>%</td></tr>
</table>

<p><?= anchor('statistics/stats_dates', t("View data between specific dates")) ?></p>