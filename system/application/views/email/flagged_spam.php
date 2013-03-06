<font style="font: normal 0.8em Helvetica">

<p><?= anchor($url, t('This ').$flagged->item_type) ?> has been flagged by 
<?= anchor('user/view/'.$user->id, $user->fullname) ?> 
as spam.</p>
</font>