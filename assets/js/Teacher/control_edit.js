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
});
