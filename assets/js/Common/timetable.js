$(document).ready(function() {
    "use strict";

    function shuffle(array) {
        for (var i = array.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var temp = array[i];
            array[i] = array[j];
            array[j] = temp;
        }
    }

    var colors = [
        'rgba(244, 67, 54, 0.7)',
        'rgba(233, 30, 99, 0.7)',
        'rgba(156, 39, 176, 0.7)',
        'rgba(103, 58, 183, 0.7)',
        'rgba(63, 81, 181, 0.7)',
        'rgba(33, 150, 243, 0.7)',
        'rgba(3, 169, 244, 0.7)',
        'rgba(0, 188, 212, 0.7)',
        'rgba(0, 150, 136, 0.7)',
        'rgba(76, 175, 80, 0.7)',
        'rgba(139, 195, 74, 0.7)',
        'rgba(205, 220, 57, 0.7)',
        'rgba(255, 235, 59, 0.7)',
        'rgba(255, 193, 7, 0.7)',
        'rgba(255, 152, 0, 0.7)',
        'rgba(255, 87, 34, 0.7)'
    ];
    shuffle(colors);

    var subject = {};
    var i = 0;

    $('.events > div:not(.fill)').each(function() {
        var subjectName = $(this).find('h5').text().split(" ", 2).splice(0, 1);

        if (!subject[subjectName]) {
            subject[subjectName] = colors[i++];
        }
        this.style.backgroundColor = subject[subjectName];

    });
});