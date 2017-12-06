$(document).ready(function() {
    'use strict';

    var TUin = $('#TUin');
    var TUout = $('#TUout');
    var course = $('#futureCourseId');

    var checkboxId = 0;

    course
        .change(function() {
            $.ajax({
                dataType: 'json',
                data: {'courseId': course.val()},
                url: '/Process_Administration/get_teaching_units',
                type: 'POST'
            })
                .done(function(data) {
                    TUin.find('.collection-item').remove();
                    TUout.find('.collection-item').remove();

                    $.each(data['in'], function(key, TU) {
                        TUin.append(
                            $('<li></li>')
                                .addClass('collection-item')
                                .data('tu-id', TU.idTeachingUnit)
                                .append(
                                    $('<div></div>')
                                        .append($('<span></span>')
                                            .text(
                                                TU.creationYear
                                                + ' ' + TU.teachingUnitCode
                                                + ' - ' + TU.teachingUnitName
                                            )
                                        )
                                        .append($('<div></div>')
                                            .addClass('secondary-content')
                                            .append('<input id="' + ++checkboxId + '" type="checkbox">')
                                            .append($('<label></label>')
                                                .prop('for', checkboxId)
                                            )
                                        )
                                )
                        );
                    });

                    $.each(data['out'], function(key, TU) {
                        TUout.append(
                            $('<li></li>')
                                .addClass('collection-item')
                                .data('tu-id', TU.idTeachingUnit)
                                .append(
                                    $('<div></div>')
                                        .append($('<span></span>')
                                            .text(TU.creationYear
                                                + ' ' + TU.teachingUnitCode
                                                + ' - ' + TU.teachingUnitName)
                                        )
                                        .append($('<div></div>')
                                            .addClass('secondary-content')
                                            .append('<input id="' + ++checkboxId + '" type="checkbox">')
                                            .append($('<label></label>')
                                                .prop('for', checkboxId)
                                            )
                                        )
                                )
                        );
                    });
                })
                .fail(function(jqXHR, status, errorThrown) {
                    console.log(status, errorThrown, jqXHR.responseText);
                    Materialize.toast('Une erreur est survenue', 4000, 'notif notif-danger');
                });
        })
        .change();

    $('#add').click(function() {
        var ids = [];
        TUout
            .children('.collection-item')
            .has(':checkbox:checked')
            .each(function(index, element) {
                ids.push($(element).data('tu-id'));
            });

        if (ids && ids.length > 0) {
            $.ajax({
                dataType: 'json',
                data: {'courseId': course.val(), 'TUids': ids},
                url: '/Process_Course/add_teaching_unit',
                type: 'POST'
            })
                .done(function(data) {
                    $.each(data, function(key, val) {
                        TUout.find('.collection-item')
                            .filter(function() {
                                return $(this).data('tu-id') === val;
                            })
                            .detach()
                            .appendTo(TUin);
                    });
                })
                .fail(function(jqXHR, status, errorThrown) {
                    console.log(status, errorThrown, jqXHR.responseText);
                    Materialize.toast('Une erreur est survenue', 4000, 'notif notif-danger');
                });
        }
    });

    $('#remove').click(function() {
        var ids = [];
        TUin
            .children('.collection-item')
            .has(':checkbox:checked')
            .each(function(index, element) {
                ids.push($(element).data('tu-id'));
            });

        if (ids && ids.length > 0) {
            $.ajax({
                dataType: 'json',
                data: {'courseId': course.val(), 'TUids': ids},
                url: '/Process_Course/remove_teaching_unit',
                type: 'POST'
            })
                .done(function(data) {
                    $.each(data, function(key, val) {
                        TUin.find('.collection-item')
                            .filter(function() {
                                return $(this).data('tu-id') === val;
                            })
                            .detach()
                            .appendTo(TUout);
                    });
                })
                .fail(function(jqXHR, status, errorThrown) {
                    console.log(status, errorThrown, jqXHR.responseText);
                    Materialize.toast('Une erreur est survenue', 4000, 'notif notif-danger');
                });
        }
    });

    $(document).on('click', '.collection-item', function() {
        var checkbox = $(this).find(':checkbox');
        checkbox.prop('checked', !checkbox.prop('checked'));
    });

    var $courseToAddSemester = $('#courseId');
    var $selectYear = $('#schoolYear');

    $courseToAddSemester
        .change(function() {
            $.ajax({
                dataType: 'json',
                data: {'courseId': $courseToAddSemester.val()},
                url: '/Process_Course/get_year',
                type: 'POST'
            })
                .done(function(data) {
                    $selectYear.empty();
                    for (var i = 0; i < 3; i++) {
                        $selectYear.append(
                            '<option value="'+(data.year + i) +'">'
                            + (data.year + i)+' - '+(data.year + i + 1 )
                            + '</option>'
                        );
                    }
                    $selectYear.material_select();
                })
                .fail(function(jqHXR, status, errorThrown) {
                    console.log(status, errorThrown, jqHXR.responseText);
                    Materialize.toast('Une erreur est survenue', 4000, 'notif notif-danger');
                });
        })
        .change();
});
