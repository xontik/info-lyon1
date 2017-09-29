$(function() {

    var DEFAULT_ANIM_TIME = 100;

    function formatDate(date) {
        var monthNames = [
            "Janvier", "Février", "Mars",
            "Avril", "Mai", "Juin", "Juillet",
            "Août", "Septembre", "Octobre",
            "Novembre", "Décembre"
        ];

        var day = date.getDate();
        var monthIndex = date.getMonth();
        var year = date.getFullYear();

        return day + ' ' + monthNames[monthIndex] + ' ' + year;
    }

    function getNameFromRow(row) {
        return $('#table_stud_list').children('div')
            .eq(row)
            .children('p')
            .text();
    }

    function getDateFromColumn(col) {
        var date = new Date(FIRST_DATE.getTime()); //clone
        date.setDate(date.getDate() + col);
        return date;
    }

    var $absence_table = $('#absences_table');

    // Center #active_day
    $absence_table.find('.wrapper').each( function() {
        var active_day = $('#active_day');
        if (active_day.length) {
            this.scrollLeft = active_day.position().left - (window.innerWidth / 2);
        }
    });

    // ### Per day absence informations
    var selected = [];

    $absence_table.on('click', 'td[class^="abs-"]', function() {
        if (selected.indexOf(this) > -1) {
            var index = selected.indexOf(this);
            selected.splice(index, 1);
            $(this).children('div').hide(DEFAULT_ANIM_TIME);
        } else {
            selected.push(this);
            $(this).children('div').show(DEFAULT_ANIM_TIME);
        }
    })
    .on('mouseenter', 'td[class^="abs-"]', function() {
        $(this).children('div').show(DEFAULT_ANIM_TIME);
    })
    .on('mouseleave', 'td[class^="abs-"]', function() {
        if (selected.indexOf(this) === -1) {
            $(this).children('div').hide(DEFAULT_ANIM_TIME);
        }
    });

    // ### Per day absence creation
    var newAbsence = {
        wrapper: $('#new-absences-wrapper'),
        content: $('#new-absences'),
        name: $('#new-absences-name'),
        date: $('#new-absences-date'),
        ok_button: $('#new-absences-submit'),
        cancel_button: $('#new-absences-cancel'),

        beginTime: $(this.content).find('#add-beginTime'),
        endTime: $(this.content).find('#add-endTime'),
        justified: $(this.content).find('#add-justified'),
        absenceType: $(this.content).find('#add-absenceType'),

        prepare: function(name, date) {
            this.name.text(name);
            this.date.text(date);
            this.beginTime.val('');
            this.endTime.val('');
            this.justified.checked = false;
            this.absenceType.value = 0;
        },

        show: function() {
            //TODO Add animation
            newAbsence.wrapper.addClass('active');
        },

        hide: function() {
            //TODO Add animation
            this.wrapper.removeClass('active');
        }
    };

    $absence_table.find('tbody').on('click', 'td:not([class^="abs-"])', function() {
        newAbsence.prepare(
            getNameFromRow(this.parentNode.rowIndex - 2),
            formatDate(getDateFromColumn(this.cellIndex))
        );
        newAbsence.show();
    });

    newAbsence.ok_button.click(function() {
        // Checks
        // $.post
        // add div to td ("active" class to td ?)
    });

    newAbsence.cancel_button.click(function() {
        newAbsence.hide();
    });

    // ### Student absence count
    $('#table_stud_list').on('click', 'p + img', function() {
        $(this).siblings('div').toggle(DEFAULT_ANIM_TIME);
    });

    // ### Thead fixation
    var $fixedHeader = $('#header-fixed').append($absence_table.find('thead').clone());

    var theadTopPosition = $absence_table.find('thead').position().top;
    $(window).on('scroll', function() {
        var offset = $(this).scrollTop();
        if (offset >= theadTopPosition && $fixedHeader.is(':hidden')) {
            $fixedHeader.show();
        } else if (offset < theadTopPosition) {
            $fixedHeader.hide();
        }
    });
    // Simulate scroll so fixed thead appears if page is scrolled down enough at loading
    $(window).scroll();

    // Make thead follow horizontal scroll of the body
    var table_static_width = $('#table_static').outerWidth(true);
    $('#table-wrapper').on('scroll', function() {
        $fixedHeader.css('left', -this.scrollLeft + table_static_width);
    });
});
