<?php $this->load->view('layout/calendar.php'); ?>
<h1><?= t("View statistics for specific cloudscape") ?></h1>
<p><?= anchor('/admin/panel', t("Back to admin panel")) ?></p>
<div id="region1">
    
    <?=form_open($this->uri->uri_string(), array('id' => 'stats-cloudscape-form'))?>
       

<?= form_dropdown('cloudscape_id', $cloudscapes) ?>

 <label for="start_date"><?=t("Start Date e.g. !date", array('!date'=>date('j F Y', time())))?>: </label>
 <input type="text" class="date-pick" maxlength="128" name="start_date" id="start_date"  size="95" value="<?php if ($cloudscape->start_date): ?><?= date('d F Y', $cloudscape->start_date) ?><?php endif; ?>" />
 <br />
     <label for="end_date"><?=t("End Date e.g. !date", array('!date'=>date('j F Y', time())))?>: </label>

 <input type="text" class="date-pick" maxlength="128" name="end_date" id="end_date"  size="95" value="
 <?php if ($cloudscape->end_date): ?><?=  date('d F Y', $cloudscape->end_date) ?><?php endif; ?>" />
         <br />
        <input type="submit" name="submit" id="submit" value="<?= t("View Statistics") ?>" />
        <?=form_close()?>
</div>
