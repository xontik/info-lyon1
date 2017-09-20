$(function() {

    var tdAbsences = $('td[class^="abs-"]');
    var selected = [];

    tdAbsences.click(function() {
        if (selected.indexOf(this) > -1) {
            var index = selected.indexOf(this);
            selected.splice(index, 1);
            $(this).children('div').hide();
        } else {
            selected.push(this);
            $(this).children('div').show();
        }
    });

    tdAbsences.hover(function() {
        $(this).children('div').show();
    },
    function () {
        if (selected.indexOf(this) === -1) {
            $(this).children('div').hide();
        }
    });
});
