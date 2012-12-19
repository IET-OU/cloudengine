/*
 * Script used on the cloud view page - if the page is in an iframe, strip
 * out part of the page and make other tweaks to make the page work better
 * in that context
 */

$(document).ready(function() {
    if (top !== self) { // If the current page is in an iframe
        // Add a link to the full page
        $('.headline').before('<p><a href="'+window.location+'">View full page on original site</a></p>');
        // Remove parts of the page that we don't want
        $('#site-header').remove();
        $('#region2').remove();
        $('#site-footer').remove();
        $('.headline-wrap .c2of2').remove();
        // Make all links open in a new window 
        $('head').append('<base target="_blank" />');    
        $('a').attr('title', 'Opens in new window');
    }
});