$(document).ready(function() {
    'use strict';

    var debug = $('#debug');
    var toolbar = $('#debug-toolbar');

    $(window).on('keydown', function(e) {
        if (e.keyCode === 68 || e.keyCode === 100) { // D / d
            debug.toggle();
        }
        else if (e.keyCode === 84 || e.keyCode === 116) { // T / t
            toolbar.toggle();
        }
    });
});
