<font style="font: normal 0.8em Helvetica">
<p><?=t('Dear !fullname,', array('!fullname'=>$recipient->fullname)) ?></p>
<p><?=t('You have received a new message on !site-link! from !person as a reply in the following conversation "!subject".',
    array('!person'=>$message->author->fullname, '!subject'=>$message->thread_subject)) ?></p>
<p><?=t('Once logged in you can view the conversation by following this link: !link',
    array('!link' => anchor('message/thread/'.$message->thread_id, site_url('message/thread/' .$message->thread_id)) )) ?></p>
<p><small><?=t('You can turn off these e-mails at !link',
    array('!link' => anchor('user/preferences', site_url('user/preferences')) )) ?>
</small></p>
</font> 