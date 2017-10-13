$(function() {
    "use strict";

    $(".button-collapse").sideNav();
    $("#nav-user-button").dropdown({
        constrainWidth: false,
        belowOrigin: true
    });

    $('#notifications').click(function() {
        var children = $(this).children();
        children.first().remove();

        if (children.length <= 1) {
            $(this).remove();
        }
    });
});