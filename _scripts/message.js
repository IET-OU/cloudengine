	$(function() {
	 
    //fade the information message in and out
    $('#message-info-area').fadeIn("slow", function() {
      $("#message-info-area").delay(1200).fadeOut("slow");      
    });  

    //button styles
		$( "button, input:submit", "#message-page" ).button();

    //select all/none on checkboxes
		$("#thread_all").click(function()				
		{
			var checked_status = this.checked;
			$("input[class=thread-checkbox]").each(function()
			{
				this.checked = checked_status;
			});
		});					   
    
	});
