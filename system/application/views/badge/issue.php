
<p id="badge-issue-stat" class="loading" >Issuing badge. Loading...</p>

<script src="http://beta.openbadges.org/issuer.js"></script>
<script>
(function () {
  var p = document.querySelector("#badge-issue-stat");
  try {
    OpenBadges.issue(['<?= base_url() ?>/badge/assertion/<?= $application_id ?>'], 
    function (errors, successes) { 
      if (errors.length > 0) {
        p.innerHTML = "Error, badge not issued.  <small>[ " + errors[0].reason + " ]</small>";
        p.className = "error";
      } else {
        p.innerHTML = "Success, badge issued OK.";
        p.className = "ok";
      }
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
