$(function() {
    "use strict";

    $('.datepicker').pickadate({
        selectMonth: true,
        today: 'Aujourd\'hui',
        clear: 'Effacer',
        close: 'Fermer',
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
            location.href = '/Professeur/addControle/';
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
            location.href = '/Professeur/addControle/Promo';
        });
    }

    $('#promoNoRedirect').on('click', function () {
        typeControl.val(saveTCVal);
        typeControl.material_select();
    });
});