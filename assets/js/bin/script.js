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

    function Notification(id, content, notifType, icon, storageType) {
        this.id = id;
        this.content = content;
        this.icon = icon;
        this.storageType = storageType;
    }

    var notifications = {};
    var toastInstance;
    var notifCount;

    // Notifications
    $.post('/notification/get_notifications', function(server_notifications) {
        notifications = server_notifications;

        createNotifications(server_notifications);
        notifCount = Object.keys(notifications).length;

        createToast();


        $(document).on('click', '#toast-container .toast li', deleteNotif);
    });

    function createNotifications(notifs) {
        $.each(notifs, function(index, element) {
            notifications[index] = new Notification(
                element.id,
                element.content,
                element.notifType,
                element.icon,
                element.storageType
            );
        });
    }

    function createToast() {
        var $toastContent = $('<div></div>');

        if (notifCount !== 0) {
            var $list = $('<ul style="display: none;"></ul>');

            $.each(notifications, function (index, notif) {
                var $notifContent = $('<li></li>')
                    .prop('id', 'notif-' + index)
                    .addClass('notif-' + notif.type)
                    .append('<i class="material-icons">' + notif.icon + '</i>')
                    .append('<span>' + notif.content + '</span>');

                $list.append($notifContent);
            });

            $toastContent
                .append(
                    $('<div class="right-align"></div>')
                        .append('<span id="notif-counter">'
                            + 'Vous avez ' + notifCount + ' notification' + (notifCount > 1 ? 's' : '')
                            + '</span>'
                        )
                        .append('<i class="material-icons right arrow-rotate">keyboard_arrow_left</i>')
                        .click(function() {
                            $(this).siblings('ul').slideToggle();
                            $(this).find('i').toggleClass('rotate');
                        })
                )
                .append($list);

            Materialize.toast($toastContent, Infinity);
            toastInstance = $('.toast').last()[0].M_Toast;
        }
    }

    function deleteNotif() {
        $(this).fadeOut(function() {
            var notificationId = $(this).prop('id').substr(6);
            var notification = notifications[notificationId];
            $(this).remove();

            notifCount--;
            if (notifCount > 0) {
                $('#notif-counter').text('Vous avez ' + notifCount + ' notification' + (notifCount > 1 ? 's' : ''));
            } else {
                toastInstance.remove();
            }

            switch (notification.storageType) {
                case 'session':
                    break;
                case 'database':
                    break;
            }

            $.post(
                '/notification/remove_notification/',
                {
                    notifId: notification.id,
                    storageType: notification.storageType
                }
            );
        });
    }
});
