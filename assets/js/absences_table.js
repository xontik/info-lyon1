$(function() {

    var absence_table = $('#absences_table');

    absence_table.find('.wrapper').each( function() {
        var active_day = $('#active_day');
        if (active_day.length) {
            this.scrollLeft = active_day.position().left - (window.innerWidth / 2);
        }
    });

    var selected = [];

    absence_table.on('click', 'td[class^="abs-"]', function() {
        if (selected.indexOf(this) > -1) {
            var index = selected.indexOf(this);
            selected.splice(index, 1);
            $(this).children('div').hide();
        } else {
            selected.push(this);
            $(this).children('div').show();
        }
    });

    absence_table.on('mouseenter', 'td[class^="abs-"]', function() {
        $(this).children('div').show();
    });

    absence_table.on('mouseleave', 'td[class^="abs-"]', function() {
        if (selected.indexOf(this) === -1) {
            $(this).children('div').hide();
        }
    });
});
