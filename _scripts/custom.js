// Show hide

 $(document).ready(function() {
   // do stuff when DOM is ready
   // use this to reset a single form
   
   $("div.collapsed").addClass("hidden");   
   $("span.more").addClass("show");   
   
   $(".show").click(function() {   	  	  
	  $("div.collapsed").slideToggle('fast');
	  $('span.more').toggle();
	  return false;
   }); 
   
   //visibility of usernames and email addresses for admins on user list page
   $("table#userlist .user-info").parents('tr').hover( 
      function() {
        $(this).find('.user-info').css({'visibility' : 'visible'});
      },  
      function() {
        $(this).find('.user-info').css({'visibility' : 'hidden'});
      } 
    );   

   //visibility of usernames and email addresses for admins on user list page
   $("table#userlist a").focus( 
      function() {      
        $(this).siblings('.user-info').css({'visibility' : 'visible'});
      } 
    );   
   $("table#userlist a").blur( 
      function() {
        $(this).siblings('.user-info').css({'visibility' : 'hidden'});
      } 
    );    

  $("input[type=search]").attr('results', '5');
  
  
  //search interface show/hide sections
  
  $("#cloud-section").hover(function() {
    $(this).css('cursor','pointer');
    }, function() {
    $(this).css('cursor','auto');
  });
  
  $("#cloudscape-section").hover(function() {
    $(this).css('cursor','pointer');
    }, function() {
    $(this).css('cursor','auto');
  });
  
  $("#user-section").hover(function() {
    $(this).css('cursor','pointer');
    }, function() {
    $(this).css('cursor','auto');
  });    
  
  $("#cloud-section").click(function() {
    $("#cloud-results").show();
    $("#cloud-section").css({'background-color' : '#D8EBC4'});
    $("#cloudscape-results").hide();
    $("#cloudscape-section").css({'background-color' : '#BED9EC'});    
    $("#user-results").hide();    
    $("#user-section").css({'background-color' : '#BED9EC'});    
    return false;
  });

  $("#cloudscape-section").click(function() {
    $("#cloud-results").hide();
    $("#cloud-section").css({'background-color' : '#BED9EC'});    
    $("#cloudscape-results").show();
    $("#cloudscape-section").css({'background-color' : '#D8EBC4'}); 
    $("#user-results").hide();    
    $("#user-section").css({'background-color' : '#BED9EC'});        
    return false;
  });
  
  $("#user-section").click(function() {
    $("#cloud-results").hide();
    $("#cloud-section").css({'background-color' : '#BED9EC'});    
    $("#cloudscape-results").hide();
    $("#cloudscape-section").css({'background-color' : '#BED9EC'});    
    $("#user-results").show();    
    $("#user-section").css({'background-color' : '#D8EBC4'});  
    return false;
  });  
  
 });