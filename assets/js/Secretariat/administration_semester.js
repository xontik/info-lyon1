$(document).ready(function() {
    'use strict';

    var $educationWrapper = $('#education-wrapper');
    var $teachers = $('#teachers');

    var semesterId = $('#group-semester').data('semester-id');

    var teachers, subjects;

    function showNextSubject(subjectId) {
        var subjectIndex;
        for (var i = 0; i < subjects.length; i++) {
            if (+subjects[i].idSubject === subjectId) {
                subjectIndex = i + 1;
                break;
            }
        }

        if (subjectIndex) {
            $teachers.collapsible('open', subjectIndex + 1);
        }
    }

    function handleTeachersData(data) {

        var teachersNoSubject;

        teachers = data.teachers;
        teachersNoSubject = data.teachersNoSubject;
        subjects = data.subjects;

        var createGroupTeachers = function(groupedTeachers) {
            var $el = $('<ul></ul>')
                .addClass('collapsible-body collection no-padding');

            $.each(groupedTeachers, function(index, teacherId) {
                var teacher = teachers[teacherId];
                $el.append(
                    $('<li class="collection-item"></li>')
                        .text(teacher.name + ' ' + teacher.surname)
                        .attr('data-teacher-id', teacherId)
                );
            });
            return $el;
        };

        $('<li data-teacher-id="0"></li>')
            .append('<div class="collapsible-header">Désassigner un professeur</div>')
            .draggable(draggableOptions)
            .appendTo($teachers);


        $.each(subjects, function(index, subject) {
            var $el = $('<li></li>')
                .append(
                    $('<div></div>')
                        .addClass('collapsible-header valign-wrapper tooltipped')
                        .text(subject.subjectName)
                        .tooltip({
                            tooltip: subject.subjectCode + ' ' + subject.moduleName,
                            delay: 400
                        })
                )
                .append(createGroupTeachers(subject.teachers));

            $teachers.append($el);
        });

        if (teachersNoSubject) {
            $teachers.append(
                $('<li></li>')
                    .append('<div class="collapsible-header">Pas de matières enseignées</div>')
                    .append(createGroupTeachers(Object.keys(teachersNoSubject)))
            );
        }
        $teachers.find('.collection-item').draggable(draggableOptions);

    }

    function handleDrop(event, ui) {

        var $target = $(event.target);
        var groupId = $target.data('group-id');
        var subjectId = $target.data('subject-id');
        var teacherId = ui.draggable.data('teacher-id');

        var teacher = teachers[teacherId];

        if (groupId === 'all') {
            $.post(
                '/Process_Education/set_teacher_all/' + semesterId,
                {
                    teacherId: teacherId,
                    subjectId: subjectId
                }
            )
                .done(function() {
                    if (teacher) {
                        $target.siblings()
                            .find('i')
                            .html('person')
                            .attr('data-tooltip', teacher.name + ' ' + teacher.surname)
                            .tooltip();

                        showNextSubject(+subjectId);
                    } else {
                        $target.siblings()
                            .find('i')
                            .html('error_outline')
                            .attr('data-tooltip', 'Assigner au groupe')
                            .tooltip();
                    }
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
                    if (teacher) {
                        $target
                            .find('i')
                            .html('person')
                            .attr('data-tooltip', teacher.name + ' ' + teacher.surname)
                            .tooltip();

                        if ($target.siblings('[data-teacher-id=0]').length === 0) {
                            showNextSubject(+subjectId);
                        }
                    } else {
                        $target
                            .find('i')
                            .html('error_outline')
                            .attr('data-tooltip', 'Assigner au groupe')
                            .tooltip();
                    }
                })
                .fail(function(jqXHR, status, errorThrown) {
                    console.log(status, errorThrown, jqXHR.responseText);
                    Materialize.toast('Une erreur s\'est produite', 4000, 'notif notif-danger');
                });
        }
    }

    function handleReceive(event, ui) {

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

        // if deleting (moving to "without group")
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
                .fail(function() {
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
                    } else {
                        Materialize.toast(data.text,4000,data.type);
                    }
                })
                .fail(function() {
                    window.location.reload();
                });

        }
    }

    var sortableOptions = {
        connectWith: '.connectedSortable',
        opacity: 0.7,
        tolerance: 'pointer',
        items: '.collection-item',
        cursor: 'move',
        placeholder: {
            element: function() {
                return $('<li class="collection-item placeholder"></li>')[0];
            },
            update: function() {}
        },
        receive: handleReceive
    };

    var draggableOptions = {
        containment: $educationWrapper,
        appendTo: $educationWrapper,
        scroll: true,
        helper: 'clone',
        cursor: 'move',
        cursorAt: function(event, ui) {
            return {
                top: ui.item.outerHeight() / 2,
                left: ui.item.outerWidth() / 2
            };
        }
    };

    var droppableOptions = {
        accept: '[data-teacher-id]',
        drop: handleDrop
    };

    $.post('/Process_Teacher/get_teachers_subjects/' + semesterId)
        .done(handleTeachersData)
        .fail(function(jqXHR, status, errorThrown) {
            console.log(status, errorThrown, jqXHR.responseText);
            Materialize.toast('Impossible de charger les professeurs', Infinity, 'notif notif-danger');
        });

    $('.connectedSortable').sortable(sortableOptions);
    $('#education-association').find('td:not(:first-child)').droppable(droppableOptions);

});
