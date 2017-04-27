$(function () {

    $( window ).resize(function()
    {
        var offset = ( $('#header_profile').width() - $('#header_profile > ul').width() ) / 2;
        $('#header_profile > ul').css( 'left', offset + 'px' );
    });
    $( window ).trigger('resize');

    $('#header_profile').click( function() {
       $( this ).children('ul').slideToggle(100);
   });

});