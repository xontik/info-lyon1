$(document).ready(function() {
    'use strict';

    var carousel = $('.carousel');

    carousel.carousel({
        fullWidth: true
    });

    $('#prev-slide').click(function () {
        carousel.carousel('prev');
    });

    $('#next-slide').click(function() {
        carousel.carousel('next');
    });
});
