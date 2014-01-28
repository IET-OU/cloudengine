
<p id="badge-issue-stat" class="loading" ><?=t('Issuing badge. Loading...') ?></p>

<script src="http://beta.openbadges.org/issuer.js"></script>
<script>
(function () {
  'use strict';

  var p = document.querySelector("#badge-issue-stat"),
    // https://github.com/mozilla/openbadges/wiki/Issuer-API#error-constant-strings
    ers = {
      "DENIED":  "<?=t('User cancelled') ?>",
      "EXISTS":  "<?=t('Badge already exists') ?>",
      "INACCESSIBLE": "<?=t('Assertion URL can not be retrieved. Maybe 404 \'Not Found\'') ?>",
      "MALFORMED": "<?=t('Assertion URL is malformed') ?>",
      "INVALID": "<?=t('Assertion URL is not valid. Maybe not logged in') ?>"
    };

  try {
    OpenBadges.issue(['<?= site_url('badge/assertion/' . $application_id) ?>'],
    function (errors, successes) {
      var msg, cls, reason, rsn_tx;

      if (errors.length > 0) {
        reason = errors[0].reason;
        rsn_tx = ers[reason];
        if ("DENIED" === reason) {
          msg = "<?=t('Warning, badge not issued.') ?> <small>[ " + rsn_tx +" - "+ reason + " ]</small>";
          cls = "warn";
        } else {
          msg = "<?=t('Error, badge not issued.') ?> <small>[ " + rsn_tx +" - "+ reason + " ]</small>";
          cls = "error";
        }
      } else {
        msg = "<?=t('OK, badge issued successfully.') ?>";
        cls = "ok";
      }
      p.innerHTML = msg;
      p.className = cls;

      if (typeof console === "object") {
        console.log(errors);
        console.log(successes);
      }
    });
  } catch (ex) {
    if (typeof console === "object") {
      console.log(ex);
    }
    p.innerHTML = "<?=t('Error (exception), badge not issued.') ?> <small>(Internet Explorer 8?) [" + ex.message + "]</small>";
    p.className = "error ex";
  }
})();
</script>
