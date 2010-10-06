<div id="region1">
    <h1><?=t("Add Google Gadget to the cloud !title", 
        array('!title'=> anchor('cloud/view/'.$cloud->cloud_id, $cloud->title)))?></h1>
   
    <?php $this->load->view('gadget/add_form'); ?>
</div>
<div id="region2">
    <div class="box">
        <p><?= t("You can also [link-add-to-user]add a gadget to all your clouds[/link] and [link-manage]manage the gadgets[/link] that you have added to all your clouds.", 
        array('[link-add-to-user]' =>t_link('gadget/add_to_user/'), '[link-manage]' => t_link('gadget/manage'))) ?></p>
    </div>
</div>