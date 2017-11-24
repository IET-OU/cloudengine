/*!
  CloudEngine | ©The Open University | License: GPL-2.0.
*/

// Show hide

 jQuery(function ($) {
   'use strict';

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

 });
