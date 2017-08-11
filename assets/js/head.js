$(function() {

    // #header_profile
    $('#header_profile').click( function() {
        $( this ).children('ul').slideToggle(100);
    });

    // #notifications
    $('.notif').click( function() {
        $( this ).remove();

        var notifs = $( '#notifications' );
        if ( notifs.children().length <= 1 ) {
            notifs.remove();
        }
    });

});
