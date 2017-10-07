$(function() {

    "use strict";

    /**
     * Get the maximum number in a table that is less than a limit
     * @param limit The maximum number possible
     * @returns {number} The maximum value under the limit
     */
    Array.prototype.maxUnder = function(limit) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] >= limit)
                return this[Math.min(i-1, 0)];
        }
        return this[this.length-1];
    };

    var DateFormat = {
        READABLE: 1,
        SQL: 2
    };

    /**
     * Format date according to a date format
     * @param format A value of DateFormat
     * @returns {String} The formatted date
     */
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

    if (!String.prototype.startsWith) {
        String.prototype.startsWith = function (searchString, position) {
            position = position || 0;
            return this.substr(position, searchString.length) === searchString;
        };
    }

    if (!String.prototype.endsWith) {
        String.prototype.endsWith = function(searchString, position) {
            var subjectString = this.toString();
            if (typeof position !== 'number' || !isFinite(position) || Math.floor(position) !== position || position > subjectString.length) {
                position = subjectString.length;
            }
            position -= searchString.length;
            var lastIndex = subjectString.lastIndexOf(searchString, position);
            return lastIndex !== -1 && lastIndex === position;
        };
    }

    /**
     * If the integer only contains 1 digit, fill with zeros
     * @param d int In integer between -99 and 99
     * @returns {string} The number filled with zeros
     */
    function twoDigits(d) {
        if(d >= 0 && d < 10) return '0' + d.toString();
        if(d >= -10 && d < 0) return '-0' + (-d).toString();
        return d.toString();
    }

    /**
     * Format a number of seconds into a formatted string :
     * "HH:mm"
     * @param seconds The number of seconds
     * @returns {string} The time formatted string
     */
    function formatTime(seconds) {
        var hours = seconds / 3600;
        return twoDigits(Math.floor(hours)) + ':'
            + twoDigits(hours % 1 * 60)
    }

    /**
     * @param time A formatted string: "HH:mm"
     * @returns {number} The number of seconds in the time
     */
    function parseTimeToSeconds(time) {
        return parseInt(time.substr(0, 2)) * 60 * 60
            + parseInt(time.substr(3)) * 60;
    }

    /**
     * Return the name of the student corresponding to the row
     * @param row The row on the table
     * @returns {string} The name of the student
     */
    function getNameFromRow(row) {
        return $('#table-stud-list').children('div')
            .eq(row)
            .children('p')
            .text();
    }

    /**
     * Return the date corresponding to the column of the table
     * @param col The column
     * @returns {Date} The date corresponding
     */
    function getDateFromColumn(col) {
        var date = new Date(FIRST_DATE.getTime());
        date.setDate(date.getDate() + col);
        return date;
    }

    /**
     * @param absenceName The name of the asbence type
     * @returns {number} The value of the absence type, or -1
     */
    function getAbsenceTypeValue(absenceName) {
        var options = document.getElementById('add-absenceType').children;
        for (var i = 0; i < options.length; i++) {
            if (options[i].text === absenceName) {
                return parseInt(options[i].value);
            }
        }
        return -1;
    }

    /**
     * @param absenceValue The value of the absence type
     * @returns {string} The name of the absence type
     */
    function getAbsenceTypeName(absenceValue) {
        return document.getElementById('add-absenceType').children[parseInt(absenceValue)].text;
    }

    /**
     * This function takes the old classes of a cell and
     * the new absence to be added or modified and produce
     * the new classes to be set on the cell.
     * If the cell has classes that do not have relation with absence,
     * they will be kept.
     *
     * @param oldClasses The old classes of the cell
     * @param absence The absence to be added/modified
     * @returns {Array.<String>} The new classes of the cell
     */
    function filterClasses(oldClasses, absence) {
        var cellClasses = oldClasses.slice(0).filter(function(klass) {
            return !klass.startsWith("abs");
        });
        cellClasses.push("abs");

        var htmlId = "#absn" + absence.absenceId;
        var $otherChild = $(htmlId).parent()
            .children('div:not(' + htmlId + ')');

        var newClass = "abs-" + absence.absenceType.name.toLowerCase();

        // justification
        if (absence.editing) {
            // if absence justified and, if it exists, other one is too
            if (absence.justified
                && ($otherChild.length
                    ? $otherChild.find('p:nth-child(2)').text().trim().endsWith("Oui")
                    : true)
            ) {
                cellClasses.push("abs-justifiee");
            }
        } else {
            var wasAbsenceCell = oldClasses.indexOf("abs") > -1;
            var wasJustifiedCell = oldClasses.indexOf("abs-justifiee") > -1;

            // if absence is justified and, if cell had an absence, was it justified
            if (absence.justified && (wasAbsenceCell ? wasJustifiedCell : true)) {
                cellClasses.push("abs-justifiee");
            }
        }

        var notAbsenceClass = function(klass) {
            return klass === "abs-justifiee" || !klass.startsWith("abs-");
        };

        // if there was no absence class
        // or new class already existed
        // or there was several abs type but now there's one
        if (oldClasses.every(notAbsenceClass)
            || oldClasses.indexOf(newClass) > -1
            || (oldClasses.indexOf("abs-several") > -1 && $otherChild.attr('class') === newClass)
        ) {
            cellClasses.push(newClass);
        }
        else {
            cellClasses.push("abs-several");
        }


        return cellClasses;
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
                justified = absence[1].textContent.trim().endsWith('Oui'),
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

            var absence = {};
            // remove 'absn' from id
            absence.absenceId = active.id.substr(4);
            absence.editing = !!absence.absenceId;
            absence.studentId = $('#table-stud-list')
                .children('div')
                .eq(cellPosition.row)
                .attr('id');

            // Errors in fields
            var beginTimeVal = this.beginTime.val();
            var endTimeVal = this.endTime.val();
            var absenceTypeValue = this.absenceType.val();

            var error = false;
            if (!beginTimeVal) {
                error = true;
                alert("Erreur avec la date de début");
            }

            if (!endTimeVal) {
                error = true;
                alert("Erreur avec la date de fin");
            }

            if (beginTimeVal === endTimeVal) {
                error = true;
                alert("Erreur : les dates ne peuvent pas être égales ");
            }

            if (absenceTypeValue === '0') {
                error = true;
                alert("Erreur : vous n'avez pas choisi de type d'absence");
            }

            if (error) return false;

            // ### Parse datas
            var date = getDateFromColumn(cellPosition.col);

            absence.beginDate = new Date(date.getTime() + (parseTimeToSeconds(beginTimeVal) * 1000));
            absence.endDate = new Date(date.getTime() + (parseTimeToSeconds(endTimeVal) * 1000));
            absence.justified = this.justified.is(':checked');
            absence.absenceType = {
                value: absenceTypeValue,
                name: getAbsenceTypeName(absenceTypeValue)
            };

            // ### Send to server
            var url;

            var data = {
                studentId: absence.studentId,
                beginDate: absence.beginDate.format(DateFormat.SQL),
                endDate: absence.endDate.format(DateFormat.SQL),
                absenceTypeId: absence.absenceType.value,
                justified: absence.justified ? '1' : '0'
            };

            if (absence.editing) {
                data.absenceId = absence.absenceId;
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

                    var oldCellClasses = cell.className.split(" ");
                    cell.className = filterClasses(oldCellClasses, absence).join(" ");

                    // Create absence in DOM
                    if (absence.editing) {
                        // if edit, modify existant
                        active.className = "abs-" + absence.absenceType.name.toLowerCase();
                        var children = active.children;
                        $schedule = children[0];
                        $justified = children[1];
                        $absenceType = children[2];
                    } else {
                        // if new, create div
                        var div = document.createElement('div');
                        div.className = 'abs-' + absence.absenceType.name.toLowerCase();
                        div.id = 'absn' + data.substr(8);

                        $schedule = div.appendChild(document.createElement('p'));
                        $justified = div.appendChild(document.createElement('p'));
                        $absenceType = div.appendChild(document.createElement('p'));

                        cell.appendChild(div);
                    }

                    $schedule.textContent = 'Horaires : ' + beginTimeVal + ' - ' + endTimeVal;
                    $justified.textContent = 'Justifiée : ' + (absence.justified ? 'Oui' : 'Non');
                    $absenceType.textContent = absence.absenceType.name;

                    // 'new' button
                    // only modify if adding absence
                    if (!absence.editing) {
                        if ($(cell).children('div').length === 1) {
                            var button = document.createElement('button');
                            button.appendChild(document.createTextNode('Nouveau'));
                            cell.appendChild(button);
                        } else {
                            // 2 children, can't add more
                            $(cell).children('button').remove();
                            $(cell).children('div').show();
                        }
                    }

                } else if (data === 'cancel') {
                    alert('Erreur interne : requête annulée');
                } else if (data === 'missing_data') {
                    alert('Erreur interne : données manquantes');
                } else if (data.substring(0, 9) === 'exception') {
                    alert('Erreur interne : ' + data);
                } else {
                    /*
                     * Use of JS to receive datas than can only happen
                     * if the client that sent it has JS disabled.
                     * Really useful ?
                     */
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
