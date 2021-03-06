$(document).ready(function() {
    "use strict";

    var studentList = $('#student-list');
    var listElement = $('<li></li>')
                        .addClass('collection-item')
                        .append($('<div></div>')
                                    .append($('<span></span>')
                                                .addClass('secondary-content')
                                                .append($('<a></a>')
                                                            .append($('<i></i>')
                                                                        .addClass('material-icons')
                                                                        .text('delete')
                                                                    )
                                                            .attr('data-confirm',"Etes-vous sur de vouloir supprimer cet étudiant ?")

                                                        )
                                            )
                                );



    $.ajax({
        dataType: 'json',
        url: '/Process_Project/get_student_available/',
        type: 'POST',
        success: function(data) {

            $('input.autocomplete').autocomplete({
               data:data,
               limit: 20,
               onAutocomplete: function(val) {

                 $.ajax({
                     dataType: 'json',
                     data: {'studentId': val.split(' ')[0], 'projectId': studentList.data('project-id')},
                     url: '/Process_Project/add_member/',
                     type: 'POST',
                     success: function(data) {
                         if (data.error) {
                             location.reload();
                         } else {

                            var firstLi = studentList.find('li').eq(1);

                            if (firstLi.data('no-one')  !== undefined) {
                                firstLi.remove();
                            }
                            var newListElement = listElement.clone();
                            studentList.append(newListElement);

                            var name = val.split(' ');
                            studentList.find('li').last().find('div').prepend(name[1] + ' ' +name[2]);
                            studentList.find('li').last().find('a').attr('href','/Process_Project/delete_member/'+studentList.data('project-id')+'/'+name[0]);
                            Materialize.toast('<i class="material-icons">done</i>Etudiant ajouté',  4000, 'notif-success');

                            $('#student').val('');
                         }

                     },
                     error: function(data){
                         console.log(data);
                         console.log('add error');
                     }
                 });
               },
               minLength: 1
             });


        },
        error: function() {
            console.log('retrieve data error');
        }
    });


});
