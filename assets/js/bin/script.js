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
        var prefix = 'notif_';
        var prefixLen = prefix.length;
        var notifCount = Object.keys(notifications).length;

        var toastInstance;

        // Delete notifications
        $(document).on('click', '#toast-container .toast li', function() {
            $(this).fadeOut(function() {
                $(this).remove();

                notifCount--;
                if (notifCount > 0) {
                    $('#notif-counter').text('Vous avez ' + notifCount + ' notification' + (notifCount > 1 ? 's' : ''));
                } else {
                    toastInstance.remove();
                }

                $.post(
                    '/user/remove_notification/',
                    { notifId: $(this).prop('id').substr(prefixLen) }
                );
            });
        });

        var $toastContent = $('<div></div>');

        if (notifCount !== 0) {
            var $list = $('<ul style="display: none;"></ul>');

            $.each(notifications, function (index, notif) {
                var $notifContent = $('<li></li>')
                    .prop('id', prefix + index)
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
    });
});
