$(document).ready(function() {
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

    // Confirm modal
    $('form[data-confirm]').submit(confirm);
    $('a[data-confirm]').click(confirm);

    function confirm() {
        return window.confirm(this.getAttribute('data-confirm'));
    }

    // Notifications
    var notificationCount = $('.notif').length / 2;
    var notificationWrappers = $('.notification-wrapper');

    $(document).on('click', '.notif', deleteNotif);
    $.post('/notification/get_alerts', function(alerts) {
        generateToasts(alerts);
    });

    function generateToasts(alerts) {
        $.each(alerts, function (index, notif) {
            var $toastContent = '<i class="material-icons">' + notif.icon + '</i>'
                + '<span>' + notif.content + '</span>';

            Materialize.toast($toastContent, notif.duration, 'notif notif-' + notif.type);
        });
    }

    function deleteNotif(event) {
        var notificationId = $(this).data('notif-id');

        if (notificationId) {

            --notificationCount;
            notificationWrappers
                .find('.badge')
                .text(notificationCount);

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

            var link = $(this).data('notif-link');
            if (link) {
                window.location.href = link.toString();
            }

            $('.notif[data-notif-id="' + notificationId + '"]').fadeOut(function() {
                $(this).remove();
            });

            // Wait until animation end
            setTimeout(function() {
                if (notificationCount <= 0) {
                    var $mobileNotifications = $('#m-notifications');
                    // Change icon
                    notificationWrappers.find('i').html('notifications_none');
                    notificationWrappers.children('.badge').fadeOut(function() {
                        this.remove();
                    });

                    // Auto-close dropdown / modal
                    $('.dropdown-button[data-activates="nav-notifications"]').dropdown('close');
                    $mobileNotifications.modal('close');

                    // Append "no result" text
                    setTimeout(function() {
                        $('#nav-notifications').append('<li><p>Pas de notifications</p></li>');
                        $mobileNotifications.find('.collection')
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
