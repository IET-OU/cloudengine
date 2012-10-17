{
  "recipient": "<?= $recipient ?>",
  "salt": "<?= $salt ?>",
  "evidence": "<?= $application->evidence_URL ?>",
  "badge": {
    "version": "0.5.0",
    "name": "<?= $application->name ?  $application->name : $this->config->item('site_name') ?>",
    "image": "http://<?= base_url() ?>image/badge/<?= $application->badge_id ?>",
    "description": "<?= $application->description ?>",
    "criteria": "http://<?= base_url() ?>badge/view/<?= $application->badge_id ?>",
    "issuer": {
      "origin": "<?= $badge_issuer_origin ?>",
      "name": "<?= $issuer_name ?>"
   }
  }
}