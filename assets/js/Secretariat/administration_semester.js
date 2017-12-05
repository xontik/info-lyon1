$(document).ready(function() {
    'use strict';

    var semesterId = $('#group-semester').data('semester-id');

    var draggableOptions = {
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
    };

    var droppableOptions = {
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
    };

    var sortableOptions = {
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
        receive: function(event, ui) {
            var $uiItem = $(ui.item);
            var oldGroupId = $uiItem.data('group-id');

            // if there are no more students in old group
            if (ui.sender.find('li').length === 1) { // there is : header only
                ui.sender.append(
                    '<li class="collection-item no-student">Aucun élève</li>'
                );
            }

            // if there was no student in the new group
            if ($uiItem.parent().find('li').length === 3) { // there is : header, li.no-student and the new item
                $uiItem.parent().find('.no-student').remove();
            }

            // update the group-id
            $uiItem.data('group-id', $uiItem.parent().data('group-id'));

            // if deleting (=> moving to "students without group")
            if ($uiItem.data('group-id') === 0) {
                $uiItem.find('a').remove();
                $.ajax({
                    dataType: 'json',
                    url: '/Process_Group/delete_student'
                    + '/' + oldGroupId
                    + '/' + $uiItem.data('studentId')
                    + '/' + semesterId,
                    data: {'ajax': true},
                    type: 'POST'
                })
                    .done(function(data) {
                        if (data.type === 'danger') {
                            window.location.reload();
                        } else {
                            Materialize.toast(data.text, 4000, data.type);
                        }
                    })
                    .fail(function(data) {
                        window.location.reload();
                    });

            // if inserting into group
            } else {
                //if comming from an other group dont add trash
                if (oldGroupId === 0) {
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
                        'studentId': $uiItem.data('studentId')
                    },
                    url: '/Process_Group/add_student/' + semesterId,
                    type: 'POST'
                })
                    .done(function(data) {

                        if (data.type === 'danger') {
                            window.location.reload();
                        }else{
                            Materialize.toast(data.text,4000,data.type);
                        }
                    })
                    .fail(function(data) {
                        window.location.reload();
                    });

            }

        }
    };

    $('.connectedSortable').sortable(sortableOptions);
    $('#education-association').find('td:not(:first-child)').droppable(droppableOptions);

    $.post('/Process_Teacher/get_teachers_subjects/' + semesterId)
        .done(function(data) {
            var $teachers = $('#teachers');
            var $search = $('#searchTeacher');

            var teachers = data.teachers;
            var teachersNoSubject = data.teachersNoSubject;
            var subjects = data.subjects;

            var autocomplete = {};

            var createGroupTeachers = function(groupedTeachers) {
                var $el = $('<ul></ul>')
                    .addClass('collapsible-body collection no-padding');

                $.each(groupedTeachers, function(index, teacherId) {
                    var teacher = teachers[teacherId];
                    $el.append(
                        $('<li class="collection-item"></li>')
                        .text(teacher.name + ' ' + teacher.surname)
                        .attr('data-teacher-id', teacherId)
                    )
                });
                return $el;
            };

            $.each(subjects, function(index, subject) {
                var $el = $('<li></li>')
                    .append(
                        $('<div></div>')
                            .addClass('collapsible-header valign-wrapper tooltipped')
                            .text(subject.subjectName)
                            .tooltip({
                                tooltip: subject.subjectCode + ' ' + subject.moduleName,
                                delay: 300
                            })
                    )
                    .append(createGroupTeachers(subject.teachers));

                $teachers.append($el);
            });

            if (teachersNoSubject) {
                $teachers.append(
                    $('<li></li>')
                        .append('<div class="collapsible-header">Pas de matières enseignées</div>')
                        .append(createGroupTeachers(teachersNoSubject))
                );
            }

            $teachers.find('.collection-item').draggable(draggableOptions);
        })
        .fail(function(jqXHR, status, errorThrown) {
            console.log(status, errorThrown, jqXHR.responseText);
            Materialize.toast('Impossible de charger les professeurs', Infinity, 'notif notif-danger');
        });

});
