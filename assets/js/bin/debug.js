$(function() {
    "use strict";

    var debug = $('#debug');
    $(window).on('keypress', function(ev) {
        if (ev.keyCode === 100 || ev.keyCode === 68) {
            //D | d
            debug.toggle();
        }
    });
});