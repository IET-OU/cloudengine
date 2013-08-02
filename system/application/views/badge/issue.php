
<p id="badge-issue-stat" class="loading" >Issuing badge. Loading...</p>

<script src="http://beta.openbadges.org/issuer.js"></script>
<script>
(function () {
  'use strict';

  var p = document.querySelector("#badge-issue-stat"),
    // https://github.com/mozilla/openbadges/wiki/Issuer-API#error-constant-strings
    ers = {
      "DENIED":  "User cancelled",
      "EXISTS":  "Badge already exists",
      "INACCESSIBLE": "Assertion URL can not be retrieved. Maybe 404 'Not Found'",
      "MALFORMED": "Assertion URL is malformed",
      "INVALID": "Assertion URL is not valid. Maybe not logged in"
    };

  try {
    OpenBadges.issue(['<?= site_url('badge/assertion/' . $application_id) ?>'],
    function (errors, successes) {
      var msg, cls, reason, rsn_tx;

      if (errors.length > 0) {
        reason = errors[0].reason;
        rsn_tx = ers[reason];
        if ("DENIED" === reason) {
          msg = "Warning, badge not issued. <small>[ " + rsn_tx +" - "+ reason + " ]</small>";
          cls = "warn";
        } else {
          msg = "Error, badge not issued. <small>[ " + rsn_tx +" - "+ reason + " ]</small>";
          cls = "error";
        }
      } else {
        msg = "Success, badge issued OK.";
        cls = "ok";
      }
      p.innerHTML = msg;
      p.className = cls;

      console.log(errors);
      console.log(successes);
    });
  } catch (ex) {
    if (typeof console === "object") {
      console.log(ex);
    }
    p.innerHTML = "Error (exception), badge not issued. <small>(Internet Explorer 8?) [" + ex.message + "]</small>";
    p.className = "error ex";
  }
})();
</script>
