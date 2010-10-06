<?php if ($edit_permission || $gadgets_cloud || $gadgets_user): ?>
<h2><?=t("Gadgets")?></h2>
<?php endif; ?>
<?php if ($gadgets_cloud || $gadgets_user): ?>
<!-- Include the Google Friend Connect javascript library. --> 
<script type="text/javascript" src="http://sociallearn.org/resources/google/friendconnect.js"></script>
<?php $i = 0; // Counter spans both the foreach loops - it is used to create a unique id for each gadget div ?>
<!-- Display the gadgets added to this specific cloud -->
<?php if ($gadgets_cloud): ?>
    <?php foreach($gadgets_cloud as $gadget): ?>
        <h3><?= $gadget->title ?></h3>
        <!-- Define the div tag where the gadget will be inserted. --> 
        <div id="div-gadget-<?= $i ?>" style="width:280px; border:1px solid #cccccc;"></div>
        
        <!-- Render the gadget into a div. -->
        <script type="text/javascript">
        var skin = {};
        google.friendconnect.container.setNoCache(1);
        
        google.friendconnect.container.setParentUrl('/'); 
        google.friendconnect.container.renderOpenSocialGadget(
         { id: 'div-gadget-<?= $i ?>',
           url:'<?= $gadget->url ?>',
           site: '<?= $this->config->item('x_gadgets_gfc_key') ?>',
           'view-params': { "userId" : "<?= $current_user_id  ?>" ,  "cloudId" : "<?= $cloud->cloud_id ?>"}
         },
          skin);
        </script>
        <?php $i++; ?>
        <br />
        <?php if ($gadget->accessible_alternative): ?>
            <p><?= anchor('gadget/accessible_alternative/'.$gadget->gadget_id, t("View accessible alternative to this gadget")) ?></p>
        <?php endif; ?>
        <?php if ($edit_permission): ?>
            &nbsp;&nbsp;<small><?= anchor('gadget/delete_from_cloud/'.$gadget->gadget_id, 
                                  t('delete Google gadget')) ?></small>
            <br /><br />
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Display the gadgets the owner of this cloud has added to all their cloud -->
<?php if ($gadgets_user): ?>
    <?php foreach($gadgets_user as $gadget): ?>
        <h3><?= $gadget->title ?></h3>
        <!-- Define the div tag where the gadget will be inserted. --> 
        <div id="div-gadget-<?= $i ?>" style="width:280px; border:1px solid #cccccc;"></div>
        
        <!-- Render the gadget into a div. -->
        <script type="text/javascript">
        var skin = {};
        google.friendconnect.container.setNoCache(1);
        
        google.friendconnect.container.setParentUrl('/'); 
        google.friendconnect.container.renderOpenSocialGadget(
         { id: 'div-gadget-<?= $i ?>',
           url:'<?= $gadget->url ?>',
           site: '<?= $this->config->item('x_gadgets_gfc_key') ?>',
           'view-params': { "userId" : "<?= $current_user_id ?>" ,  "cloudId" : "<?= $cloud->cloud_id ?>"}
         },
          skin);
        </script>
        <?php $i++; ?>
        <br />
        <?php if ($gadget->accessible_alternative): ?>
           <p><?= anchor('gadget/accessible_alternative/'.$gadget->gadget_id, t("View accessible alternative to this gadget")) ?></p>
        <?php endif; ?>
    <?php endforeach; ?>
        <?php if ($edit_permission): ?>
            &nbsp;&nbsp;<small><?= anchor('gadget/manage/', 
                                  t('Manage Google gadgets added to all your clouds')) ?></small>
            <br /><br />
        <?php endif; ?>    
<?php endif; ?>
<?php endif; ?>
<?php if ($edit_permission): ?>
<p class="add-link"><?= anchor('gadget/add_to_cloud/'.$cloud->cloud_id, t("Add gadget")) ?></p>
<?php endif; ?>