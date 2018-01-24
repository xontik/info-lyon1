$(function() {
    "use strict";

    var months = [ 'Janvier', 'Février', 'Mars',
        'Avril', 'Mai', 'Juin', 'Juillet', 'Août',
        'Septembre', 'Octobre', 'Novembre', 'Décembre'
    ];

    var weekdays = [ 'Dimanche', 'Lundi', 'Mardi',
        'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'
    ];

    $('.datepicker').pickadate({
        selectMonth: true,
        labelMonthNext: 'Mois suivant',
        labelMonthPrev: 'Mois précédent',
        monthsFull: months,
        monthsShort: months,
        weekdaysFull: weekdays,
        weekdaysShort: weekdays,
        weekdaysLetter: weekdays.map(function(e) { return e.substr(0, 3); }),
        firstDay: 1,
        today: 'Aujourd\'hui',
        clear: 'Effacer',
        close: 'Fermer',
        format: 'dd/mm/yyyy',
        closeOnSelect: true
    });

    var modalPromo = $('#modalPromo');
    var controlType = $('#typeId');
    var CTValue;

    modalPromo.modal({
        dismissible: true
    });

    // if promo page
    if (location.href.split('/').slice(-1)[0].toLowerCase().substr(0, 5) === 'promo') {
        CTValue = '1'; // Promo

        controlType.on('change', function () {
            CTValue = $(this).val();
            if (CTValue !== '1') {
                // Confirm change
                modalPromo.modal('open');
            }
        });

        $('#promoRedirect').on('click', function () {
            location.href = '/Control/add';
        });
    } else {
        CTValue = '2'; // Group

        controlType.on('change', function () {
            CTValue = $(this).val();
            if (CTValue === '1') {
                // Confirm change
                modalPromo.modal('open');
            }
        });

        $('#promoRedirect').on('click', function () {
            location.href = '/Control/add/promo';
        });
    }

    $('#promoNoRedirect').on('click', function () {
        controlType.val(CTValue);
        controlType.material_select();
    });
});