$(document).ready(function() {
    "use strict";

    let studentList = $('#student-list');
    let listElement = $('<li></li>').addClass('collection-item');
    console.log('now');
    $.ajax({
        dataType: 'json',
        url: '/Process_Project/get_student_available/' + studentList.data('project-id'),
        type: 'POST',
        success: function(data) {

            $('input.autocomplete').autocomplete({
               data:data,
               limit: 20, // The max amount of results that can be shown at once. Default: Infinity.
               onAutocomplete: function(val) {
                 // Callback function when value is autcompleted.
               },
               minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
             });

             console.log(data);

        },
        error: function(data){
            console.log(data);
        },
    });


});
