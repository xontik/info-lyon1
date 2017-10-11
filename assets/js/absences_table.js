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
            return twoDigits(this.getUTCHours()) + ':' + twoDigits(this.getUTCMinutes());
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
     * Activate an element relatively to a field.
     * If the field contained another activated node,
     * it deactivates it then active the target element.
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
     * Deactivate the active element of a field.
     * @param field The field to be emptied
     */
    function deactivate(field) {
        if (ACTIVE_CONTAINER[field]) {
            ACTIVE_CONTAINER[field].removeClass('active');
            delete ACTIVE_CONTAINER[field];
        }
    }

    /**
     * Return the activated node in a field.
     * @param field The field containing the active node
     * @returns {Node} The active DOM element corresponing to the field
     */
    function getActive(field) {
        return ACTIVE_CONTAINER[field]
    }

    /**
     * Add absence to the count of absence of "its" student.
     * @param absence The absence to be added
     */
    function addToCount(absence) {
        var cell = document.getElementById('absn' + absence.absenceId).parentNode;

        var $div = $('#' + absence.student.id).find('div')[0];
        var $datas = $div.children;

        var justifiyIndex, justifiedDays;

        // always add halfday, as we add an absence
        var halfDays = parseInt($datas[0].textContent) + 1;
        $datas[0].textContent = halfDays + " demi-journée" + (halfDays > 1 ? 's' : '');

        // If there's more than one absence
        if (halfDays > 1) {
            // if $datas[1] is justified days
            if ($datas[1].textContent.split(' ').length === 3) {
                // Store it before it changes
                justifiedDays = parseInt($datas[1].textContent);
            }
            // If half-day count was 1
            else if ($datas.length === 1) {
                $div.appendChild(document.createElement('p'));
            }

            // if there's not already an absence this day, add one day
            var days = ($datas.length > 1 ? parseInt($datas[1].textContent) || 1 : 1)
                + !(cell.children.length - 1);

            $datas[1].textContent = days + " jour" + (days > 1 ? 's' : '');

            justifiyIndex = 2;
        } else {
            justifiyIndex = 1;
        }


        if (absence.justified) {
            if (!justifiedDays) {
                justifiedDays = ($datas.length > justifiyIndex
                    ? parseInt($datas[justifiyIndex].textContent) : 0);
            }
            justifiedDays++;

            if ($datas.length <= justifiyIndex) {
                $div.appendChild(document.createElement('p'));
            }

            $datas[justifiyIndex].textContent = justifiedDays
                + " absence" + (justifiedDays > 1 ? 's' : '')
                + " justifiée" + (justifiedDays > 1 ? 's' : '');
        }
    }

    /**
     * Remove an absence to the count of "its" student.
     * @param absence The absence to be removed
     */
    function removeFromCount(absence) {
        var cell = document.getElementById('absn' + absence.absenceId).parentNode;

        var $datas = $('#' + absence.student.id)
            .find('div')[0].children;

        var justifiyIndex, justifiedDays;

        // always add halfday, as we add an absence
        var halfDays = parseInt($datas[0].textContent) - 1;
        $datas[0].textContent = halfDays + " demi-journée" + (halfDays > 1 ? 's' : '');

        // After removing
        if (halfDays > 1) {
            // if there's more than one absence
            var days = parseInt($datas[1].textContent) - !(cell.children.length - 1);
            $datas[1].textContent = days + " jour" + (days > 1 ? 's' : '');

            justifiyIndex = 2;
        } else {
            // if no or one absence left
            justifiyIndex = 1;
            if (halfDays === 1) $($datas[1]).remove();
        }

        if (absence.justified && $datas.length > justifiyIndex) {
            justifiedDays = parseInt($datas[justifiyIndex].textContent) - 1;
            if (justifiedDays === 0) {
                $($datas[justifiyIndex]).remove();
            } else {
                $datas[justifiyIndex].textContent = justifiedDays
                    + " absence" + (justifiedDays > 1 ? 's' : '')
                    + " justifiée" + (justifiedDays > 1 ? 's' : '');
            }
        }
    }

    /**
     * Change the number of the justified absence for the student.
     * @param student The student the change
     * @param add How many justification to add. Can be negative
     */
    function updateJustifiedCount(student, add) {
        var $div = $('#' + student.id).find('div')[0];
        var $datas = $div.children;
		
        var justifyIndex, justifiedDays;

        if ($datas.length > 1) {
			// If all infos are shown
			if ($datas.length === 3) {
                justifyIndex = 2;
                justifiedDays = parseInt($datas[2].textContent) + add;
			}
			// if only 2 infos and second 'p' contains justified absence count
            else if ($datas[1].textContent.split(' ').length === 3) {
                justifyIndex = 1;
                justifiedDays = parseInt($datas[1].textContent) + add;
            }
			// if 2 infos, but no justified absence count
            else {
				justifyIndex = 2;
				justifiedDays = add;
			}
        } else {
			justifyIndex = 1;
		}

		if (justifiedDays > 0) {
			if ($datas.length <= justifyIndex) {
				$div.appendChild(document.createElement('p'));
			}
			$datas[justifyIndex].textContent = justifiedDays
				+ " absence" + (justifiedDays > 1 ? 's' : '')
				+ " justifiée" + (justifiedDays > 1 ? 's' : '');
		} else if ($datas.length > justifyIndex) {
			$($datas[justifyIndex]).remove();
		}
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
     * Parse a time formatted string (HH:mm:ss).
     * @param time A formatted string: HH:mm:ss
     * @returns {number} The number of seconds
     */
    function parseTimeToSeconds(time) {
        return (parseInt(time.substr(0, 2)) || 0) * 3600
            + (parseInt(time.substr(3, 2)) || 0) * 60
            + (parseInt(time.substr(7, 2)) || 0);
    }

    /**
     * Return the time slot corresponding to the dates.
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
        date.setUTCDate(date.getUTCDate() + col);
        return date;
    }

    /**
     * Get the value of an absence type from its name.
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
     * @returns {null|boolean} Whether the absence is in morning or not,
     * null if $div is not a valid absence container
     */
    function isMorningAbsence($div) {
        if (!$div) {
            return null;
        }
        var time = $($div).children().eq(0).text().substr(-13, 5);
        return parseTimeToSeconds(time) <= 43200; // midday
    }

    /**
     *
     * @param array Either 'newAbsence.morning' or 'newAbsence.afternoon'
     * @param absence The absence to be used to fill the informations (can be null)
     * @param activeField The field to be used to active things in absence
     */
    function setInterfaceAbsence(array, absence, activeField) {
        if (absence) {
            array.activeAbsence = absence;
            activate(activeField, array.timeSlot.children().eq(absence.time.slot));
            activate(activeField + 'Delete', array.delete);
            array.justified[0].checked = absence.justified;
            array.absenceType.val(absence.absenceType.value);
        } else {
            array.activeAbsence = null;
            deactivate(activeField);
            deactivate(activeField + 'Delete');
            array.justified[0].checked = false;
            array.absenceType.val('0');
        }
    }

    /**
     * Create an absence from one of the interface.
     * @param cell The cell to which belongs the absence
     * @param array The interface used to create the absence (newAbsence.morning or newAbsense.afternoon)
     * @param absenceId The id of the absence (only if updating an absence)
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
            alert("Vous n'avez pas sélectionné de créneau");
        }

        if (absenceTypeVal === '0') {
            error = true;
            alert("Vous n'avez pas choisi de type d'absence");
        }

        if (error) return null;

        // ### Parse datas
        var date = getDateFromColumn(cell.cellIndex);
        var beginTimeVal = $timeSlot.text().substr(0, 5),
            endTimeVal = $timeSlot.text().substr(-5);

        var student = getStudentFromRow(cell.parentNode.rowIndex - 2);
        var beginTime = new Date(date.getTime() + parseTimeToSeconds(beginTimeVal) * 1000),
            endTime = new Date(date.getTime() + parseTimeToSeconds(endTimeVal) * 1000);
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
            getStudentFromRow(cell.parentNode.rowIndex - 2),
            beginTime,
            endTime,
            div.children[1].textContent.trim().substr(-3) === "Oui",
            getAbsenceTypeValue(div.children[2].textContent)
        );
    }

    /**
     * Deletes an absence from interface and the database
     * @param absence The absence to be deleted
     */
    function deleteAbsence(absence) {
        if (!absence) return;

        var $absence = $('#absn' + absence.absenceId);
        var cell = $absence.parent()[0];
        var cellClasses = cell.className.split(' ').filter(function(element) {
            return element.substring(0, 3) !== "abs";
        });

        removeFromCount(absence);

        delete absences[absence.absenceId];
        $absence.remove();

        if (cell.children.length) {
            var $datas = cell.children[0].children;

            cellClasses.push("abs");
            if ($datas[1].textContent.trim().substr(-3) === "Oui") {
                cellClasses.push('abs-justified');
            }
            cellClasses.push('abs-' + $datas[2].textContent.toLowerCase());
        }
        cell.className = cellClasses.join(' ');
    }

    /**
     * Choose to create or modify an absence.
     * @param cell The cell that contains the absence
     * @param array The interface used to create the absence (newAbsence.morning or newAbsense.afternoon)
     * @param originalAbsence The old absence if it's a modification
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
     * @param cell The cell to which belongs the absence
     * @param absence The absence to be added or modified
     * @return {boolean} true if the operation was successful, false otherwise
     */
    function sendAbsence(cell, absence) {
        if (!absence) return false;

        var data = {
            studentId: absence.student.id,
            beginDate: absence.time.begin.format(DateFormat.SQL),
            endDate: absence.time.end.format(DateFormat.SQL),
            absenceTypeId: absence.absenceType.value,
            justified: absence.justified ? '1' : '0'
        };
        var url = '/Process_secretariat/';

        if (absence.absenceId) {
            url += 'modifier_absence';
            data.absenceId = absence.absenceId;
        } else {
            url += 'ajout_absence';
        }

        $.post(url, data, function(data) {
            // If success
            if (data.match(/^success [0-9]*$/)) {
                var oldCellClasses = cell.className.split(" ");
                cell.className = filterClasses(oldCellClasses, absence).join(" ");

                var $schedule,
                    $justified,
                    $absenceType;

                if (absence.absenceId) {
                    // If absence editition
                    var wasJustified = absences[absence.absenceId].justified;
                    absences[absence.absenceId] = absence;

                    if (absence.justified !== wasJustified) {
                        updateJustifiedCount(absence.student, absence.justified - wasJustified);
                    }

                    var $absence = document.getElementById('absn' + absence.absenceId);
                    $absence.className = "abs-" + absence.absenceType.name.toLowerCase();

                    // Fields to be modified
                    var children = $absence.children;
                    $schedule = children[0];
                    $justified = children[1];
                    $absenceType = children[2];
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
                    $schedule = div.appendChild(document.createElement('p'));
                    $justified = div.appendChild(document.createElement('p'));
                    $absenceType = div.appendChild(document.createElement('p'));

                    // if absence is in the morning, add it before the other absence
                    if (absence.time.begin.getUTCHours() <= 12) {
                        $(cell).prepend(div);
                    } else {
                        cell.appendChild(div);
                    }
                    addToCount(absence);
                }

                $schedule.textContent = 'Horaires : ' + absence.time.begin.format(DateFormat.SHORT_TIME)
                    + ' - ' + absence.time.end.format(DateFormat.SHORT_TIME);
                $justified.textContent = 'Justifiée : ' + (absence.justified ? 'Oui' : 'Non');
                $absenceType.textContent = absence.absenceType.name;

            } else if (data === 'cancel') {
                alert('Erreur interne : requête annulée');
            } else if (data === 'missing_data') {
                alert('Erreur interne : données manquantes');
            } else if(data === 'wrong_data') {
                alert('Erreur : Les données entrées ne sont pas correctes');
            } else if (data.substr(0, 9) === 'exception') {
                alert(data);
            }
        });

        return true;
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
        var $otherChild = $(htmlId).siblings('div:not(' + htmlId + ')');

        var newClass = "abs-" + absence.absenceType.name.toLowerCase();

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

            // if there's only one absence
            // or the other absence has same class
            cellClasses.push(!$otherChild.length || $otherChild.attr('class') === newClass
                ? newClass
                : "abs-several"
            );
        }
        // if adding
        else {
            var wasAbsenceCell = oldClasses.indexOf("abs") > -1;
            var wasJustifiedCell = oldClasses.indexOf("abs-justifiee") > -1;

            // if absence is justified and, if cell had an absence, was it justified
            if (absence.justified && (wasAbsenceCell ? wasJustifiedCell : true)) {
                cellClasses.push("abs-justifiee");
            }

            var notAbsenceClass = function(klass) {
                return klass === "abs-justifiee" || !klass.startsWith("abs-");
            };

            // if first absence
            // or new class already existed
            cellClasses.push(oldClasses.every(notAbsenceClass) || oldClasses.indexOf(newClass) > -1
                ? newClass
                : "abs-several"
            );
        }

        return cellClasses;
    }

    var newAbsence = {
        wrapper: $('#edition-wrapper'),
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
            delete: $('#am-delete')
        },
        afternoon: {
            activeAbsence: null,
            modified: false,
            content: $('#edition-afternoon'),
            timeSlot: $('#pm-time'),
            justified: $('#pm-justified'),
            absenceType: $('#pm-absenceType'),
            delete: $('#pm-delete')
        },
        submitButton: $('#edition-submit'),
        cancelButton: $('#edition-cancel'),

        edit: function(td) {
            var $absences = td.children;
            var amAbs = null,
                pmAbs = null;

            var student = getStudentFromRow(td.parentNode.rowIndex - 2);
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
                        absenceId = parseInt(($absences[1].id || '').substr(4));
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

            if (date.getUTCDay() === 5) { // Friday
                $pmTimes[0].textContent = '13h30 - 17h30';
                $pmTimes[1].textContent = '13h30 - 15h30';
                $pmTimes[2].textContent = '15h30 - 17h30';
            } else {
                $pmTimes[0].textContent = '14h00 - 18h00';
                $pmTimes[1].textContent = '14h00 - 16h00';
                $pmTimes[2].textContent = '16h00 - 18h00';
            }

            setInterfaceAbsence(this.morning, amAbs, 'morning');
            setInterfaceAbsence(this.afternoon, pmAbs, 'afternoon');
        },

        send: function() {
            var cell = getActive('cell')[0];
            var cellChildren = cell.children;
            var absence;
            var morningAbsence = isMorningAbsence(cellChildren[0]),
                afternoonAbsence = null;

            var handled = true;
            if (this.morning.modified) {
                if (morningAbsence === null || morningAbsence === false) {
                    handled = handleAbsence(cell, this.morning);
                }
                else {
                    absence = absences[cellChildren[0].id.substr(4)];
                    handled = handleAbsence(cell, this.morning, absence);
                }
            }

            if (this.afternoon.modified) {
                var index = morningAbsence ? (cellChildren.length === 2 ? 1 : -1) : 0;

                if (index > -1) {
                    afternoonAbsence = isMorningAbsence(cellChildren[index]);
                    if (afternoonAbsence !== null) {
                        afternoonAbsence = !afternoonAbsence;
                    }
                }

                if (index === -1 || afternoonAbsence === null) {
                    handled = handleAbsence(cell, this.afternoon);
                }
                else if (afternoonAbsence === true) {
                    absence = absences[cellChildren[index].id.substr(4)];
                    handled = handleAbsence(cell, this.afternoon, absence);
                }
            }

            if (handled) this.hide();
        },

        show: function() {
            this.wrapper.addClass('active');

            $(window).on('keydown', function(event) {
                if (event.keyCode === 27) {
                    newAbsence.hide();
                }
            });
        },

        hide: function() {
            this.wrapper.removeClass('active');

            $(window).off('keydown');

            this.morning.modified = false;
            this.afternoon.modified = false;
        }
    };

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


    /* ##### Per day absence creation, modification, deletion ##### */

    // ### NEW ABSENCE EVENTS ###
    // Morning
    newAbsence.morning.timeSlot.on('click', 'p', function() {
        newAbsence.morning.modified = true;
        activate("morning", this);
        activate("morningDelete", newAbsence.morning.delete);
    });

    newAbsence.morning.content.on('change', ':checkbox, select', function() {
        newAbsence.morning.modified = true;
        activate("morningDelete", newAbsence.morning.delete);
    });

    newAbsence.morning.delete.click(function() {
        var absence = newAbsence.morning.activeAbsence;
        if (absence === null) {
            setInterfaceAbsence(newAbsence.morning, null, 'morning');
            newAbsence.morning.modified = false;
        } else {
            $.post('/Process_secretariat/suppression_absence', {absenceId: absence.absenceId}, function(data) {
                if (data === 'success') {
                    setInterfaceAbsence(newAbsence.morning, null, 'morning');
                    deleteAbsence(absence);
                    newAbsence.morning.modified = false;
                } else if (data === 'missing_data') {
                    alert('Erreur de communication avec le serveur.' +
                        'Nous vous conseillons de rafraîchir la page !');
                } else if (data === 'unknown id') {
                    alert('Erreur de communication avec le serveur.' +
                        'Nous vous conseillons de rafraîchir la page !');
                } else if (data.substr(0, 9) === 'exception') {
                    alert(data);
                } else {
                    alert('Message inconnu reçu du serveur');
                }
            });
        }
    });

    // Afternoon
    newAbsence.afternoon.timeSlot.on('click', 'p', function() {
        newAbsence.afternoon.modified = true;
        activate("afternoon", this);
        activate("afternoonDelete", newAbsence.afternoon.delete);
    });

    newAbsence.afternoon.content.on('change', ':checkbox, select', function() {
        newAbsence.afternoon.modified = true;
        activate("afternoonDelete", newAbsence.afternoon.delete);
    });

    newAbsence.afternoon.delete.click(function() {
        var absence = newAbsence.afternoon.activeAbsence;
        if (absence === null) {
            setInterfaceAbsence(newAbsence.afternoon, null, 'afternoon');
            newAbsence.afternoon.modified = false;
        } else {
            $.post('/Process_secretariat/suppression_absence', {absenceId: absence.absenceId}, function(data) {
                if (data === 'success') {
                    setInterfaceAbsence(newAbsence.afternoon, null, 'afternoon');
                    deleteAbsence(absence);
                    newAbsence.afternoon.modified = false;
                } else if (data === 'missing_data') {
                    alert('Erreur de communication avec le serveur.' +
                        'Nous vous conseillons de rafraîchir la page !');
                } else if (data === 'unknown id') {
                    alert('Erreur de communication avec le serveur.' +
                        'Nous vous conseillons de rafraîchir la page !');
                } else if (data.substr(0, 9) === 'exception') {
                    alert(data);
                } else {
                    alert('Message inconnu reçu du serveur');
                }
            });
        }
    });

    // Footer
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
