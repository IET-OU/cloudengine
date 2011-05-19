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
    var number_of_pages = $("#cloud-results ul.simplePagerNav li.search-page-number").length;       
    //alert('x' + number_of_pages);    
    reset_search_pagination(number_of_pages)  
    return false;
  });

  $("#cloudscape-section").click(function() {
    $("#cloud-results").hide();
    $("#cloud-section").css({'background-color' : '#BED9EC'});    
    $("#cloudscape-results").show();
    $("#cloudscape-section").css({'background-color' : '#D8EBC4'}); 
    $("#user-results").hide();    
    $("#user-section").css({'background-color' : '#BED9EC'});     
    var number_of_pages = $("#cloudscape-results ul.simplePagerNav li.search-page-number").length;   
    //alert('x' + number_of_pages);
    reset_search_pagination(number_of_pages)       
    return false;
  });
  
  $("#user-section").click(function() {
    $("#cloud-results").hide();
    $("#cloud-section").css({'background-color' : '#BED9EC'});    
    $("#cloudscape-results").hide();
    $("#cloudscape-section").css({'background-color' : '#BED9EC'});    
    $("#user-results").show();    
    $("#user-section").css({'background-color' : '#D8EBC4'});  
    var number_of_pages = $("#user-results ul.simplePagerNav li.search-page-number").length;         
    reset_search_pagination(number_of_pages) 
    return false;
  });  
  
  function reset_search_pagination(number_of_pages) {
    //$("ol.paging").quickPager(1,number_of_pages);   
    /*$(".simplePagerNav").remove();
    $("ol.paging").quickPager(null,true,number_of_pages);*/ 
    
    //$("ol").attr('start',1);   
    $("li.currentPage").removeClass('currentPage');    
    $("ul.simplePagerNav li:nth-child(2)").addClass('currentPage');
    $("ul.simplePagerNav li:nth-child(2) a").trigger('click');
    //$("ol.paging").buildPager(1,number_of_pages); 
  }  
  
  $("ol.paging").quickPager();    
  //$("#cloud-section").trigger('click');  
 
  $(".show-all-results").click(function() {
    $('ol.paging').children().css('display' , 'list-item');     
    $("ol").attr('start',1);       
    return false;
  });  
    
  $(".search-page-number a").click(function() {  
    var page_limit = 15;
    var list_start = ($(this).text() * page_limit - (page_limit - 1)); 
    $("ol").attr('start',list_start);       
  });    
    
 });