$(document).ready(function() {

    $('.expandable .card-title i')
        .first().html('expand_less');

    $('.card-content  > .row')
        .hide()
        .first().show();

    $(document).on('click', '.expandable .card-title i', function(e) {
            var $el = $(e.target);
            $el.closest('.card')
                .find('.row')
                .first()
                .toggle(300);

            if ($el.html() === 'expand_less') {
                $el.html('expand_more');
            } else {
                $el.html('expand_less');
            }
        });

});
