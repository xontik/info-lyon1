$(document).ready(function() {
    var TUin = $("#TUin");
    var TUout = $("#TUout");
    var course = $("#futureCourseId");

    var checkboxId = 0;

    course
        .change(function() {
            $.ajax({
                dataType: 'json',
                data: {'courseId': course.val()},
                url: '/Process_Administration/get_teaching_units',
                type: 'POST',
                success: function(data) {
                    console.log(data);
                    TUin.find('.collection-item').remove();
                    TUout.find('.collection-item').remove();

                    $.each(data['in'], function(key, TU) {
                        TUin.append(
                            $('<li></li>')
                                .addClass('collection-item')
                                .addClass('clickable')
                                .data('tu-id', TU.idTeachingUnit)
                                .append(
                                    $('<div></div>')
                                        .append($('<span></span>')
                                            .text(
                                                TU.creationYear
                                                + ' ' + TU.teachingUnitCode
                                                + ' - ' + TU.teachingUnitName
                                            )
                                            .addClass('clickable')

                                        )
                                        .append($('<div></div>')
                                            .addClass('secondary-content')
                                            .append('<input id="' + ++checkboxId + '" type="checkbox">')
                                            .append($('<label></label>')
                                                .prop('for', checkboxId)
                                            )
                                        )
                                )
                                .addClass('clickable')

                        )
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
                                .addClass('clickable')
                        )
                    });
                }
            });
        })
        .change();

    $("#add").click(function(e) {
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
                type: 'POST',
                success: function(data) {
                    $.each(data, function(key, val) {
                        TUout.find('.collection-item')
                            .filter(function() {
                                return $(this).data('tu-id') === val
                            })
                            .detach()
                            .appendTo(TUin);
                    });
                }
            });
        }
    });

    $("#remove").click(function(e) {
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
                type: 'POST',
                success: function(data) {
                    $.each(data, function(key, val) {
                        TUin.find('.collection-item')
                            .filter(function() {
                                return $(this).data('tu-id') === val
                            })
                            .detach()
                            .appendTo(TUout);
                    });
                }
            });
        }
    });

    $(document).on('click','.clickable',function(e) {
        var checkbox = $(this).find('input');
        checkbox.prop("checked", !checkbox.prop('checked'));
    });


    $('#delete').submit(function(e) {
        return window.confirm("Êtes-vous sûr de vouloir supprimer ce parcours ?");
    });

    var courseToAddSemester = $("#courseId");
    var selectYear = $('#schoolYear');

    courseToAddSemester
        .change(function() {
            $.ajax({
                dataType: 'json',
                data: {'courseId': courseToAddSemester.val()},
                url: '/Process_Course/get_year',
                type: 'POST',
                success: function(data) {
                    selectYear.empty();
                    for (var i = 0; i < 3; i++) {
                        selectYear.append($(
                            '<option value="'+(data.year + i) +'">'+(data.year + i)+' - '+(data.year + i + 1 )+'</option>'));
                    }
                    selectYear.material_select();
                }
            });
        }).change();
});
