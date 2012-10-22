<script src="http://beta.openbadges.org/issuer.js"></script>
<script type="text/javascript">
OpenBadges.issue(['<?= base_url() ?>/badge/assertion/<?= $application_id ?>'], 
function(errors, successes) { 
    console.log(errors); 
    console.log(successes); 
});
</script>
<p>Badge issued</p>