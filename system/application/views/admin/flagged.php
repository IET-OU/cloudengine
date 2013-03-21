<h1><?= t('Items flagged as spam') ?></h1>

<?php if ($flagged): ?>
    <ul>
<?php foreach ($flagged as $item): ?>
    <li><?= t("The !item_type [link-url]!url[/link] was flagged by [link-user]!fullname[/link] on !date", 
            array('!item_type' => $item->item_type,
                '!url' => $item->url,
                    '[link-url]'=>t_link($item->url), 
                  '[link-user]'=>t_link('user/view/'.$item->user_id),
                  '!fullname' => $item->fullname,
                  '!date' => date("g:ia j F Y", $item->timestamp))
            ) ?>
<?php endforeach;?>
</ul>
<?php else:?>
    <?= t("No items flagged as spam")?>
<?php endif;?>