$(document).ready(function() {
    'use strict';

    var list = $('#list-content');

    var students = {};
    var groups = {};
    var years = {};

    var acValToId = {'group' : {}};
    var displayStudent = {};

    var sort = {
        init: function() {
            if (this.$sorter) {
                // Remove former sorter icon
                this.$sorter.find('i').addClass('scale-out');
            }

            this.$sorter = $('.sorter').first();
            this._field = this.$sorter.data('sort');
            this._ascending = true;

            this.$sorter.find('i')
                .text(this._getIcon())
                .removeClass('scale-out');

            displayStudent = students;
            displayStudent.sort(this._getSortFunction());
            updateList();
        },
        update: function(sorter) {
            var newField = sorter.data('sort');

            if (this._field === newField) {
                var icon = this.$sorter.find('i');

                this._ascending = !this._ascending;
                icon.text(this._getIcon());

                displayStudent.sort(this._getSortFunction());
            }
            else {
                this.$sorter.find('i').addClass('scale-out');

                this._ascending = true;
                this._field = newField;
                displayStudent.sort(this._compare);

                sorter.find('i')
                    .text(this._getIcon())
                    .removeClass('scale-out');
                this.$sorter = sorter;
            }
        },
        _compare: function(el1, el2) {
            return el1[sort._field] > el2[sort._field] ? 1 : -1;
        },
        _compareInvert: function(el1, el2) {
            return el2[sort._field] >= el1[sort._field] ? 1 : -1;
        },
        _getSortFunction: function() {
            return this._ascending ? this._compare : this._compareInvert;
        },
        _getIcon: function() {
            return this._ascending ? 'keyboard_arrow_down' : 'keyboard_arrow_up';
        }
    };

    $.post('/Process_Student/get_all')
        .done(function (resources) {
            students = resources.students;
            groups = resources.groups;
            years = resources.years;

            $('#list-progress').remove();

            if (students.length) {
                var autocompleteDatas = {};

                $.each(students, function(index, student) {
                    autocompleteDatas[student.idStudent + ' ' + student.surname + ' ' + student.name] = null;
                });
                sort.init();

                $.each(groups, function(index, group) {
                    var val = group.groupName + ' ' + group.schoolYear;
                    acValToId.group[val] = group.idGroup;
                    autocompleteDatas[val] = null;
                });

                $.each(years, function (year) {
                    autocompleteDatas[year] = null;
                });

                $('#search')
                    .on('keydown', function(event) {
                        if (event.keyCode === 8 && this.value) {
                            this.value = '';
                            displayStudent = students;
                            sort.init();
                            updateList();
                        }
                    })
                    .autocomplete({
                        data: autocompleteDatas,
                        limit: 15,
                        onAutocomplete: function(val) {
                            var fword = val.split(' ')[0];

                            // If student
                            if (/^p\d{7}$/.test(fword)) {
                                location.href = '/Student/profile/' + fword;
                            }
                            // If group
                            else if (/^G\d+S[0-4]$/.test(fword)) {
                                displayStudent = groups[acValToId.group[val]].students;
                                updateList();
                            }
                            // If year
                            else if (/^\d{4}-\d{4}/) {
                                displayStudent = years[fword];
                                updateList();
                            }
                        }
                    });

                $('#list-student').on('click', '.sorter', function() {
                    sort.update($(this));
                    updateList();
                });
            } else {
                list.append('<p class="center-align">Pas d\'étudiant à afficher</p>');
            }
        })
        .fail(function(jqXHR) {
            console.log(jqXHR.responseText);
        });

    function updateList() {
        list.empty();
        $.each(displayStudent, function(index, student) {
            list.append(
                $('<a href="/Student/profile/' + student.idStudent + '"></a>')
                    .addClass('black-text')
                    .append(
                        $('<div class="row"></div>')
                            .append('<div class="col s4">' + student.idStudent + '</div>')
                            .append('<div class="col s4">' + student.surname + '</div>')
                            .append('<div class="col s4">' + student.name + '</div>')
                    )
            );
        });
    }
});
