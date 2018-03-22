'use strict';
$(document).ready(function() {

    var DEFAULT_ANIM_TIME = 100;
    var ACTIVE_CONTAINER = {};

    var absences = {};

    /**
     * Absence class
     * @param {Number|null} absenceId
     * @param {Student} student
     * @param {Date} beginTime
     * @param {Date} endTime
     * @param {boolean} justified
     * @param {number} absenceTypeValue
     * @constructor
     */
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
            value: parseInt(absenceTypeValue) || 0,
            name: getAbsenceTypeName(absenceTypeValue)
        };
    }

    /**
     * Student class.
     * @param {string} studentId
     * @param {string} name
     * @constructor
     */
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
     * @param {number} format - A value of DateFormat
     * @returns {String} The formatted date
     */
    Date.prototype.format = function(format) {
        format = format || DateFormat.READABLE;

        switch (format) {
            case DateFormat.READABLE:
                var dayName = [
                    'Dimanche', 'Lundi', 'Mardi', 'Mercredi',
                    'Jeudi', 'Vendredi', 'Samedi'
                ];
                var monthNames = [
                    'Janvier', 'Février', 'Mars',
                    'Avril', 'Mai', 'Juin', 'Juillet',
                    'Août', 'Septembre', 'Octobre',
                    'Novembre', 'Décembre'
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
                return twoDigits(this.getUTCHours()) + ':' + twoDigits(this.getUTCMinutes());
            default:
                return '';
        }
    };

    if (!String.prototype.startsWith) {
        /**
         * @param {string} searchString - The string to search for
         * @param {number} [position=0] - The position where to begin to search
         * @returns {boolean} true if the string begins with the search string, false otherwise
         */
        String.prototype.startsWith = function (searchString, position) {
            position = position || 0;
            return this.substr(position, searchString.length) === searchString;
        };
    }

    if (!String.prototype.endsWith) {
        /**
         * @param {string} searchString - The string to look for
         * @param {number} [position] - Search for the search string like this was its length
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
     * Activate an element relatively to a field.
     * If the field contained another activated node,
     * it deactivates it then active the target element.
     * @param {string} field - The field of the target
     * @param {jQuery} target - The element to be activated
     */
    function activate(field, target) {
        if (ACTIVE_CONTAINER[field]) {
            ACTIVE_CONTAINER[field].removeClass('active');
        }
        ACTIVE_CONTAINER[field] = $(target).addClass('active');
    }

    /**
     * Deactivate the active element of a field.
     * @param {string} field The field to be emptied
     */
    function deactivate(field) {
        if (ACTIVE_CONTAINER[field]) {
            ACTIVE_CONTAINER[field].removeClass('active');
            delete ACTIVE_CONTAINER[field];
        }
    }

    /**
     * Return the activated node in a field.
     * @param {string} field - The field containing the active node
     * @returns {jQuery} The active DOM element corresponing to the field
     */
    function getActive(field) {
        return ACTIVE_CONTAINER[field];
    }

    /**
     * Add absence to the count of absence of 'its' student.
     * @param {Absence} absence - The absence to be added
     */
    function addToCount(absence) {
        var cell = $('#absn' + absence.absenceId).parent();

        var counter = $('#' + absence.student.id).find('div');
        var $datas = counter.children();

        var justifyIndex, justifiedAbs;

        // always add one halfday, as we add an absence
        var halfDays = parseInt($datas.eq(0).text()) + 1;
        $datas.eq(0).text(halfDays + ' demi-journée' + (halfDays > 1 ? 's' : ''));

        // If there's more than one absence
        if (halfDays > 1) {
            // If half-day count was 1 and no justified abs
            if ($datas.length === 1) {
                counter.append(document.createElement('p'));
                $datas = counter.children();
            }
            // if $datas[1] is justified abs
            else if ($datas.eq(1).text().split(' ').length === 3) {
                // Store it before it changes
                justifiedAbs = parseInt($datas.eq(1).text());
            }

            // if there's not already an absence this day, add one day
            var days = ($datas.length > 1 ? parseInt($datas.eq(1).text()) || 1 : 1)
                + !(cell.children().length - 1);

            $datas.eq(1).text(days + ' jour' + (days > 1 ? 's' : ''));

            justifyIndex = 2;
        } else {
            justifyIndex = 1;
        }


        if (!justifiedAbs) {
            justifiedAbs = ($datas.length > justifyIndex
                ? parseInt($datas.eq(justifyIndex).text()) : 0);
        }
        if (absence.justified) {
            justifiedAbs++;
        }

        if (justifiedAbs > 0) {
            if ($datas.length <= justifyIndex) {
                counter.append(document.createElement('p'));
                $datas = counter.children();
            }

            $datas.eq(justifyIndex).text(justifiedAbs
                + ' absence' + (justifiedAbs > 1 ? 's' : '')
                + ' justifiée' + (justifiedAbs > 1 ? 's' : '')
            );
        }
    }

    /**
     * Remove an absence to the count of 'its' student.
     * @param {Absence} absence - The absence to be removed
     */
    function removeFromCount(absence) {
        var cell = $('#absn' + absence.absenceId).parent();

        var $datas = $('#' + absence.student.id)
            .find('div')
            .children();

        // always add halfday, as we add an absence
        var halfDays = parseInt($datas.eq(0).text()) - 1;
        $datas.eq(0).text(halfDays + ' demi-journée' + (halfDays > 1 ? 's' : ''));

        // After removing
        if (halfDays > 1) {
            // if there's more than one absence
            var days = parseInt($datas.eq(1).text()) - !(cell.children().length - 1);
            $datas.eq(1).text(days + ' jour' + (days > 1 ? 's' : ''));
        } else {
            // if no or one absence left
            if (halfDays === 1) {
                $datas.eq(1).remove();
            }
        }

        updateJustifiedCount(absence.student, -1);
    }

    /**
     * Change the number of the justified absence for the student.
     * @param {Student} student - The student the change
     * @param {number} add - How many justification to add. Can be negative
     */
    function updateJustifiedCount(student, add) {
        var counter = $('#' + student.id).find('div');
        var $datas = counter.children();

        var justifyIndex, justifiedAbs;

        if ($datas.length > 1) {
            // If all infos are shown
            if ($datas.length === 3) {
                justifyIndex = 2;
                justifiedAbs = parseInt($datas.eq(2).text()) + add;
            }
            // if only 2 infos and second 'p' contains justified absence count
            else if ($datas.eq(1).text().split(' ').length === 3) {
                justifyIndex = 1;
                justifiedAbs = parseInt($datas.eq(1).text()) + add;
            }
            // if 2 infos, but no justified absence count
            else {
                justifyIndex = 2;
                justifiedAbs = add;
            }
        } else {
            justifyIndex = 1;
        }

        if (justifiedAbs > 0) {
            if ($datas.length <= justifyIndex) {
                counter.append(document.createElement('p'));
                $datas = counter.children();
            }

            $datas.eq(justifyIndex).text(justifiedAbs
                + ' absence' + (justifiedAbs > 1 ? 's' : '')
                + ' justifiée' + (justifiedAbs > 1 ? 's' : '')
            );
        } else if ($datas.length > justifyIndex) {
            // if justified paragraph exists but should not
            $datas.eq(justifyIndex).remove();
        }
    }

    /**
     * If the integer only contains 1 digit, fill with zeros
     * @param {number} d - In integer between -99 and 99
     * @returns {string} The number filled with zeros
     */
    function twoDigits(d) {
        if(d >= 0 && d < 10) return '0' + d.toString();
        if(d >= -10 && d < 0) return '-0' + (-d).toString();
        return d.toString();
    }

    /**
     * Parse a time formatted string (HH:mm:ss) in milliseconds.
     * @param {string} time - A time string with format HH:mm:ss
     * @returns {number} The number of milliseconds
     */
    function parseTime(time) {
        var vals = time.split(':').map(function(e) {
            return parseInt(e) || 0;
        });

        if ( (vals[0] && vals[0] < 0 && vals[0] >= 24)
            || (vals[1] && vals[1] < 0 && vals[1] >= 60)
            || (vals[2] && vals[2] < 0 && vals[2] >= 60)
        ) {
            throw TypeError('Invalid time');
        }

        return ((vals[0] || 0) * 3600 + (vals[1] || 0) * 60 + (vals[2] || 0)) * 1000;
    }

    /**
     * Return the time slot corresponding to the dates.
     * @param {Date} begin
     * @param {Date} end
     * @return {number} The index of the time slot, -1 if dates are incorrect
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
     * Return the Student object corresponding to the row
     * @param {number} row - The row on the table
     * @returns {Student} The informations about the student
     */
    function getStudentFromRow(row) {
        var $student = $('#table-stud-list').children('div').eq(row);

        return new Student(
            $student.prop('id'),
            $student.children('p').text()
        );

    }

    /**
     * Return the date corresponding to the column of the table
     * @param {number} col - The column
     * @returns {Date} The date corresponding
     */
    function getDateFromColumn(col) {
        var date = new Date(FIRST_DATE.getTime());
        date.setUTCDate(date.getUTCDate() + col);
        return date;
    }

    /**
     * Get the value of an absence type from its name.
     * @param {string} absenceName - The name of the asbence type
     * @returns {number} The value of the absence type, 0 if it doesn't exists
     */
    function getAbsenceTypeValue(absenceName) {
        var options = $('#am-absenceType').children();
        var value = 0;
        options.each(function() {
            if ($(this).text() === absenceName) {
                value = $(this).val();
                return false;
            }
        });
        return value;
    }

    /**
     * Get the name of the absence type from its value.
     * @param {number} absenceValue - The value of the absence type
     * @returns {string} The name of the absence type
     */
    function getAbsenceTypeName(absenceValue) {
        if (!absenceValue) {
            return '';
        }
        var absenceTypes = $('#am-absenceType').children();
        return absenceTypes.eq(absenceValue).text();
    }

    /**
     * Check if the element contains an absence that is in the morning or not.
     * @param {jQuery} element - The absence element to be tested
     * @returns {null|boolean} Whether the absence is in morning or not,
     * null if the element is not a valid absence container
     */
    function isMorningAbsence(element) {
        if (!element) {
            return null;
        }
        var time = $(element).children().eq(0).text().trim().substr(-13).split(' - ')[0];
        return parseTime(time) <= 43200000; // midday
    }

    /**
     *
     * @param {Object} array - Either 'newAbsence.morning' or 'newAbsence.afternoon'
     * @param {jQuery} array.timeSlot
     * @param {jQuery} array.absenceType
     * @param {jQuery} array.justified
     * @param {jQuery} array.delete
     * @param {Absence} absence - The absence to be used to fill the informations (can be null)
     * @param {string} activeField - The field to be used to active things in absence
     */
    function setInterfaceAbsence(array, absence, activeField) {
        if (absence) {
            array.activeAbsence = absence;
            if (absence.time.slot > -1) {
                activate(activeField, array.timeSlot.children().eq(absence.time.slot));
            }
            array.delete.removeClass('scale-out');
            array.justified.prop('checked', absence.justified);
            array.absenceType.val(absence.absenceType.value);
        } else {
            array.activeAbsence = null;
            deactivate(activeField);
            array.delete.addClass('scale-out');
            array.justified.prop('checked', false);
            array.absenceType.val('0');
        }
        array.absenceType.material_select();
    }

    /**
     * Create an absence from one of the interface.
     * @param {jQuery} cell - The cell to which belongs the absence
     * @param {Object} array - The interface used to create the absence (newAbsence.morning or newAbsense.afternoon)
     * @param {jQuery} array.timeSlot
     * @param {jQuery} array.absenceType
     * @param {jQuery} array.justified
     * @param {jQuery} array.delete
     * @param {number} absenceId - The id of the absence (only if updating an absence)
     * @returns {Absence} The absence created, null if a error happened
     */
    function createAbsenceFromInterface(cell, array, absenceId) {
        absenceId = absenceId || null;

        var $timeSlot = array.timeSlot.children('.active');

        var timeSlotVal = array.timeSlot.children().index($timeSlot);
        var absenceTypeVal = array.absenceType.val();

        // ### Check for errors
        var error = false;
        if (timeSlotVal === -1) {
            error = true;
            Materialize.toast('Vous n\'avez pas sélectionné de créneau', 4000, 'notif-warning');
        }

        if (!absenceTypeVal) {
            error = true;
            Materialize.toast('Vous n\'avez pas choisi de type d\'absence', 4000, 'notif-warning');
        }

        if (error) return null;

        // ### Parse datas
        var date = getDateFromColumn($(cell).index());
        var timeVals = $timeSlot.text().trim().split(' - ');

        var student = getStudentFromRow($(cell).parent().index());
        var beginTime = new Date(date.getTime() + parseTime(timeVals[0])),
            endTime = new Date(date.getTime() + parseTime(timeVals[1]));
        var justified = array.justified.is(':checked');

        return new Absence(
            absenceId,
            student,
            beginTime,
            endTime,
            justified,
            absenceTypeVal
        );
    }

    /**
     * Create an absence from the informations in the div
     * @param {jQuery} div - The element you want the absence from
     * @returns {Absence} An absence
     */
    function createAbsenceFromDiv(div) {
        var cell = $(div).parent();
        var $data = $(div).children();
        var date = getDateFromColumn(cell.index());

        var timeVals = $data.eq(0).text().trim().substr(-13).split(' - ');
        var beginTime = new Date(date.getTime() + parseTime(timeVals[0]));
        var endTime = new Date(date.getTime() + parseTime(timeVals[1]));

        return new Absence(
            parseInt($(div).prop('id').substr(4)),
            getStudentFromRow(cell.parent().index()),
            beginTime,
            endTime,
            $data.eq(1).text().trim().endsWith('Oui'),
            getAbsenceTypeValue($data.eq(2).text())
        );
    }

    /**
     * Deletes an absence the page
     * @param {Absence} absence - The absence to be deleted
     */
    function deleteAbsence(absence) {
        if (!absence) return;

        var $absence = $('#absn' + absence.absenceId);
        var cell = $absence.parent();
        var cellClasses = cell.attr('class').split(' ').filter(function(el) {
            return !el.startsWith('abs');
        });

        removeFromCount(absence);

        delete absences[absence.absenceId];
        $absence.remove();

        var cellChildren = cell.children();
        if (cellChildren.length) {
            var $otherAbsence = cellChildren.eq(0);

            cellClasses.push('abs');
            if ($otherAbsence.children().eq(1).text().trim().endsWith('Oui')) {
                cellClasses.push('abs-justifiee');
            }
            cellClasses.push($otherAbsence.attr('class'));
        }

        cell.removeClass()
            .addClass(cellClasses.join(' '));
    }

    /**
     * Choose to create or modify an absence.
     * @param {jQuery} cell - The cell that contains the absence
     * @param {Object} array - The interface used to create the absence (newAbsence.morning or newAbsense.afternoon)
     * @param {jQuery} array.timeSlot
     * @param {jQuery} array.absenceType
     * @param {jQuery} array.justified
     * @param {jQuery} array.delete
     * @param {Absence} [originalAbsence] - The old absence if it's a modification
     * @return {boolean} true if the operation was successful, false otherwise
     */
    function handleAbsence(cell, array, originalAbsence) {
        originalAbsence = originalAbsence || null;

        var absence = createAbsenceFromInterface(
            cell, array, originalAbsence ? originalAbsence.absenceId : null);

        if (!absence) {
            return false;
        }
        return sendAbsence(cell, absence);
    }

    /**
     * Permenantly save the absence.
     * @param {jQuery} cell - The cell to which belongs the absence
     * @param {Absence} absence - The absence to be added or modified
     * @return {boolean} true if the operation was successful, false otherwise
     */
    function sendAbsence(cell, absence) {
        if (!absence) return false;

        var data = {
            studentId: absence.student.id.toString(),
            beginDate: absence.time.begin.format(DateFormat.SQL),
            endDate: absence.time.end.format(DateFormat.SQL),
            absenceTypeId: absence.absenceType.value,
            justified: absence.justified
        };
        var url = '/Process_Absence/';

        if (absence.absenceId) {
            url += 'update';
            data.absenceId = absence.absenceId;
        } else {
            url += 'add';
        }

        $.post(url, data, function(data) {
            // If success
            if (data.match(/^success [0-9]*$/)) {
                var oldCellClasses = $(cell).attr('class').split(' ');
                $(cell)
                    .removeClass()
                    .addClass(filterClasses(oldCellClasses, absence).join(' '));

                var $schedule,
                    $justified,
                    $absenceType;

                if (absence.absenceId) {
                    // If absence edition
                    var wasJustified = absences[absence.absenceId].justified;
                    absences[absence.absenceId] = absence;

                    if (absence.justified !== wasJustified) {
                        updateJustifiedCount(absence.student, absence.justified - wasJustified);
                    }

                    var $absence = $('#absn' + absence.absenceId);
                    $absence
                        .removeClass()
                        .addClass('abs-' + absence.absenceType.name.toLowerCase());

                    // Fields to be modified
                    var children = $absence.children();
                    $schedule = children.eq(0);
                    $justified = children.eq(1);
                    $absenceType = children.eq(2);
                } else {
                    // if new absence
                    var absenceId = data.substr(8);
                    absence.absenceId = absenceId;
                    absences[absenceId] = absence;

                    // create new absence container
                    var div = document.createElement('div');
                    div.className = 'abs-' + absence.absenceType.name.toLowerCase();
                    div.id = 'absn' + absenceId;

                    // add the fields that will be filled to the container
                    $schedule = $(div.appendChild(document.createElement('p')));
                    $justified = $(div.appendChild(document.createElement('p')));
                    $absenceType = $(div.appendChild(document.createElement('p')));

                    // if absence is in the morning, add it before the other absence
                    if (absence.time.begin.getUTCHours() <= 12) {
                        $(cell).prepend(div);
                    } else {
                        $(cell).append(div);
                    }
                    addToCount(absence);
                }

                $schedule.text('Horaires : ' + absence.time.begin.format(DateFormat.SHORT_TIME)
                    + ' - ' + absence.time.end.format(DateFormat.SHORT_TIME));
                $justified.text('Justifiée : ' + (absence.justified ? 'Oui' : 'Non'));
                $absenceType.text(absence.absenceType.name);

            } else if (data === 'cancel') {
                Materialize.toast(
                    'Erreur interne : requête annulée. Nous vous conseillons de rafraichir la page',
                    4000, 'notif-warning'
                );
            } else if (data === 'missing_data') {
                Materialize.toast('Les données envoyées sont incomplètes', 4000, 'notif-warning');
            } else if(data === 'wrong_data') {
                Materialize.toast('Les données envoyées sont incorrectes', 4000, 'notif-danger');
            } else if (data === 'fail') {
                Materialize.toast('Erreur : Impossible de créer l\'absence', 4000, 'notif-warning');
            }
        });

        return true;
    }

    function createDayAbsence(cell) {
        var $cell = $(cell);
        var date = getDateFromColumn($(cell).index());
        var student = getStudentFromRow($(cell).parent().index());

        sendAbsence($cell, new Absence(
            null,
            student,
            new Date(date.getTime() + (8 * 3600 * 1000)),
            new Date(date.getTime() + (12 * 3600 * 1000)),
            false,
            1
        ));

        sendAbsence($cell, new Absence(
           null,
           student,
           new Date(date.getTime() + (14 * 3600 * 1000)),
           new Date(date.getTime() + (18 * 3600 * 1000)),
           false,
           1
        ));
    }

    /**
     * This function takes the old classes of a cell and
     * the new absence to be added or modified and produce
     * the new classes to be set on the cell.
     * If the cell has classes that do not have relation with absence,
     * they will be kept.
     *
     * @param {Array.<string>} oldClasses - The old classes of the cell
     * @param {Absence} absence - The absence to be added/modified
     * @returns {Array.<string>} The new classes of the cell
     */
    function filterClasses(oldClasses, absence) {
        var cellClasses = oldClasses.slice(0).filter(function(el) {
            return !el.startsWith('abs');
        });
        cellClasses.push('abs');

        var htmlId = '#absn' + absence.absenceId;
        var $otherChild = $(htmlId).siblings('div');

        var newClass = 'abs-' + absence.absenceType.name.toLowerCase();

        // if edition
        if (absence.absenceId) {
            // if absence justified and, if it exists, other one is too
            if (absence.justified
                && ($otherChild.length
                    ? $otherChild.find('p:nth-child(2)').text().trim().endsWith('Oui')
                    : true)
            ) {
                cellClasses.push('abs-justifiee');
            }

            // if there's only one absence
            // or the other absence has same class
            cellClasses.push(!$otherChild.length || $otherChild.hasClass(newClass)
                ? newClass      // then, add the only or common class
                : 'abs-several' // else, there are different classes
            );
        }
        // if adding
        else {
            var wasAbsenceCell = oldClasses.indexOf('abs') > -1;
            var wasJustifiedCell = oldClasses.indexOf('abs-justifiee') > -1;

            // if absence is justified and, if cell had an absence, was it justified
            if (absence.justified && (wasAbsenceCell ? wasJustifiedCell : true)) {
                cellClasses.push('abs-justifiee');
            }

            var notAbsenceClass = function(el) {
                return el === 'abs-justifiee' || !el.startsWith('abs-');
            };

            // if first absence
            // or new class already existed
            cellClasses.push(oldClasses.every(notAbsenceClass) || oldClasses.indexOf(newClass) > -1
                ? newClass      // then, add the only or common class
                : 'abs-several' // else, there are different classes
            );
        }

        return cellClasses;
    }

    var edition = {
        content: $('#edition'),
        name: $('#edition-name'),
        date: $('#edition-date'),
        morning: {
            activeAbsence: null,
            modified: false,
            content: $('#edition-morning'),
            timeSlot: $('#am-time'),
            justified: $('#am-justified'),
            absenceType: $('#am-absenceType'),
            delete: $('#am-delete'),
            setModified: function(modif) {
                this.modified = modif;
                if (modif)  this.delete.removeClass('scale-out');
                else        this.delete.addClass('scale-out');
            }
        },
        afternoon: {
            activeAbsence: null,
            modified: false,
            content: $('#edition-afternoon'),
            timeSlot: $('#pm-time'),
            justified: $('#pm-justified'),
            absenceType: $('#pm-absenceType'),
            delete: $('#pm-delete'),
            setModified: function(modif) {
                this.modified = modif;
                if (modif)  this.delete.removeClass('scale-out');
                else        this.delete.addClass('scale-out');
            }
        },
        submitButton: $('#edition-submit'),

        edit: function(td) {
            var $absences = $(td).children();
            var amAbsence = null,
                pmAbsence = null;

            var student = getStudentFromRow($(td).parent().index());
            var date = getDateFromColumn(td.cellIndex);
            var tmp, absenceId;

            if ($absences.length >= 1) {
                // Morning absence
                absenceId = parseInt($absences.eq(0).prop('id').substr(4));
                tmp = absences[absenceId];

                if (tmp.time.begin.getUTCHours() <= 12) {
                    amAbsence = tmp;

                    if ($absences.length === 2) {
                        // Afternoon absence
                        absenceId = parseInt($absences.eq(1).prop('id').substr(4));
                        pmAbsence = absences[absenceId];
                    }
                } else {
                    pmAbsence = tmp;
                }

            }

            this.prepare(student, date, amAbsence, pmAbsence);
            this.show();
        },

        prepare: function(student, date, amAbsence, pmAbsence) {

            this.name.text(student.name);
            this.date.text(date.format(DateFormat.READABLE));

            var $pmTimes = this.afternoon.timeSlot.children();

            if (date.getUTCDay() === 5) { // Friday
                $pmTimes.eq(0).text('13:30 - 17:30');
                $pmTimes.eq(1).text('13:30 - 15:30');
                $pmTimes.eq(2).text('15:30 - 17:30');
            } else {
                $pmTimes.eq(0).text('14:00 - 18:00');
                $pmTimes.eq(1).text('14:00 - 16:00');
                $pmTimes.eq(2).text('16:00 - 18:00');
            }

            setInterfaceAbsence(this.morning, amAbsence, 'morning');
            setInterfaceAbsence(this.afternoon, pmAbsence, 'afternoon');
        },

        send: function() {
            var cell = getActive('cell');
            var cellChildren = cell.children();

            var absence;
            var morningAbsence = isMorningAbsence(cellChildren.eq(0)),
                afternoonAbsence = null;

            var handled = true;
            if (this.morning.modified) {
                if (morningAbsence === null || morningAbsence === false) {
                    handled = handleAbsence(cell, this.morning);
                }
                else {
                    absence = absences[(cellChildren.eq(0).prop('id') || '').substr(4)];
                    handled = handleAbsence(cell, this.morning, absence);
                }
            }

            if (this.afternoon.modified) {
                var index = morningAbsence ? (cellChildren.length === 2 ? 1 : -1) : 0;

                if (index > -1) {
                    afternoonAbsence = isMorningAbsence(cellChildren.eq(index));
                    if (afternoonAbsence !== null) {
                        afternoonAbsence = !afternoonAbsence;
                    }
                }

                if (index === -1 || afternoonAbsence === null) {
                    handled = handleAbsence(cell, this.afternoon);
                }
                else if (afternoonAbsence === true) {
                    absence = absences[cellChildren.eq(index).prop('id').substr(4)];
                    handled = handleAbsence(cell, this.afternoon, absence);
                }
            }

            if (handled) this.hide();
        },

        show: function() {
            edition.content.modal('open');
        },

        hide: function() {
            this.content.modal('close');
        }
    };

    // #################################
    // ########  PROGRAM START  ########
    // #################################
    var firstDayInWeek = FIRST_DATE.getDay();

    $('head').append(
        '<style>'
        + 'tbody td:nth-child(7n + ' + (8 - firstDayInWeek) + '),'
        + 'tbody td:nth-child(7n + ' + (7 - firstDayInWeek) + ') {'
            + 'background-color: rgba(255, 183, 77, .6);'
        + '}'
        + '</style>'
    );

    var $absenceTable = $('#absences-table');
    var $tableWrapper = $('#table-wrapper');

    // Initialize materialize
    edition.content.modal({
        dismissible: true,
        inDuration: 200,
        outDuration: 125,
        startingTop: '50%',
        endingTop: '8%',
        complete: function() {
            edition.morning.setModified(false);
            edition.afternoon.setModified(false);
        }
    });

    // Store existing absences in an object
    $('td.abs').each(function() {
        $(this).children().each(function() {
            var absence = createAbsenceFromDiv($(this));
            absences[absence.absenceId] = absence;
        });
    });

    // Center #active-day
    $tableWrapper.each( function() {
        var activeDay = $('#active-day');
        if (activeDay.length) {
            this.scrollLeft = activeDay.position().left - (window.innerWidth / 2);
        }
    });

    // Student list events
    $('#table-stud-list').on('click', 'i', function() {
        $(this).siblings('div').toggle(DEFAULT_ANIM_TIME);
    });

    // Absence table events
    $absenceTable
        .find('tbody')
        .on('click',
            'td:not(:nth-child(7n + ' + (8 - firstDayInWeek) + '))'
            + ':not(:nth-child(7n + ' + (7 - firstDayInWeek) + '))',
            function(event) {
                activate('cell', this);
                if (event.shiftKey) {
                    createDayAbsence(this);
                } else {
                    edition.edit(this);
                }
            })
        .on('mouseenter', 'td.abs', function() {
            $(this).children().show(DEFAULT_ANIM_TIME);
        })

        .on('mouseleave', 'td.abs', function() {
            $(this).children().hide(DEFAULT_ANIM_TIME);
        });

    // Absence edition interface events
    // Morning
    edition.morning.timeSlot.on('click', 'p', function() {
        activate('morning', this);
        edition.morning.setModified(true);
    });

    edition.morning.content.on('change', ':checkbox, select', function() {
        edition.morning.setModified(true);
    });

    edition.morning.delete.click(function() {
        var absence = edition.morning.activeAbsence;
        if (absence === null) {
            setInterfaceAbsence(edition.morning, null, 'morning');
            edition.morning.setModified(false);
        } else {
            $.post('/Process_Absence/delete', {absenceId: absence.absenceId}, function(data) {
                switch (data) {
                    case 'success':
                        setInterfaceAbsence(edition.morning, null, 'morning');
                        deleteAbsence(absence);
                        edition.morning.setModified(false);
                        break;
                    case 'missing_data':
                    case 'fail':
                        Materialize.toast('Erreur de communication avec le serveur.\n'
                            + 'Nous vous conseillons de rafraîchir la page',
                            4000, 'notif notif-warning');
                        break;
                    default:
                        Materialize.toast('Message inconnu reçu du serveur', 4000, 'notif notif-danger');
                }
            });
        }
    });

    // Afternoon
    edition.afternoon.timeSlot.on('click', 'p', function() {
        edition.afternoon.setModified(true);
        activate('afternoon', this);
    });

    edition.afternoon.content.on('change', ':checkbox, select', function() {
        edition.afternoon.setModified(true);
    });

    edition.afternoon.delete.click(function() {
        var absence = edition.afternoon.activeAbsence;
        if (absence === null) {
            setInterfaceAbsence(edition.afternoon, null, 'afternoon');
            edition.afternoon.setModified(false);
        } else {
            $.post('/Process_Absence/delete', {absenceId: absence.absenceId}, function(data) {
                switch (data) {
                    case 'success':
                        setInterfaceAbsence(edition.afternoon, null, 'afternoon');
                        deleteAbsence(absence);
                        edition.afternoon.setModified(false);
                        break;
                    case 'missing_data':
                    case 'fail':
                        Materialize.toast('Erreur de communication avec le serveur.\n'
                            + 'Nous vous conseillons de rafraîchir la page',
                            4000, 'notif notif-warning');
                        break;
                    default:
                        Materialize.toast('Message inconnu reçu du serveur', 4000, 'notif notif-danger');
                }
            });
        }
    });

    // New absence footer
    edition.submitButton.click(function() {
        edition.send();
        return false;
    });

    // Absence table head fixation
    var $absenceTableHead = $('#absences-table-head');
    var $fixedHeader = $('#header-fixed').append($absenceTableHead.clone());

    var theadTopPosition = $absenceTableHead.position().top;

    $(window)
        .on('scroll', function() {
            var offset = $(this).scrollTop();
            if (offset >= theadTopPosition && $fixedHeader.is(':hidden')) {
                $fixedHeader.css('display', 'table');
            } else if (offset < theadTopPosition) {
                $fixedHeader.css('display', 'none');
            }
        })
        // Simulate scroll so fixed header appears if page is scrolled down at loading
        .scroll();

    // Make thead follow horizontal scroll of the body
    var tableStaticWidth = $('#table-static').outerWidth(true);
    $tableWrapper
        .on('scroll', function() {
            $fixedHeader.css('left', -this.scrollLeft + tableStaticWidth);
        });
});
