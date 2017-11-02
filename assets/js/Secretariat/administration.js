$(document).ready(function() {
    var UEin = $("#UEin");
    var UEout = $("#UEout");
    var parcours = $("#parcours");

    parcours
        .change(function() {
            $.ajax({
                dataType: 'json',
                data: {'idParcours': parcours.val()},
                url: '/Process_Administration/get_UEs',
                type: 'POST',
                success: function(data) {
                    UEin.find('.collection-item').remove();
                    UEout.find('.collection-item').remove();

                    $.each(data['in'], function(key, UE) {
                        UEin.append(
                            $('<li></li>')
                                .addClass('collection-item')
                                .data('ue-id', UE.idUE)
                                .append(
                                    $('<div></div>')
                                        .append(UE.anneeCreation + ' ' + UE.codeUE + ' - ' + UE.nomUE)
                                        .append($('<div></div>')
                                            .addClass('secondary-content')
                                            .append('<input id="linked' + key + '" type="checkbox">')
                                            .append($('<label></label>')
                                                .prop('for', 'linked' + key)
                                            )
                                        )
                                )
                        )
                    });

                    $.each(data['out'], function(key, UE) {
                        UEout.append(
                            $('<li></li>')
                                .addClass('collection-item')
                                .data('ue-id', UE.idUE)
                                .append(
                                    $('<div></div>')
                                        .append(UE.anneeCreation + ' ' + UE.codeUE + ' - ' + UE.nomUE)
                                        .append($('<div></div>')
                                            .addClass('secondary-content')
                                            .append('<input id="available' + key + '" type="checkbox">')
                                            .append($('<label></label>')
                                                .prop('for', 'available' + key)
                                            )
                                        )
                                )
                        )
                    });
                }
            });
        })
        .change();

    $("#add").click(function(e) {
        var ids = [];
        UEout
            .children('.collection-item')
            .has(':checkbox:checked')
            .each(function(index, element) {
                ids.push($(element).data('ue-id'));
            });

        if (ids && ids.length > 0) {
            $.ajax({
                dataType: 'json',
                data: {'idParcours': parcours.val(), 'idUEs': ids},
                url: '/Process_Parcours/add_UE',
                type: 'POST',
                success: function(data) {
                    console.log(data);
                    $.each(data, function(key, val) {
                        UEout.find('.collection-item')
                            .filter(function() {
                                return $(this).data('ue-id') === val
                            })
                            .detach()
                            .appendTo(UEin);
                    });
                }
            });
        }
    });

    $("#remove").click(function(e) {
        var ids = [];
        UEin
            .children('.collection-item')
            .has(':checkbox:checked')
            .each(function(index, element) {
                ids.push($(element).data('ue-id'));
            });

        if (ids && ids.length > 0) {
            $.ajax({
                dataType: 'json',
                data: {'idParcours': parcours.val(), 'idUEs': ids},
                url: '/Process_Parcours/remove_UE',
                type: 'POST',
                success: function(data) {
                    $.each(data, function(key, val) {
                        UEin.find('.collection-item')
                            .filter(function() {
                                return $(this).data('ue-id') === val
                            })
                            .prop('id', 'available' + val)
                            .detach()
                            .appendTo(UEout);
                    });
                }
            });
        }
    });

    $('#delete').submit(function(e) {
        return window.confirm("Êtes-vous sûr de vouloir supprimer ce parcours ?");
    });

});
