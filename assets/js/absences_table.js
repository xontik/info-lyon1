$(function() {

    "use strict";

    var DEFAULT_ANIM_TIME = 100;
    var ACTIVE_CONTAINER = {};

    var absences = {};

    function Absence(absenceId, student, beginTime, endTime, justified, absenceTypeValue) {
        this.absenceId = absenceId || null;
        this.student = student || null;
        this.time = {};
        this.time = {
            begin: beginTime,
            end: endTime,
            slot: getTimeSlot(beginTime, endTime)
        };
        this.justified = justified || false;
        this.absenceType = {
            value: absenceTypeValue || 0,
            name: getAbsenceTypeName(absenceTypeValue)
        };
    }

    function Student(studentId, name) {
        this.id = studentId || '';
        this.name = name || '';
    }

    var DateFormat = {
        READABLE: 1,
        SQL: 2,
        SHORT_TIME: 3
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
            var dayName = [
                "Dimanche", "Lundi", "Mardi", "Mercredi",
                "Jeudi", "Vendredi", "Samedi"
            ];
            var monthNames = [
                "Janvier", "Février", "Mars",
                "Avril", "Mai", "Juin", "Juillet",
                "Août", "Septembre", "Octobre",
                "Novembre", "Décembre"
            ];

            return dayName[this.getUTCDay()] + ' '
                + this.getUTCDate() + ' '
                + monthNames[this.getUTCMonth()] + ' '
                + this.getUTCFullYear();
        case DateFormat.SQL:
            return this.getUTCFullYear() + '-'
                + twoDigits(1 + this.getUTCMonth()) + '-'
                + twoDigits(this.getUTCDate()) + ' '
                + twoDigits(this.getUTCHours()) + ':'
                + twoDigits(this.getUTCMinutes()) + ':'
                + twoDigits(this.getUTCSeconds());
        case DateFormat.SHORT_TIME:
            return twoDigits(this.getUTCHours()) + 'h' + twoDigits(this.getUTCMinutes());
        default:
            return '';
        }
    };

    if (!String.prototype.startsWith) {
        /**
         * @param searchString The string to search for
         * @param position The position where to begin to search
         * @returns {boolean} true if the string begins with the search string, false otherwise
         */
        String.prototype.startsWith = function (searchString, position) {
            position = position || 0;
            return this.substr(position, searchString.length) === searchString;
        };
    }

    if (!String.prototype.endsWith) {
        /**
         * @param searchString The string to look for
         * @param position Search for the search string like this was its length
         * @returns {boolean} true if the string ends with the search string, false otherwise
         */
        String.prototype.endsWith = function(searchString, position) {
            var subjectString = this.toString();
            if (typeof position !== 'number' || !isFinite(position)
                || Math.floor(position) !== position || position > subjectString.length
            ) {
                position = subjectString.length;
            }
            position -= searchString.length;
            var lastIndex = subjectString.lastIndexOf(searchString, position);
            return lastIndex !== -1 && lastIndex === position;
        };
    }

    /**
     * Activate an element relative to a field.
     * If the field contained another actived element,
     * it deactivate it then active the target element.
     * @param field The field of the target
     * @param target The element to be activated
     */
    function activate(field, target) {
        if (ACTIVE_CONTAINER[field]) {
            ACTIVE_CONTAINER[field].removeClass('active');
        }
        ACTIVE_CONTAINER[field] = $(target).addClass('active');
    }

    /**
     * Deactivate all the elements a field
     * @param field The field to be emptied
     */
    function deactivate(field) {
        if (ACTIVE_CONTAINER[field]) {
            ACTIVE_CONTAINER[field].removeClass('active');
            delete ACTIVE_CONTAINER[field];
        }
    }

    function getActive(field) {
        return ACTIVE_CONTAINER[field]
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
     * @param begin Date
     * @param end Date
     * @return {int} The index of the time slot, -1 if dates are incorrect
     */
    function getTimeSlot(begin, end) {
        if (begin.getUTCDate() !== end.getUTCDate()
        || begin.getUTCMonth() !== end.getUTCMonth()
        || begin.getUTCFullYear() !== end.getUTCFullYear()
        ) {
            return -1;
        }

        var beginHour = begin.getUTCHours() + begin.getUTCMinutes() / 60;
        var endHour = end.getUTCHours() + end.getUTCMinutes() / 60;

        return beginHour === 8 || beginHour === 13.5 || beginHour === 14
            ? (endHour === 12 || endHour === 17.5 || endHour === 18 ? 0 : 1)
            : (beginHour === 10 || beginHour === 16 || beginHour === 15.5 ? 2 : -1);

    }

    /**
     * Return the id and the name of the student corresponding to the row
     * @param row The row on the table
     * @returns {object} The informations about the student
     */
    function getStudentFromRow(row) {
        var $studentDiv = $('#table-stud-list').children('div')
            .eq(row);
        return new Student(
            $studentDiv.attr('id'),
            $studentDiv.children('p').text()
        );

    }

    /**
     * Return the date corresponding to the column of the table
     * @param col The column
     * @returns {Date} The date corresponding
     */
    function getDateFromColumn(col) {
        var date = new Date(FIRST_DATE.getTime());
        date.setDate(date.getUTCDate() + col);
        return date;
    }

    /**
     * @param absenceName The name of the asbence type
     * @returns {number} The value of the absence type, 0 if it doesn't exists
     */
    function getAbsenceTypeValue(absenceName) {
        var options = document.getElementById('am-absenceType').children;
        for (var i = 0; i < options.length; i++) {
            if (options[i].textContent === absenceName) {
                return parseInt(options[i].value);
            }
        }
        return 0;
    }

    /**
     * Get the name of the absence type from its value.
     * @param absenceValue The value of the absence type
     * @returns {string} The name of the absence type
     */
    function getAbsenceTypeName(absenceValue) {
        absenceValue = parseInt(absenceValue);
        if (!absenceValue) {
            return '';
        }
        return document.getElementById('am-absenceType').children[absenceValue].textContent;
    }

    /**
     * Check if the element contains an absence that is in the morning or not.
     * @param $div The absence element to be tested
     * @returns {boolean} Whether the absence is in morning or not
     */
    function isMorningAbsence($div) {
        var time = $($div).children().eq(0).text().substr(-13, 5);
        return parseTimeToSeconds(time) <= 43200; // midday
    }

    /**
     * Choose to create or to modify an absence.
     * @param cell The cell that contains the absence
     * @param array newAbsence.morning or newAbsense.afternoon
     * @return {bool} Whether the absence was added/modified or not
     */
    function handleAbsence(cell, array) {
        //TODO Create or modify absence
    }

    /**
     * Create an absence from the informations in the div
     * (and its position in the table)
     * @param div The element you want the absence from
     * @returns {Absence} An absence
     */
    function createAbsenceFromDiv(div) {
        var cell = div.parentNode;
        var date = getDateFromColumn(cell.cellIndex);

        var timeContent = div.children[0].textContent.trim();
        var beginTime = new Date(date.getTime() + parseTimeToSeconds(timeContent.substr(-13, 5)) * 1000);
        var endTime = new Date(date.getTime() + parseTimeToSeconds(timeContent.substr(-5)) * 1000);

        return new Absence(
            div.id.substr(4),
            getStudentFromRow(cell.parentNode.rowIndex),
            beginTime,
            endTime,
            div.children[1].textContent.trim().substr(-3) === "Oui",
            getAbsenceTypeValue(div.children[2].textContent)
        );
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
        // if edition
        if (absence.absenceId) {
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

    // #################################
    // ########  PROGRAM START  ########
    // #################################

    var $absenceTable = $('#absences-table');
    var $tableWrapper = $('#table-wrapper');

    /* ##### Center #active-day ##### */

    $tableWrapper.each( function() {
        var activeDay = $('#active-day');
        if (activeDay.length) {
            this.scrollLeft = activeDay.position().left - (window.innerWidth / 2);
        }
    });

    $('td.abs').each(function() {
        $(this).children().each(function() {
            var absence = createAbsenceFromDiv(this);
            absences[absence.absenceId] = absence;
        });
    });

    /* ##### Per day absence informations #####*/

    $absenceTable.on('click', 'td', function(event) {
        if (event.which === 1) {
            activate("cell", this);
            newAbsence.edit(this);
        }
    })

    .on('mouseenter', 'td.abs', function() {
        $(this).children('div').show(DEFAULT_ANIM_TIME);
    })

    .on('mouseleave', 'td.abs', function() {
        $(this).children().hide(DEFAULT_ANIM_TIME);
    });


    /* ##### Per day absence creation ##### */

    var newAbsence = {
        wrapper: $('#edition-wrapper'),
        content: $('#edition'),
        name: $('#edition-name'),
        date: $('#edition-date'),
        morning: {
            modified: false,
            content: $('#edition-morning'),
            timeSlot: $('#am-time'),
            justified: $('#am-justified'),
            absenceType: $('#am-absenceType')
        },
        afternoon: {
            modified: false,
            content: $('#edition-afternoon'),
            timeSlot: $('#pm-time'),
            justified: $('#pm-justified'),
            absenceType: $('#pm-absenceType')
        },
        submitButton: $('#edition-submit'),
        cancelButton: $('#edition-cancel'),

        edit: function(td) {
            var $absences = td.children;
            var amAbs = null,
                pmAbs = null;

            var student = getStudentFromRow(td.parentNode.rowIndex);
            var date = getDateFromColumn(td.cellIndex);
            var tmp, absenceId;

            if ($absences.length >= 1) {
                // Morning absence
                absenceId = parseInt(($absences[0].id || '').substr(4));
                tmp = absences[absenceId];

                if (tmp.time.begin.getUTCHours() <= 12) {
                    amAbs = tmp;

                    if ($absences.length === 2) {
                        // Afternoon absence
                        absenceId = parseInt(($absences[0].id || '').substr(4));
                        pmAbs = absences[absenceId];
                    }
                } else {
                    pmAbs = tmp;
                }

            }

            this.prepare(student, date, amAbs, pmAbs);
            this.show();
        },

        prepare: function(student, date, amAbs, pmAbs) {

            this.name.text(student.name);
            this.date.text(date.format(DateFormat.READABLE));

            var $pmTimes = this.afternoon.timeSlot.children();

            if (date.getDay() === 5) { // Friday
                $pmTimes[0].textContent = '13h30 - 17h30';
                $pmTimes[1].textContent = '13h30 - 15h30';
                $pmTimes[2].textContent = '15h30 - 17h30';
            } else {
                $pmTimes[0].textContent = '14h00 - 18h00';
                $pmTimes[1].textContent = '14h00 - 16h00';
                $pmTimes[2].textContent = '16h00 - 18h00';
            }

            if (amAbs) {
                activate('morning', this.morning.timeSlot.children().eq(amAbs.time.slot));
                this.morning.justified[0].checked = amAbs.justified;
                this.morning.absenceType.val(amAbs.absenceType.value);
            } else {
                this.morning.justified[0].checked = false;
                this.morning.absenceType.val('0');
            }

            if (pmAbs) {
                activate('afternoon', this.afternoon.timeSlot.children().eq(pmAbs.time.slot));
                this.afternoon.justified[0].checked = pmAbs.justified;
                this.afternoon.absenceType.val(pmAbs.absenceType.value);
            } else {
                this.afternoon.justified[0].checked = false;
                this.afternoon.absenceType.val('0');
            }

        },

        send: function() {
            var cell = getActive('cell');
            var handled;
            if (this.morning.modified) {
                handled = handleAbsence(cell, this.morning);
            }
            if (this.afternoon.modified && handled) {
                handled = handleAbsence(cell, this.afternoon);
            }
            if (handled) this.hide();
        },

        show: function() {
            this.wrapper.addClass('active');
        },

        hide: function() {
            this.wrapper.removeClass('active');

            deactivate('morning');
            deactivate('afternoon');

            this.morning.modified = false;
            this.afternoon.modified = false;
        }
    };

    newAbsence.morning.timeSlot.on('click', 'p', function() {
        newAbsence.morning.modified = true;
        if ($(this).is('.active')) {
            deactivate("morning");
        } else {
            activate("morning", this);
        }
    });

    newAbsence.morning.content.on('change', ':checkbox, select', function() {
        newAbsence.morning.modified = true;
    });

    newAbsence.afternoon.timeSlot.on('click', 'p', function() {
        newAbsence.afternoon.modified = true;
        if ($(this).is('.active')) {
            deactivate("afternoon");
        } else {
            activate("afternoon", this);
        }
    });

    newAbsence.afternoon.content.on('change', ':checkbox, select', function() {
        newAbsence.afternoon.modified = true;
    });

    newAbsence.submitButton.click(function() {
        newAbsence.send();
        return false;
    });

    newAbsence.cancelButton.click(function() {
        newAbsence.hide();
        return false;
    });


    /* ##### Student absence count ##### */

    $('#table-stud-list').on('click', 'p + img', function() {
        $(this).siblings('div').toggle(DEFAULT_ANIM_TIME);
    });


    // ##### Thead fixation #####

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
