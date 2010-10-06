<ul class="cloudstream-filter">
<li><?php if($type ==''): ?><strong><?=t("Show all")?></strong><?php else: ?><?=anchor($default_cloudscape->cloudscape_id.'/#cloudstream', t("Show all"), array('title'=>t('Show all activity'))); endif; ?></li>
<li><?php if($type =='cloud'): ?><strong>Clouds</strong><?php else: ?><?=anchor($default_cloudscape->cloudscape_id.'/cloud#cloudstream', 'Clouds', array('title'=>t('Clouds Activity'))); endif; ?></li>
<li><?php if($type =='cloudscape'): ?><strong>Cloudscapes</strong><?php else: ?><?=anchor($default_cloudscape->cloudscape_id.'/cloudscape#cloudstream', 'Cloudscapes', array('title'=>t('Cloudscapes Activity'))); endif; ?></li>
<li><?php if($type =='comment'): ?><strong><?=t("Discussion")?></strong><?php else: ?><?=anchor($default_cloudscape->cloudscape_id.'/comment#cloudstream', t("Discussion"), array('title'=>t('Discussion Activity'))); endif; ?></li>
<li><?php if($type =='link'): ?><strong><?=t("Links")?></strong><?php else: ?><?=anchor($default_cloudscape->cloudscape_id.'/link#cloudstream', t("Links"), array('title'=>t('Links Activity'))); endif; ?></li>
<li><?php if($type =='reference'): ?><strong><?=t("References")?></strong><?php else: ?><?=anchor($default_cloudscape->cloudscape_id.'/reference#cloudstream', t("References"), array('title'=>t('Links Activity'))); endif; ?></li>
<li><?php if($type =='content'): ?><strong><?=t("Extra content")?></strong><?php else: ?><?=anchor($default_cloudscape->cloudscape_id.'/content#cloudstream', t("Extra content"), array('title'=>t('Extra Content Activity'))); endif; ?></li>
</ul>

