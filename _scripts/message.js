	$(function() {
		$( "button, input:submit", "#message-page" ).button();
    

			$("#thread_all").click(function()				
			{
				var checked_status = this.checked;
				$("input[class=thread-checkbox]").each(function()
				{
					this.checked = checked_status;
				});
			});					   
    
	});
