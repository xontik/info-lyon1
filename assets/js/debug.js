$(document).ready(function() {
    "use strict";

    var debug = $('#debug');
    var toolbar = $('#debug-toolbar');

    $(window).on('keypress', function(ev) {
        if (ev.keyCode === 68 || ev.keyCode === 100) { // D / d
            debug.toggle();
        }
        else if (ev.keyCode === 84 || ev.keyCode === 116) { // T / t
            toolbar.toggle();
        }
    });
});
