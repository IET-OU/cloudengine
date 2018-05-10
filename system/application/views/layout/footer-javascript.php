
<script
  data-nofollow-count="<?= Nofollow::get_count() ?>"
  data-rtt=<?= json_encode(str_rot13(config_item('egg'))) ?>
  data-ga-analytics-id=<?= json_encode(config_item('google_analytics')) ?>
></script>


<?php if (! isset($no_javascript)): ?>

<?php /*
	<script src="<?=base_url()?>_scripts/jquery/js/jquery-1.4.2.min.js"></script>
*/ ?>
    <?php if (config_item( 'x_message' )): ?>
        <script src="<?=base_url()?>_scripts/jquery/js/jquery-ui-1.8.6.custom.min.js"></script>
        <link href="<?=base_url()?>_scripts/jquery/css/redmond/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
    <?php endif; ?>
    <?php if (config_item( 'gaad_widget' )): ?>
        <script src="https://unpkg.com/gaad-widget@^3/dist/gaad-widget.min.js"
          data-gaad-widget='<?=json_encode(config_item( 'gaad_widget' ))?>'></script>
    <?php endif; ?>
    <?php if ($this->uri->segment(1) === 'search'): ?>
      <script src="<?=base_url()?>_scripts/buildpager.jquery.js"></script>
      <script src="<?=base_url()?>_scripts/search.js"></script>
    <?php endif; ?>
  <script src="<?=base_url()?>_scripts/custom.js"></script>
<?php endif; ?>

<script> console.warn('rel=nofollow count:', <?= Nofollow::get_count() ?>) </script>
