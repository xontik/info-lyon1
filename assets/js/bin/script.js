$(function() {
    'use strict';

    /* General initialization */
    $('select').material_select();

    $('.button-collapse').sideNav({
        draggable: true
    });

    // header
    $('.hide-on-med-and-down .dropdown-button').dropdown({
        constrainWidth: false,
        belowOrigin: true
    });

    $('.hide-on-large-only .dropdown-button').dropdown({
        belowOrigin: true
    });

    var notificationCount = $('.notif').length;

    $(document).on('click', '.notif', deleteNotif);

    // Notifications
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
            $(this).remove();

            var notificationId = $(this).prop('id');

            if (notificationId) {
                if (--notificationCount === 0) {
                    // Set icon to 'notifications_none'
                    $('a[data-activates="nav-notifications"] i').html('&#xE7F5;');
                    $('#nav-notifications')
                        .append('<li><p>Pas de notifications</p></li>');
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
                    notifId: parseInt(notificationId.substr(6)),
                    storage: storage
                };

                $.post('/notification/remove_notification/', data);
            }
        });

        // Prevent notification menu from closing
        event.stopPropagation();
    }
});
