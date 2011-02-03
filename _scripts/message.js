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
    
    $(".row-click").click( function() {
      var id = $(this).parent().parent().parent().parent().attr('id');
      $(window.location).attr('href', '/message/thread/' + id);
    });

    $(".row-click").hover( function() {      
      $(this).parent().parent().parent().parent().css({'cursor' : 'pointer', 'background-color' : '#D2E3F3'});
      }, function() {$(this).parent().parent().parent().parent().css({'cursor' : 'default','background-color' : ''});} 
    );     
    
    $("button.delete-cross").hover( function() {      
      $(this).css({'cursor' : 'pointer', 'background-color' : '#D63333'});
      }, function() {$(this).css({'background-color' : ''});} 
    );      
    
	});
