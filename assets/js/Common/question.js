$(document).ready(function() {
    'use strict';

    $('.collapsible-header.active').each(function() {
        var el = $(this);
        var parent = el.parent();

        $('html, body').animate({
            scrollTop: el.offset().top - ($(window).height() - parent.height()) / 2
        });
    });
});
