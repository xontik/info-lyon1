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
        weekdaysLetter: weekdays.map(function(e) { return e.substr(0, 3) }),
        firstDay: 1,
        today: 'Aujourd\'hui',
        clear: 'Effacer',
        close: 'Fermer',
        format: 'dd/mm/yyyy',
        closeOnSelect: true
    });

    var modalPromo = $('#modalPromo');
    var typeControl = $('#typeControle');
    var saveTCVal;

    modalPromo.modal({
        dismissible: true
    });

    // if ds promo page creation
    if (location.href.split('/').slice(-1)[0].toLowerCase().substr(0, 5) === 'promo') {
        saveTCVal = '1'; // Promo

        typeControl.on('change', function () {
            saveTCVal = $(this).val();
            if (saveTCVal !== '1') {
                // Confirm change
                modalPromo.modal('open');
            }
        });

        $('#promoRedirect').on('click', function () {
            location.href = '/Control/add';
        });
    } else {
        saveTCVal = '2'; // Group

        typeControl.on('change', function () {
            saveTCVal = $(this).val();
            if (saveTCVal === '1') {
                // Confirm change
                modalPromo.modal('open');
            }
        });

        $('#promoRedirect').on('click', function () {
            location.href = '/Control/add/promo';
        });
    }

    $('#promoNoRedirect').on('click', function () {
        typeControl.val(saveTCVal);
        typeControl.material_select();
    });
});