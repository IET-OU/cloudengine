<?php $current_page = str_replace(base_url(), '', current_url()); ?> 
<div id="site-header-content">
        <div id="skip">
        <a href="#content"><?=t('Skip navigation') ?></a> </div>
        <?php if ($current_page != ''): ?>
        <a rel="home" href="<?=base_url()?>">
        <?php endif; ?>
        <img id="link-home" src="<?=base_url()?><?= $this->config->item('theme_logo') ?>" alt="<?=$this->config->item('site_name').t("home page") ?>" />
        <?php if ($current_page  != '/'): ?>
        </a>
        <?php endif; ?>
        <div id="site-nav">
            <ul <?=$this->lang->lang_tag() /*/Translators: primary navigation links, so only space for single words.*/ ?>>
                <li class="home">
                    <?php if ($current_page != '/'): ?>
                        <?=anchor('', t("Home"), array('class'=>'home')) ?>
                    <?php else: ?>
                        <?= t('Home') ?>
                    <?php endif; ?>
                </li>
                <li lang="en" class="clouds">
                    <?php if ($current_page != '/cloud/cloud_list'): ?>
                        <?=anchor('cloud/cloud_list', 'Clouds', array('class'=>'clouds')) ?>
                    <?php else: ?>
                        <?= 'Clouds' ?>
                    <?php endif; ?>
                </li>
                <li lang="en" class="cloudscapes">
                    <?php if ($current_page != '/cloudscape/cloudscape_list'): ?>
                        <?=anchor('cloudscape/cloudscape_list', 'Cloudscapes', array('class'=>'cloudscapes')) ?>
                    <?php else: ?>
                        <?= 'Cloudscapes' ?>
                    <?php endif; ?>
                </li>
                <li class="events">
                    <?php if ($current_page != '/events/events_list'): ?>
                        <?=anchor('events/events_list', t("Events"), array('class'=>'events')) ?>
                    <?php else: ?>
                        <?= t("Events") ?>
                    <?php endif; ?>
                </li>
                <li class="tags">
                    <?php if ($current_page != '/tag'): ?>
                        <?=anchor('tag',  t("Tags"), array('class'=>'tags')) ?>
                     <?php else: ?>
                        <?= t("Tags") ?>
                    <?php endif; ?>               
                </li>
                <li class="messages">
                    <?php if ($current_page != '/message'): ?>
                        <?=anchor('message',  t("Messages"), array('class'=>'messages')) ?>
                     <?php else: ?>
                        <?= t("Messages") ?>
                    <?php endif; ?>               
                </li>                  
                <li class="people">
                    <?php if ($current_page != '/user/people'): ?>
                        <?=anchor('user/people', t("People"), array('class'=>'people')) ?>
                    <?php else: ?>
                        <?=  t("People")?>
                    <?php endif; ?>
                </li>
                <li class="support">
                    <?php if ($current_page != 'support'): ?>
                        <?=anchor('support',t("Support"), array('class'=>'support', 'title'=>t('Support'))) ?>
                    <?php else: ?>
                        <?= t('Support') ?>
                    <?php endif; ?>
                </li>                  
              
            </ul>
        </div>
    </div>