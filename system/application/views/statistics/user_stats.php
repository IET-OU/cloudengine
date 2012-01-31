<h1>User Statistics</h1>
<p><?= anchor('/admin/panel', t("Back to admin panel")) ?></p>
<p><strong>Total Users:</strong> <?= $user_total ?></p>
<p><?= t("A contribution is the creation of a cloud, cloudscape, comment, extra content, link or embed.") ?>
<h3><?= t("Number of users who have made given number of contributions over site lifetime") ?></h3>
<table>
<tr>
<th scope="col">0</th>
<th scope="col">1-5</th>
<th scope="col">5-9</th>
<th scope="col">10-49</th>
<th scope="col">50+</th></tr>
<tr>
<td><?=$contrib['0']?></td>
<td><?=$contrib['1-5']?></td>
<td><?=$contrib['5-9']?></td>
<td><?=$contrib['10-49']?></td>
<td><?=$contrib['50+']?></td> </tr>
</table>
<h3><?= t("Number of users who have made given number of contributions in last 60 days") ?></h3>
<table>
<tr>
<th scope="col">0</th>
<th scope="col">1-5</th>
<th scope="col">5-9</th>
<th scope="col">10-49</th>
<th scope="col">50+</th></tr>
<tr>
<td><?=$contrib_last_60_days['0']?></td> 
<td><?=$contrib_last_60_days['1-5']?></td> 
<td><?=$contrib_last_60_days['5-9']?></td> 
<td><?=$contrib_last_60_days['10-49']?></td> 
<td><?=$contrib_last_60_days['50+']?></td> 
</table>
<h3><?= t("Number of users who have made given number of contributions over 30 days after registration") ?></h3>
<table>
<tr>
<th scope="col">0</th>
<th scope="col">1-5</th>
<th scope="col">5-9</th>
<th scope="col">10-49</th>
<th scope="col">50+</th></tr>
<tr>
<td><?=$contrib_month_after_registration['0']?></td> 
<td><?=$contrib_month_after_registration['1-5']?></td> 
<td><?=$contrib_month_after_registration['5-9']?></td> 
<td><?=$contrib_month_after_registration['10-49']?></td> 
<td><?=$contrib_month_after_registration['50+']?></td> 
</table>
<h3><?= t("Number of users who have made given number of contributions over 365 days after registration") ?></h3>
<table>
<tr>
<th scope="col">0</th>
<th scope="col">1-5</th>
<th scope="col">5-9</th>
<th scope="col">10-49</th>
<th scope="col">50+</th></tr>
<tr>
<td><?=$contrib_year_after_registration['0']?></td> 
<td><?=$contrib_year_after_registration['1-5']?></td> 
<td><?=$contrib_year_after_registration['5-9']?></td> 
<td><?=$contrib_year_after_registration['10-49']?></td> 
<td><?=$contrib_year_after_registration['50+']?></td> 
</table>
