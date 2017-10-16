$(function() {
    "use strict";

    /* General initialization */
    $('select').material_select();

    // Header
    $(".button-collapse").sideNav({
        draggable: true
    });

    $("#nav-user-button").dropdown({
        constrainWidth: false,
        belowOrigin: true
    });

    $('#m-nav-user-button').dropdown({
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
