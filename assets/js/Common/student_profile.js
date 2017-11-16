$(document).ready(function() {
    var i = $(".expandable .card-title i");
    $('.card-content  > .row').hide();
    $('.card-content  > .row').first().show();
    i.first().html('expand_less');
    i.click(function(e){
        var row = $(e.target).parents('.card').find(".row").first();
        row.toggle(300)

        if ($(e.target).html() == 'expand_less'){
            $(e.target).html('expand_more');
        } else {
            $(e.target).html('expand_less');
        }
    });
});
