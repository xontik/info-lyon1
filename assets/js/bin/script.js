$(function() {
    "use strict";

    /* General initialization */
    $("select").material_select();

    $(".button-collapse").sideNav({
        draggable: true
    });

    // header
    $("#nav-user-button").dropdown({
        constrainWidth: false,
        belowOrigin: true
    });

    $('#m-nav-user-button').dropdown({
        belowOrigin: true
    });

    // Notifications
    $.post('/user/get_notifications', function(notifications) {

        $(document).on('click', '#toast-container .toast', function() {
            $(this).fadeOut(function() {
                $(this).remove();
                $.post(
                    '/user/remove_notification/',
                    { notifId: $(this).children('span').prop('id').substr(5) }
                );
            });
        });

        $.each(notifications, function(index, notif) {
            var $toastContent = $('<i class="material-icons">' + notif.icon + '</i>')
                .add('<span id="notif' + index + '">' + notif.content + '</span>');

            Materialize.toast($toastContent, Infinity, 'notif-' + notif.type);
        });

    });
});
