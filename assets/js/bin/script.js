$(function() {
    'use strict';

    /* General initialization */
    $('select').material_select();

    $('.button-collapse').sideNav({
        draggable: true
    });

    $('.dropdown-button').dropdown({
        belowOrigin: true
    });

    $('.modal').modal({
        inDuration: 170,
        outDuration: 115
    });

    // Notifications
    var notificationCount = $('.notif').length / 2;

    $(document).on('click', '.notif', deleteNotif);
    $.post('/notification/get_alerts', function(alerts) {
        generateToasts(alerts);
    });

    function generateToasts(alerts) {
        $.each(alerts, function (index, notif) {
            var $toastContent = '<i class="material-icons">' + notif.icon + '</i>'
                + '<span>' + notif.content + '</span>';

            Materialize.toast($toastContent, Infinity, 'notif notif-' + notif.type);
        });
    }

    function deleteNotif(event) {
        $(this).fadeOut(function() {
            var notificationId = $(this).data('notif-id');

            if (notificationId) {
                // Session or seen notification
                $('.notif[data-notif-id="' + notificationId + '"]').remove();

                if (--notificationCount <= 0) {
                    $('.notifications-icon').html('notifications_none');
                    $('#nav-notifications').append('<li><p>Pas de notifications</p></li>');
                    $('#m-notifications').find('div.collection')
                        .append('<div class="collection-item">Pas de notifications</div>');
                }

                var storage;
                if ($(this).hasClass('notif-session')) {
                    storage = 'session';
                } else if ($(this).hasClass('notif-seen')) {
                    storage = 'seen';
                } else {
                    return;
                }

                var data = {
                    notifId: parseInt(notificationId),
                    storage: storage
                };

                $.post('/notification/remove_notification/', data);
            } else {
                // Page notification
                $(this).remove();
            }
        });

        // Prevent notification menu from closing
        event.stopPropagation();
    }
});
