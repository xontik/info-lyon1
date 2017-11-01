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
        var notificationId = $(this).data('notif-id');

        if (notificationId) {
            // Session or seen notification
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

            var link = $(this).data('notif-link').toString();
            if (link) {
                window.location.href = link
            }

            $('.notif[data-notif-id="' + notificationId + '"]').fadeOut(function() {
                $(this).remove();
            });

            // Wait until animation end
            setTimeout(function() {
                if (--notificationCount <= 0) {
                    var $mobileNotifications = $('#m-notifications');
                    // Change icon
                    $('.notifications-icon').html('notifications_none');

                    // Auto-close dropdown / modal
                    $('.dropdown-button[data-activates="nav-notifications"]').dropdown('close');
                    $mobileNotifications.modal('close')

                    // Append "no result" text
                    setTimeout(function() {
                        $('#nav-notifications').append('<li><p>Pas de notifications</p></li>');
                        $mobileNotifications.find('div.collection')
                            .append('<div class="collection-item">Pas de notifications</div>')
                    }, 200);
                }
            }, 400);
        } else {
            // Page notification
            $(this).fadeOut(function() {
                $(this).remove();
            });
        }

        // Prevent notification menu from closing
        event.stopPropagation();
    }
});
