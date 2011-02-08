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

    $(".thread-list-row .cell-click").click( function() { //Was:.row-click.
	  var parent = $(this).parent();
	  var href = jQuery('.subject  a', parent).attr('href');
      $(window.location).attr('href', href);
    });

    $(".thread-list-row").hover( 
      function() { //Was:.row-click
        $(this).children().css({'cursor' : 'pointer', 'background-color' : '#D2E3F3'});
        $(this).children(':first-child').css({'border-radius' : '10px 0 0 10px', '-webkit-border-radius' : '10px 0 0 10px', '-moz-border-radius' : '10px 0 0 10px'});   
        $(this).children(':last-child').css({'border-radius' : '0 10px 10px 0', '-webkit-border-radius' : '0 10px 10px 0', '-moz-border-radius' : '0 10px 10px 0'});        
      }, 
      function() {
        $(this).children().css({'cursor' : 'default','background-color' : ''});
      } 
    );     

    $("button.delete-cross").hover( function() {
      $(this).css({'cursor' : 'pointer', 'background-color' : '#D63333'});
      }, function() {$(this).css({'background-color' : ''});} 
    );

	});
