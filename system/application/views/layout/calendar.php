<script type="text/javascript" src="<?=base_url()?>_scripts/date.js"></script>
<script src="<?=base_url()?>_scripts/jquery.datePicker.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">
Date.firstDayOfWeek = 0;
Date.format = 'dd mmmm yyyy';

	$(function()
    {
		$('.date-pick').datePicker({startDate:'01/01/2000'})
		$('#start_date').bind(
			'dpClosed',
			function(e, selectedDates)
			{
				var d = selectedDates[0];
				if (d) {
					d = new Date(d);
					$('#end_date').dpSetStartDate(d.addDays(1).asString());
				}
			}
		);
		$('#end_date').bind(
			'dpClosed',
			function(e, selectedDates)
			{
				var d = selectedDates[0];
				if (d) {
					d = new Date(d);
					$('#start_date').dpSetEndDate(d.addDays(-1).asString());
				}
			}
		);
    });
      
</script>