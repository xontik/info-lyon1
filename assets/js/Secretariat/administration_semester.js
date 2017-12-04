$(document).ready(function() {
    'use strict';

    var semesterId = $('#group-semester').data('semester-id');

    $('#teachers').find('.collection-item').draggable({
        containment: '#education-wrapper',
        appendTo: '#education-wrapper',
        scroll: true,
        helper: 'clone',
        cursor: 'move',
        cursorAt: function(event, ui) {
            return {
                top: ui.item.outerHeight() / 2,
                left: ui.item.outerWidth() / 2
            }
        }
    });

    $('#education-association').find('td:not(:first-child)').droppable({
        accept: '[data-teacher-id]',
        drop: function(event, ui) {
            var $target = $(event.target);
            var groupId = $target.data('group-id');
            var subjectId = $target.data('subject-id');
            var teacherId = ui.draggable.data('teacher-id');

            if (groupId === 'all') {
                $.post(
                    '/Process_Education/set_teacher_all/' + semesterId,
                    {
                        teacherId: teacherId,
                        subjectId: subjectId
                    }
                )
                        .done(function() {
                            $target.siblings()
                                .find('i')
                                .html('person')
                                .attr('data-tooltip', $('[data-teacher-id=' + teacherId + ']').text())
                                .tooltip();
                        })
                        .fail(function(jqXHR, status, errorThrown) {
                            console.log(status, errorThrown, jqXHR.responseText);
                            Materialize.toast('Une erreur s\'est produite', 4000, 'notif notif-danger');
                        });
            } else {
                $.post(
                    '/Process_Education/set_teacher/' + semesterId,
                    {
                        teacherId: teacherId,
                        groupId: groupId,
                        subjectId: subjectId
                    }
                )
                    .done(function() {
                        $target.find('i').html('person')
                            .attr('data-tooltip', $('[data-teacher-id=' + teacherId + ']').text())
                            .tooltip();
                    })
                    .fail(function(jqXHR, status, errorThrown) {
                        console.log(status, errorThrown, jqXHR.responseText);
                        Materialize.toast('Une erreur s\'est produite', 4000, 'notif notif-danger');
                    });
            }
        }
    });

    var sortableParams = {
        connectWith: '.connectedSortable',
        opacity: 0.7,
        tolerance: 'pointer',
        items: '.collection-item',
        cursor: 'move',
        placeholder: {
            element: function(currentItem) {
                return $('<li class="collection-item placeholder"></li>')[0];
            },
            update: function(container, p) {}
        },
        receive: function(event, ui ) {
            var $uiItem = $(ui.item);

            //from
            if (ui.sender.find('li').length == 1) { // 1 -> header only
                ui.sender.append(
                    $('<li>Aucun élève</li>')
                        .addClass('collection-item')
                        .addClass('no-student')
                );
            }
            var oldGrp = $uiItem.data('group-id');

            //update the group-id
            $uiItem.data('group-id', $uiItem.parent().data('group-id'));


            //to
            if ($uiItem.parent().find('li').length == 3) { // 3 -> header | li no student | new item
                $uiItem.parent().find('.no-student').remove();
            }

            //if deleting
            if ($uiItem.data('group-id') == 0) {
                $uiItem.find('a').remove();
                $.ajax({
                    dataType: 'json',
                    url: '/Process_Group/delete_student'
                        + '/' + oldGrp
                        + '/' + $uiItem.data('studentId')
                        + '/' + semesterId,
                    data: {'ajax' : true},
                    type: 'POST',
                    success: function(data) {
                        if (data.type == 'danger') {
                            window.location.reload();
                        } else {
                            Materialize.toast(data.text, 4000, data.type);
                        }
                    },
                    error: function(data) {
                        window.location.reload();
                    }
                });

            //if inserting into group
            } else {
                //if comming from an other group dont add trash
                if (oldGrp == 0) {
                    $uiItem.find('div').prepend(
                        $('<a>')
                            .attr('href',
                                '/Process_Group/delete_student'
                                + '/' + $uiItem.data('group-id')
                                + '/' + $uiItem.data('student-id')
                                + '/' + semesterId
                            )
                            .append(
                                $('<i>')
                                    .addClass('material-icons')
                                    .html('delete')
                            )
                    );
                }

                $.ajax({
                    dataType: 'json',
                    data: {
                        'groupId': $uiItem.data('group-id'),
                        'studentId':  $uiItem.data('studentId')
                    },
                    url: '/Process_Group/add_student/' + semesterId,
                    type: 'POST',
                    success: function(data) {

                        if (data.type == 'danger') {
                            window.location.reload();
                        }else{
                            Materialize.toast(data.text,4000,data.type);
                        }
                    },
                    error: function(data) {
                        window.location.reload();
                    }

                });
            }

        }
    };
    
    $('.connectedSortable').sortable(sortableParams);

});
