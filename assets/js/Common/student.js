$(document).ready(function() {
    'use strict';

    $.post(
        '/Process_Student/get_all', {},
        function (students) {
            var list = $('#list-content');
            $('#list-progress').remove();

            if (students.length) {
                var studentsData = {};

                $.each(students, function(index, student) {
                    studentsData[student.idStudent + ' ' + student.surname + ' ' + student.name] = null;
                    list.append(
                        $('<tr></tr>')
                            .append('<td>' + student.idStudent + '</td>')
                            .append('<td>' + student.surname + '</td>')
                            .append('<td>' + student.name + '</td>')
                    )
                });

                $('#search').autocomplete({
                    data: studentsData,
                    limit: 20,
                    onAutocomplete: function(val) {
                        location.href = '/Student/profile/' + val.split(' ').slice(0, 1);
                    }
                });
            } else {
                list.append('<tr><td colspan="3">Pas d\'étudiant à afficher</td></tr>')
            }
        }
    );
});
