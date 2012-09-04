<!DOCTYPE html><html <?=$this->lang->lang_tag()?>><head>
<base target="_blank" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=config_item("charset") ?>" />
<?=$this->lang->content_lang() ?>
    <title><?= $title ?> - <?= $this->config->item('site_name') ?></title>
    <link rel="stylesheet" href="<?=base_url() ?>_design/styles_1_1.css" type="text/css" />
    <?php if(config_item('theme_stylesheet')): ?>
    <link rel="stylesheet" href="<?=base_url().config_item('theme_stylesheet') ?>" type="text/css" />
    <?php endif; ?>
    <!--[if IE 6]>
    <style type="text/css">
    div#site {width:940px; /* Because IE6 doesn't support max-width */ }
    div#site-header-content {width:940px;}
    </style>
    <![endif]-->
    <script type="text/javascript" src="<?=base_url()?>_scripts/jquery/js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('a').attr('title', '<?= t('Opens in new window') ?>');
        });
    </script>
</head>
<body id="<?= $navigation ?>">
<div id="region1">
    <div id="stripped">

        <h1><?=$cloud->title ?></h1>
        <p><?= t("[link-cloud]View this cloud on !site_name [/link]", 
        array('!site_name'=>$this->config->item('site_name'),
                '[link-cloud]'=>t_link('cloud/view/'.$cloud->id) 
        )); ?></p>

        

        <?php if ($cloud->event_date): ?>
            <?=format_date(_("!date"), $cloud->event_date) ?><br /><br />
        <?php endif; ?>  
        <?php if ($cloud->call_deadline): ?>
            <?=format_date(_("Deadline: !date"), $cloud->call_deadline) ?><br /><br />
        <?php endif; ?>
        
        <?php if($cloud->summary): ?>
            <p><?=$cloud->summary ?></p>
        <?php endif; ?>
        
        <?php if ($cloud->primary_url): ?>
            <div class="box" style="margin:30px 0 10px 0">
            
                <a href="<?= $cloud->primary_url ?>"><?= $cloud->primary_url ?></a>
            </div>
        <? endif; ?>
        <p><?=t("Created by: !person", array('!person'=>$cloud->fullname)); ?>
        </p>




    <div class="user-entry">

        <?=$cloud->body?>
    </div> 

    <?php $this->load->view('content/content_block.php'); ?>
    <?php $this->load->view('embed/embed_block.php'); ?>
    

    <div class="grid">
        <h2><?= t("Contribute") ?></h2>
        <a name="contribute"></a> 
        <ul class="cloudstream-filter">
            <li>
            <?php if ($view == 'comments'): ?>
                <strong><?= t("Discussion") ?> (<?= count($comments) ?>)</strong>
            <?php else: ?>
                <?= anchor('cloud/view/'.$cloud->cloud_id.'/comments#contribute', 
                       t("Discussion").' ('.count($comments).')')?>
            <?php endif; ?>
            </li>
            <li>            
            <?php if ($view == 'links'): ?>
                <strong><?= t("Links") ?> (<?= count($links) ?>)</strong>
            <?php else: ?>
                <?= anchor('cloud/view/'.$cloud->cloud_id.'/links#contribute', 
                       t("Links").' ('.count($links).')')?>
            <?php endif; ?>
            </li>
            <li>            <?php if ($view == 'references'): ?>
                <strong><?= t("Academic References") ?> (<?= count($references) ?>)</strong>
            <?php else: ?>
                <?= anchor('cloud/view/'.$cloud->cloud_id.'/references#contribute', 
                       t("Academic References").' ('.count($references).')')?>
            <?php endif; ?></li>
        </ul>
    </div>

    <?php 
    switch ($view) {
        case 'comments'   : $this->load->view('cloud_comment/cloud_comments.php'); break;
        case 'links'      : $this->load->view('link/link_block.php'); break;
        case 'references' : $this->load->view('reference/references_block.php'); break; 
        default           : $this->load->view('cloud_comment/cloud_comments.php');
    } ?>
        <?php $this->load->view('layout/google_analytics.php') ?>
</div>
</div>   
   </body>
</html>


