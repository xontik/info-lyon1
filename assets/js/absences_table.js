$(function() {

    "use strict";

    Array.prototype.maxUnder = function(value) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] >= value)
                return this[Math.min(i-1, 0)];
        }
        return this[this.length-1];
    };

    var DateFormat = {
        READABLE: 1,
        SQL: 2
    };

    Date.prototype.format = function(format) {
        format = format || DateFormat.READABLE;

        switch (format) {
        case DateFormat.READABLE:
            var monthNames = [
                "Janvier", "Février", "Mars",
                "Avril", "Mai", "Juin", "Juillet",
                "Août", "Septembre", "Octobre",
                "Novembre", "Décembre"
            ];

            return this.getDate() + ' '
                + monthNames[this.getMonth()] + ' '
                + this.getFullYear();
        case DateFormat.SQL:
            return this.getUTCFullYear() + '-'
                + twoDigits(1 + this.getUTCMonth()) + '-'
                + twoDigits(this.getUTCDate()) + ' '
                + twoDigits(this.getUTCHours()) + ':'
                + twoDigits(this.getUTCMinutes()) + ':'
                + twoDigits(this.getUTCSeconds());
        default:
            return false;
        }
    };

    function twoDigits(d) {
        if(d >= 0 && d < 10) return '0' + d.toString();
        if(d >= -10 && d < 0) return '-0' + (-d).toString();
        return d.toString();
    }

    function formatTime(seconds) {
        var hours = seconds / 3600;
        return twoDigits(Math.floor(hours)) + ':'
            + twoDigits(hours % 1 * 60)
    }

    function getNameFromRow(row) {
        return $('#table-stud-list').children('div')
            .eq(row)
            .children('p')
            .text();
    }

    function getDateFromColumn(col) {
        var date = new Date(FIRST_DATE.getTime());
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

    function getAbsenceTypeName(absenceValue) {
        return document.getElementById('add-absenceType').children[absenceValue].text;
    }

    function parseTimeToSeconds(time) {
        return parseInt(time.substr(0, 2)) * 60 * 60
            + parseInt(time.substr(3));
    }

    var DEFAULT_ANIM_TIME = 100;
    var ACTIVE_DAY_COL = $('#active-day').prop('cellIndex');

    var $absenceTable = $('#absences-table');
    var $tableWrapper = $('#table-wrapper');

    /* ##### Center #active-day ##### */

    $tableWrapper.each( function() {
        var activeDay = $('#active-day');
        if (activeDay.length) {
            this.scrollLeft = activeDay.position().left - (window.innerWidth / 2);
        }
    });


    /* ##### Per day absence informations #####*/

    var selected = [];
    var mousedOver = null;
    var activeNode = null;

    $absenceTable.on('click', 'td', function() {
        if (activeNode !== null)
            activeNode.removeClass('active');
        activeNode = $(this).addClass('active');

        if ($(this).is('.abs')) {
            if (selected.indexOf(this) > -1) {
                var index = selected.indexOf(this);
                selected.splice(index, 1);
                $(this).children(mousedOver === this ? 'button' : '').hide(DEFAULT_ANIM_TIME);
            } else {
                selected.push(this);
                $(this).children().show(DEFAULT_ANIM_TIME);
            }
        } else {
            newAbsence.new(this.parentNode.rowIndex - 2, this.cellIndex);
        }
    })

    .on('click', 'td.abs > div', function() {
        if (activeNode !== null)
            activeNode.removeClass('active');
        activeNode = $(this).addClass('active');

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
        submitButton: $('#new-absences-submit'),
        cancelButton: $('#new-absences-cancel'),

        beginTime: $('#add-beginTime'),
        endTime: $('#add-endTime'),
        justified: $('#add-justified'),
        absenceType: $('#add-absenceType'),

        new: function(row, col) {
            var beginTime,
                endTime;

            if (col === ACTIVE_DAY_COL) {

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
                getDateFromColumn(col).format(DateFormat.READABLE),
                beginTime || '08:00',
                endTime || '10:00'
            );
            this.show();
        },

        edit: function(td, abs) {
            var absence = $(td).children().eq(abs).children('p');
            var beginTime = absence[0].textContent.trim().substr(-13, 5),
                endTime = absence[0].textContent.trim().substr(-5, 5),
                justified = absence[1].textContent.trim().substr(-3, 3) === 'Oui',
                absenceTypeValue = getAbsenceTypeValue(absence[2].textContent.trim());

            this.prepare(
                getNameFromRow(td.parentNode.rowIndex),
                getDateFromColumn(td.cellIndex).format(DateFormat.READABLE),
                beginTime,
                endTime,
                justified,
                absenceTypeValue
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

        send: function() {

            // ### Check for errors
            // Errors with active element
            var active = $('td.active, table div.active');
            if (active.length !== 1) {
                alert("Error : " + active.length + " actives part");
                return false;
            }

            active = active[0];

            var cell = $(active).is('td') ? active : active.parentNode;

            if (cell.children > 2) {
                return false;
            }

            var cellPosition = {
                col: cell.cellIndex,
                row: cell.parentNode.rowIndex - 2
            };

            // remove 'absn' from id
            var absenceId = active.id.substr(4);

            var studentId = $('#table-stud-list')
                .children('div')
                .eq(cellPosition.row)
                .attr('id');

            // Errors in fields
            var beginTimeVal = this.beginTime.val();
            var endTimeVal = this.endTime.val();
            var justified = this.justified.is(':checked');
            var absenceTypeValue = this.absenceType.val();

            var error = false;
            if (!beginTimeVal) {
                error = true;
                this.beginTime.addClass('form-error');
            }

            if (!endTimeVal) {
                error = true;
                this.endTime.addClass('form-error');
            }

            if (beginTimeVal === endTimeVal) {
                error = true;
                this.endTime.addClass('form-error');
            }

            if (absenceTypeValue === '0') {
                error = true;
                this.absenceType.addClass('form-error');
            }

            if (error) return false;

            // ### Parse datas
            var date = getDateFromColumn(cellPosition.col);

            var beginDate = new Date(date.getTime() + (parseTimeToSeconds(beginTimeVal) * 1000));
            var endDate = new Date(date.getTime() + (parseTimeToSeconds(endTimeVal) * 1000));

            // ### Send to server
            var url;
            var data = {
                studentId: studentId,
                beginDate: beginDate.format(DateFormat.SQL),
                endDate: endDate.format(DateFormat.SQL),
                absenceTypeId: absenceTypeValue,
                justified: justified ? '1' : '0'
            };

            if (absenceId) {
                data.absenceId = absenceId;
                url = '/Process_secretariat/modifier_absence/';
            } else {
                url = '/Process_secretariat/ajout_absence/';
            }

            $.post(url, data, function(data) {
                // If success
                if (data.match(/^success [0-9]*$/)) {
                    newAbsence.hide();

                    var $schedule;
                    var $justified;
                    var $absenceType;

                    var isNotAbsenceClass = function(element) {
                        return element === 'abs-justifiee' || element.substr(0, 4) !== 'abs-';
                    };

                    var absenceType = getAbsenceTypeName(absenceTypeValue);

                    var newClass = 'abs-' + absenceType.toLowerCase();
                    var cellClasses = cell.className.split(' ');
                    var justifiedCell = cellClasses.indexOf('abs-justifiee') !== -1;

                    if (cellClasses.indexOf('abs') === -1) {
                        cellClasses.push('abs');
                    }

                    // justification
                    if ($(cell).children('div').length === 1) {
                        var indexJustified = cellClasses.indexOf('abs-justifiee');
                        if (indexJustified !== -1) {
                            // if cell justified and current new abs is not
                            if (!justified) {
                                // remove justification
                                cellClasses.splice(indexJustified, 1);
                                justifiedCell = false;
                            }
                        }
                    } else { // cell has no div childrens
                        if (justified) {
                            cellClasses.push('abs-justifiee');
                            justifiedCell = true;
                        }
                    }

                    if (!justifiedCell) {
                        // if the new class doesn't already exists in cell
                        if (cellClasses.indexOf(newClass) === -1) {
                            // if no other absence type
                            if (cellClasses.every(isNotAbsenceClass)) {
                                // add it
                                cellClasses.push(newClass);
                            } else {
                                // remove all absences type and add 'abs-several'
                                cellClasses.filter(isNotAbsenceClass);
                                cellClasses.push('abs-several');
                            }
                        }
                        // if new class already exist in cell, change nothing
                    }

                    cell.className = cellClasses.join(' ');

                    // Create absence in DOM
                    // If new, create div
                    if (!absenceId) {
                        var div = document.createElement('div');
                        div.className = 'abs-' + absenceType.toLowerCase();
                        div.id = 'absn' + data.substr(8);

                        $schedule = div.appendChild(document.createElement('p'));
                        $justified = div.appendChild(document.createElement('p'));
                        $absenceType = div.appendChild(document.createElement('p'));

                        cell.appendChild(div);

                    } else {
                        var children = active.children;
                        $schedule = children[0];
                        $justified = children[1];
                        $absenceType = children[2];
                    }

                    $schedule.textContent = 'Horaires : ' + beginTimeVal + ' - ' + endTimeVal;
                    $justified.textContent = 'Justifiée : ' + (justified ? 'Oui' : 'Non');
                    $absenceType.textContent = absenceType;

                    // 'new' button
                    if ($(cell).children('div').length === 1) {
                        var button = document.createElement('button');
                        button.appendChild(document.createTextNode('Nouveau'));
                        cell.appendChild(button);
                    } else {
                        // 2 children, can't add more
                        $(cell).children('button').remove();
                        $(cell).children('div').show();
                    }

                } else if (data === 'cancel') {
                    alert('Erreur interne : requête annulée');
                } else if (data === 'missing_data') {
                    alert('Erreur interne : données manquantes');
                } else if (data.substring(0, 9) === 'exception') {
                    alert('Erreur interne : ' + data);
                } else {
                    var errors = data.split(',');
                    for (var error in errors) {
                        switch(error) {
                            case 'beginDate':
                                newAbsence.beginTime.addClass('form-error');
                                break;
                            case 'endDate':
                            case 'sameDates':
                                newAbsence.endTime.addClass('form-error');
                                break;
                        }
                    }
                }

            });
        },

        show: function() {
            newAbsence.wrapper.addClass('active');
        },

        hide: function() {
            this.wrapper.removeClass('active');
        }
    };

    newAbsence.submitButton.click(function() {
        newAbsence.send();
        return false;
    });

    newAbsence.cancelButton.click(function() {
        newAbsence.hide();
        return false;
    });


    var previousTimes = {};
    newAbsence.beginTime.on('focus', function() {
        previousTimes.begin = parseTimeToSeconds(newAbsence.beginTime.val());
        previousTimes.end = parseTimeToSeconds(newAbsence.endTime.val());
    })
    .on('change', function() {
        var diff = previousTimes.end - previousTimes.begin;
        newAbsence.endTime.val(formatTime(
            parseTimeToSeconds(newAbsence.beginTime.val()) + diff
        ));

        previousTimes.begin = parseTimeToSeconds(newAbsence.beginTime.val());
        previousTimes.end = parseTimeToSeconds(newAbsence.endTime.val());
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
    })
    // Simulate scroll so fixed header appears if page is scrolled down at loading
    .scroll();

    // Make thead follow horizontal scroll of the body
    var tableStaticWidth = $('#table-static').outerWidth(true);
    $tableWrapper.on('scroll', function() {
        $fixedHeader.css('left', -this.scrollLeft + tableStaticWidth);
    });
});
