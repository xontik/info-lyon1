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
    $(document)
        .on('click', 'a[data-confirm]', confirm)
        .on('submit', 'form[data-confirm]', confirm);

    function confirm() {
        return window.confirm(this.getAttribute('data-confirm'));
    }

    // Notifications
    var notificationCount = $('.notif').length / 2;
    var notificationWrappers = $('.notification-wrapper');

    $(document).on('click', '.clear-notif', clearNotifs);
    $(document).on('click', '.notif', deleteNotif);
    $.post('/notification/get_alerts').done(generateToasts);

    function generateToasts(alerts) {
        $.each(alerts, function (index, notif) {
            var $toastContent = '<i class="material-icons white-text">' + notif.icon + '</i>'
                + '<span>' + notif.content + '</span>';

            Materialize.toast($toastContent, notif.duration, 'notif notif-' + notif.type);
        });
    }

    function deleteNotif(event) {
        var el = $(this);
        var notificationId = el.data('notif-id');

        if (notificationId) {

            --notificationCount;
            notificationWrappers
                .find('.badge')
                .text(notificationCount);

            // Session or seen notification
            var storage;
            if (el.hasClass('notif-session')) {
                storage = 'session';
            } else if (el.hasClass('notif-seen')) {
                storage = 'seen';
            } else {
                return;
            }

            $.post('/notification/remove_notification', {
                notifId: parseInt(notificationId),
                storage: storage
            })
                .done(function() {
                    var link = el.data('notif-link');
                    if (link) {
                        location.href = link.toString();
                    }
                })
                .fail(function(jxHQR, status, errorThrown) {
                    console.log(status, errorThrown);
                });

            $('.notif[data-notif-id="' + notificationId + '"]').fadeOut(function() {
                this.remove();

                if (notificationCount <= 0) {
                    resetNotifUI();
                }
            });

        } else {
            // Page notification
            el.fadeOut(function() {
                el.remove();
            });
        }

        // Prevent notification menu from closing
        event.stopPropagation();
    }

    function clearNotifs() {
        $.post('/notification/remove_all')
            .done(function() {
                $('[data-notif-id], .clear-notif, .notification-wrapper .badge').fadeOut(function() {
                    this.remove();
                });

                resetNotifUI();
            })
            .fail(function(jxHQR, status, errorThrown) {
                console.log(status, errorThrown);
            });
    }

    function resetNotifUI() {
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
        $('#nav-notifications').append('<li><p>Pas de notifications</p></li>');
        $mobileNotifications.find('.collection')
            .append('<div class="collection-item">Pas de notifications</div>');
    }
});
