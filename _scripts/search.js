/*!
  CloudEngine | ©The Open University | License: GPL-2.0.
*/

 jQuery(function ($) {
   'use strict';

  //search interface tab sections
  $("#cloud").hover(function() {
    $(this).css('cursor','pointer');
    }, function() {
    $(this).css('cursor','auto');
  });

  $("#cloudscape").hover(function() {
    $(this).css('cursor','pointer');
    }, function() {
    $(this).css('cursor','auto');
  });

  $("#user").hover(function() {
    $(this).css('cursor','pointer');
    }, function() {
    $(this).css('cursor','auto');
  });

  //******************************************************************
  // call the quickPager function on the search result list
  //******************************************************************

  //the number of results to show per page
  var pageSize = 15;

  //call the paging plugin
  $("ol.paging").quickPager(
        {pageSize:  pageSize}
  );

  //create variables for the number of pages in each tab
  var cloud_pages       = Math.ceil(($("#cloud-results ol.paging li").length) / pageSize);
  var cloudscape_pages  = Math.ceil(($("#cloudscape-results ol.paging li").length) / pageSize);
  var user_pages        = Math.ceil(($("#user-results ol.paging li").length) / pageSize);

  //when a tab is clicked, call the tab_click() functionwith the tab id
  $(".search-result-nav-section").click(function() {
    tab_click($(this).attr('id'));
  });

  //process the tab that's been clicked
  function tab_click(active_tab) {
    var inactive_tab1, inactive_tab2;

    /* jshint -W061 */
    //number of pages for the tab
    var number_of_pages = eval(active_tab + '_pages');
    /* jshint +W061 */

    //set the active and non active tab names
    switch (active_tab) {
      case 'cloud':
        inactive_tab1 = 'cloudscape';
        inactive_tab2 = 'user';
        break;
      case 'cloudscape':
        inactive_tab1 = 'cloud';
        inactive_tab2 = 'user';
        break;
      case 'user':
        inactive_tab1 = 'cloud';
        inactive_tab2 = 'cloudscape';
        break;
    }

    //hide and show the relevant results
    $("#" + inactive_tab1 + "-results").hide();
    $("#" + inactive_tab2 + "-results").hide();
    $("#" + active_tab    + "-results").show();

    //change the colours of the tabs accordingly
    $("#" + inactive_tab1).css({'background-color' : '#BED9EC'});
    $("#" + inactive_tab2).css({'background-color' : '#BED9EC'});
    $("#" + active_tab).css({'background-color' : '#D8EBC4'});

    //remove the pagination for the tab and reset it
    $("#" + active_tab + "-results ul.simplePagerNav").remove();
    reset_search_pagination(number_of_pages);
    reactivate_page_number_links();
    return false;
  }

  //trigger the click event on the active cloud tab to reset the tab
  $("#cloud").trigger('click');

  //reset the pagination with the amount of pages for this tab
  function reset_search_pagination(number_of_pages) {
    $("ol.paging").quickPager(
      {
        resetPaging:  true,
        pageCounter:  number_of_pages,
      }
    );
    //simulate a click of the first page link to reset the list
    $("ul.simplePagerNav li:nth-child(2) a").trigger('click');
  }

  //dynamically assign the start number for the list each time a
  //search page number is clicked
  function reactivate_page_number_links() {
    $(".search-page-number a").click(function() {
      var list_start = ($(this).text() * pageSize - (pageSize - 1));
      $("ol").attr('start',list_start);
    });
  }

  //if the user clicks to show all results, display the whole list
  $(".show-all-results").click(function() {
    $('ol.paging').children().css('display' , 'list-item');
    $("ol").attr('start',1);
    return false;
  });

 });
