$(function () {

    // nav hover
    // $('header > nav li').hover(
    //     function() {
    //         var params = {'height': '4em', 'line-height': '4em'};
    //         $( this ).animate(params, 100);
    //         $( this ).next('li').animate(params, 100);
    //     },
    //     function() {
    //         var params = {'height': '3.5em', 'line-height': '3.5em'};
    //         $( this ).animate(params, 100);
    //         $( this ).next('li').animate(params, 100);
    //     }
    // );

    // #header_profile
    $('#header_profile').click( function() {
        $( this ).children('ul').slideToggle(100);
    });

    $( window ).resize(function()
    {
        var offset = ( $('#header_profile').width() - $('#header_profile > ul').width() ) / 2;
        $('#header_profile > ul').css( 'left', offset + 'px' );
    });
    $( window ).trigger('resize');

});