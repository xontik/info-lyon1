$(function() {

    // #header_profile
    $('#header_profile').click( function() {
        $( this ).children('ul').slideToggle(100);
    });

    // #notifications
    $('#notifications').click( function() {
        var notifs = $(this).children('.notif');
        notifs.first().remove();

        if ( notifs.length <= 1 ) {
            $('#notifications').remove();
        }
    });

});
