$(function() {

    "use strict";

    Array.prototype.maxUnder = function(value) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] >= value)
                return this[Math.min(i-1, 0)];
        }
        return this[this.length-1];
    };

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
        return $('#table-stud-list').children('div')
            .eq(row)
            .children('p')
            .text();
    }

    function getDateFromColumn(col) {
        var date = new Date(FIRST_DATE.getTime()); //clone
        date.setDate(date.getDate() + col);
        return date;
    }

    function getAbsenceTypeValue(absenceName) {
        var options = document.getElementById('add-absenceType').children;
        for (var i = 0; i < options.length; i++) {
            if (options[i].text === absenceName) {
                return options[i].value;
            }
        }
        return -1;
    }

    var DEFAULT_ANIM_TIME = 100;
    var ACTIVE_DAY_COL = $('#active-day').prop('cellIndex');

    var $absenceTable = $('#absences-table');


    /* ##### Center #active-day ##### */

    $('#table-wrapper').each( function() {
        var activeDay = $('#active-day');
        if (activeDay.length) {
            this.scrollLeft = activeDay.position().left - (window.innerWidth / 2);
        }
    });


    /* ##### Per day absence informations #####*/

    var selected = [];
    var mousedOver = null;

    $absenceTable.on('click', 'td.abs', function() {
        if (selected.indexOf(this) > -1) {
            var index = selected.indexOf(this);
            selected.splice(index, 1);
            $(this).children(mousedOver === this ? 'button' : '').hide(DEFAULT_ANIM_TIME);
        } else {
            selected.push(this);
            $(this).children().show(DEFAULT_ANIM_TIME);
        }
    })
    .on('click', 'td.abs > div', function() {
        var td = this.parentNode;
        newAbsence.edit(td, $(td).children('div').index(this));
        return false;
    })
    .on('click', 'td.abs > button', function() {
        newAbsence.new(this.parentNode.parentNode.rowIndex - 2, this.parentNode.cellIndex);
        return false;
    })
    .on('mouseenter', 'td.abs', function() {
        mousedOver = this;
        $(this).children('div').show(DEFAULT_ANIM_TIME);
    })
    .on('mouseleave', 'td.abs', function() {
        mousedOver = null;
        if (selected.indexOf(this) === -1) {
            $(this).children().hide(DEFAULT_ANIM_TIME);
        }
    });


    /* ##### Per day absence creation ##### */

    var newAbsence = {
        wrapper: $('#new-absences-wrapper'),
        content: $('#new-absences'),
        name: $('#new-absences-name'),
        date: $('#new-absences-date'),
        okButton: $('#new-absences-submit'),
        cancelButton: $('#new-absences-cancel'),

        beginTime: $('#add-beginTime'),
        endTime: $('#add-endTime'),
        justified: $('#add-justified'),
        absenceType: $('#add-absenceType'),

        new: function(row, col) {
            var beginTime = '08:00',
                endTime = '10:00';

            if (col === ACTIVE_DAY_COL) {

                var formatTime = function(time) {
                    return (time < 10 ? '0' : '') + Math.floor(time) + ':'
                        + (time % 1 * 60 < 10 ? '0' : '') + (time % 1 * 60).toString();
                };

                var now = new Date();
                var lessonsStartTime = [8, 10].concat(
                    now.getDay() !== 5
                        ? [14, 16]
                        : [13.5, 15.5]
                );

                var time = lessonsStartTime.maxUnder(now.getHours() + now.getMinutes()/60);

                beginTime = formatTime(time);
                time += 2;
                endTime = formatTime(time);
            }

            this.prepare(
                getNameFromRow(row),
                formatDate(getDateFromColumn(col)),
                beginTime,
                endTime
            );
            this.show();
        },

        edit: function(td, abs) {
            var absence = $(td).children().eq(abs).children('p');
            var beginTime = absence[0].textContent.trim().substr(-13, 5),
                endTime = absence[0].textContent.trim().substr(-5, 5),
                justified = absence[1].textContent.trim().substr(-3, 3) === 'Oui',
                absenceType = getAbsenceTypeValue(absence[2].textContent.trim());

            this.prepare(
                getNameFromRow(td.parentNode.rowIndex),
                formatDate(getDateFromColumn(td.cellIndex)),
                beginTime,
                endTime,
                justified,
                absenceType
            );
            this.show();
        },

        prepare: function(name, date, beginTime, endTime, justified, absenceTypeValue) {
            this.name.text(name);
            this.date.text(date);
            this.beginTime.val(beginTime || '');
            this.endTime.val(endTime || '');
            this.justified.checked = justified || false;
            this.absenceType.val(absenceTypeValue || '0');
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

    $absenceTable.find('tbody').on('click', 'td:not(.abs)', function() {
        newAbsence.new(this.parentNode.rowIndex - 2, this.cellIndex);
        newAbsence.show();
    });

    newAbsence.okButton.click(function() {
        // Checks

        // $.post
        // add div to td ("active" class to td ?)
    });

    newAbsence.cancelButton.click(function() {
        newAbsence.hide();
    });


    /* ##### Student absence count ##### */

    $('#table-stud-list').on('click', 'p + img', function() {
        $(this).siblings('div').toggle(DEFAULT_ANIM_TIME);
    });

    // ### Thead fixation
    var $absenceTableHead = $('#absences-table-head');
    var $fixedHeader = $('#header-fixed').append($absenceTableHead.clone());

    var theadTopPosition = $absenceTableHead.position().top;
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
    var tableStaticWidth = $('#table-static').outerWidth(true);
    $('#table-wrapper').on('scroll', function() {
        $fixedHeader.css('left', -this.scrollLeft + tableStaticWidth);
    });
});
