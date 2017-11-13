$(document).ready(function() {
    'use strict';

    var list = $('#list-content');
    var students;

    var sort = {
        $sorter: null,
        field: 'idStudent',
        ascending: true,
        update: function(sorter) {
            var newField = $(sorter).data('sort');
            if (!this.$sorter) {
                this.$sorter = $(sorter);
            }

            if (this.field === newField) {
                var icon = this.$sorter.find('i');

                this.ascending = !this.ascending;
                icon.text(this._getIcon());

                students.sort(this._getSortFunction());
            }
            else {
                this.ascending = true;
                this.field = newField;
                students.sort(this._compare);

                if (this.$sorter) {
                    this.$sorter.find('i').addClass('scale-out');
                }

                this.$sorter = $(sorter);
                this.$sorter.find('i').removeClass('scale-out');

            }
        },
        _compare: function(el1, el2) {
            return el1[sort.field] > el2[sort.field] ? 1 : -1;
        },
        _compareInvert: function(el1, el2) {
            return el2[sort.field] >= el1[sort.field] ? 1 : -1;
        },
        _getSortFunction: function() {
            return this.ascending ? this._compare : this._compareInvert;
        },
        _getIcon: function() {
            return this.ascending ? 'keyboard_arrow_down' : 'keyboard_arrow_up';
        }
    };

    $.post(
        '/Process_Student/get_all', {},
        function (serverStudents) {
            students = serverStudents;

            $('#list-progress').remove();

            if (students.length) {
                var autocompleteDatas = {};

                $.each(students, function(index, student) {
                    autocompleteDatas[student.idStudent + ' ' + student.surname + ' ' + student.name] = null;
                    list.append(
                        $('<a href="/Student/profile/' + student.idStudent + '"></a>')
                            .addClass('black-text')
                            .append(
                                $('<div class="row"></div>')
                                    .append('<div class="col s4">' + student.idStudent + '</div>')
                                    .append('<div class="col s4">' + student.surname + '</div>')
                                    .append('<div class="col s4">' + student.name + '</div>')
                            )
                    )
                });

                $('#search').autocomplete({
                    data: autocompleteDatas,
                    limit: 20,
                    onAutocomplete: function(val) {
                        location.href = '/Student/profile/' + val.split(' ').slice(0, 1);
                    }
                });

                $('#list-student').on('click', '.sorter', function() {
                    sort.update(this);
                    updateList();
                });
            } else {
                list.append('<p class="center-align">Pas d\'étudiant à afficher</p>')
            }
        }
    );

    function updateList() {
        list.empty();
        $.each(students, function(index, student) {
            list.append(
                $('<a href="/Student/profile/' + student.idStudent + '"></a>')
                    .addClass('black-text')
                    .append(
                        $('<div class="row"></div>')
                            .append('<div class="col s4">' + student.idStudent + '</div>')
                            .append('<div class="col s4">' + student.surname + '</div>')
                            .append('<div class="col s4">' + student.name + '</div>')
                    )
            )
        });
    }
});
