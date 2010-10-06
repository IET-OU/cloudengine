<h1>Delete reference from the cloud 
<?= anchor('cloud/view/'.$cloud->cloud_id, $cloud->title) ?></h1>

<p>Are you sure that you want to delete the following reference? Deleting a reference removes it permanently and cannot be undone.</p>
<p><?= $reference->reference_text ?> </p>

<?=form_open($this->uri->uri_string(), array('id' => 'reference-delete-form'))?>

 <input type="submit" name="submit" id="submit" value="Delete Reference" />
   
<?php form_close(); ?>
<br />