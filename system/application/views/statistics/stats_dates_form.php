<h1>View statistics between dates</h1>
<p><?= anchor('/admin/panel', t("Back to admin panel")) ?></p>
<div id="region1">
    
    <?=form_open($this->uri->uri_string(), array('id' => 'stats-date-form'))?>
       
        <label for="title">Start Date:</label>
        <input type="text" maxlength="20" name="start" id="start"  size="20" value="<?php echo date("j F Y", time() - 24*60*60*30)?>" />
    
        <label for="summary">End Date: </label>
        <input type="text" maxlength="20" name="end" id="end"  size="20" value="<?php echo date("j F Y", time())?>" class="form-text" />
    
         
        <input type="submit" name="submit" id="submit" class="submit" value="View Statistics" />
        <?=form_close()?>
</div>