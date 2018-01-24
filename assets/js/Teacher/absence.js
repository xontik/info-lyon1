$(document).ready(function() {
    'use strict';

    var studentList = $('#studentList');
    var now = new Date();
    var time = $('#time').text().split('-').map(function(s) {
        return now.getUTCFullYear()
            + '-' + (now.getUTCMonth() + 1)
            + '-' + now.getUTCDate()
            + ' '
            + s.trim() + ':00';
    });

    studentList.on('click', '> div', function(event) {
        var $el = $(this);
        var checkbox = $el.find(':checkbox');
        var checked = checkbox.prop('checked');

        if (checked) {
            $.post(
                '/Process_Absence/add',
                {
                    studentId: checkbox.data('student-id'),
                    beginDate: time[0],
                    endDate: time[1],
                    absenceTypeId: 1,
                    justified: false
                }
            )
                .done(function(data) {
                    if (/^success/.test(data)) {
                        checkbox.prop('checked', false);
                        checkbox.data('absence-id', data.substr(8));
                    } else {
                        Materialize.toast('Une erreur s\'est produite', 4000, 'notif notif-danger');
                    }
                })
                .fail(function(jqXHR, status, errorThrown) {
                    console.log(status, errorThrown);
                });
        } else {
            $.post(
                '/Process_Absence/delete',
                { absenceId: checkbox.data('absence-id')}
            )
                .done(function(data) {
                    if (data === 'success') {
                        checkbox.prop('checked', true);
                    } else {
                        Materialize.toast('Une erreur s\'est produite', 4000, 'notif notif-danger');
                    }
                })
                .fail(function(jqXHR, status, errorThrown) {
                    console.log(status, errorThrown);
                });
        }

        event.preventDefault();
    });
});
