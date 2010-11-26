<font style="font: normal 0.8em Helvetica">
<p><?=t('Dear !fullname,', array('!fullname'=>$recipient->fullname)) ?></p>
<p><?=t('You have received a new message on !site-name! from !person titled "!subject".',
    array('!person'=>$thread->author->fullname, '!subject'=>$thread->subject)) ?></p>
<p><?=t('Once logged in you can view the new message by following this link: !link',
    array('!link' => anchor('message/thread/'.$message->thread_id, site_url('message/thread/' .$message->thread_id)) )) ?></p>
<p><small><?=t('You can turn off these e-mails at !link',
    array('!link' => anchor('user/preferences', site_url('user/preferences')) )) ?>
</small></p>
</font> 