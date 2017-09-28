$(function() {

    var DEFAULT_ANIM_TIME = 100;
    var $absence_table = $('#absences_table');

    // Center #active_day
    $absence_table.find('.wrapper').each( function() {
        var active_day = $('#active_day');
        if (active_day.length) {
            this.scrollLeft = active_day.position().left - (window.innerWidth / 2);
        }
    });

    // Per day absence informations
    var selected = [];

    $absence_table.on('click', 'td[class^="abs-"]', function() {
        if (selected.indexOf(this) > -1) {
            var index = selected.indexOf(this);
            selected.splice(index, 1);
            $(this).children('div').hide(DEFAULT_ANIM_TIME);
        } else {
            selected.push(this);
            $(this).children('div').show(DEFAULT_ANIM_TIME);
        }
    })
    .on('mouseenter', 'td[class^="abs-"]', function() {
        $(this).children('div').show(DEFAULT_ANIM_TIME);
    })
    .on('mouseleave', 'td[class^="abs-"]', function() {
        if (selected.indexOf(this) === -1) {
            $(this).children('div').hide(DEFAULT_ANIM_TIME);
        }
    });

    // Student absence count
    $('#table_stud_list').on('click', 'p + img', function() {
        $(this).siblings('div').toggle(DEFAULT_ANIM_TIME);
    });

    // Thead fixation

    var thead_original_top = $('#absences_table').find('thead').position().top;
    var $fixedHeader = $('#header-fixed').append($absence_table.find('thead').clone());

    $(window).on('scroll', function() {
        var offset = $(this).scrollTop();
        if (offset >= thead_original_top && $fixedHeader.is(':hidden')) {
            $fixedHeader.show();
        } else if (offset < thead_original_top) {
            $fixedHeader.hide();
        }
    });
    // Simulate scroll so fixed thead appears if page is scrolled down enough at loading
    $(window).scroll();

    // Make thead follow horizontal scroll of the body
    var table_static_width = $('#table_static').outerWidth(true);
    $('#table-wrapper').on('scroll', function() {
        $fixedHeader.css('left', -this.scrollLeft + table_static_width);
    });
});
