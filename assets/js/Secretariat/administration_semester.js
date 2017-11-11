$(document).ready(function() {

    $(".deleter").click(function(e) {
      return window.confirm("Êtes-vous sûr de vouloir supprimer cet étudiant ?");
    });


    $("td i[data-group-id]").click(function (e) {
        var subject = $("#subjectId");
        var groupe = $("#groupId");
        var teacher = $("#teacherId");

        var groupId = $(e.target).data('group-id');
        var subjectId = $(e.target).data('subject-id');
        var teacherId = $(e.target).data('teacher-id');

        // console.log(groupId + ' ' + subjectId + ' ' + teacherId );

        subject.find(':selected').prop('selected',false);
        subject.find('option[value='+subjectId+']').prop('selected', true);
        subject.material_select();

        groupe.find(':selected').prop('selected',false);
        groupe.find('option[value='+groupId+']').prop('selected', true);
        groupe.material_select();

        teacher.find(':selected').prop('selected',false);
        teacher.find('option[value='+teacherId+']').prop('selected', true);
        teacher.material_select();

        
        $('html, body').animate({
            scrollTop: $("#assoctiationCard").offset().top - 100
        }, 200);

        $("#assoctiationCard").addClass('z-depth-5')

        setTimeout(function(){
            $("#assoctiationCard").removeClass('z-depth-5');
        },1000);



    })

});

$( function() {
    var semesterId = $('#group-semester').data('semester-id');

    var params = {
        connectWith: ".connectedSortable",
        opacity: 0.7,
        tolerance: "pointer",
        items: ".collection-item",
        cursor: "move",
        placeholder: {
            element: function(currentItem) {
                return $('<li class="collection-item placeholder"></li>')[0];
            },
            update: function(container, p) {
                return;
            }
        },
        receive: function(event, ui ) {

            //from
            if(ui.sender.find('li').length == 1){ // 1 -> header only
                ui.sender.append($('<li>Aucun élève</li>')
                                    .addClass('collection-item')
                                    .addClass('no-student'));
            }
            var oldGrp = ui.item.data('group-id');

            //update the group-id
            $(ui.item).data('group-id', $(ui.item).parent().data('group-id'));



            //to
            if(ui.item.parent().find('li').length == 3){ // 3 -> header | li no student | new item
                $(ui.item).parent().find('.no-student').remove();
            }

            //if deleting
            if(ui.item.data('group-id') == 0){
                ui.item.find('a').remove();
                $.ajax({
                    dataType: 'json',
                    url: '/Process_Group/delete_student/'+oldGrp+"/"+ui.item.data('studentId')+"/"+semesterId,
                    data: {'ajax' : true},
                    type: 'POST',
                    success: function(data) {
                        if(data.type == 'danger'){
                            window.location.reload();
                        } else {
                            Materialize.toast(data.text,4000,data.type);
                            ui.item
                        }
                    },
                    error: function(data){
                        window.location.reload();
                    }

                });
            //if inserting into group
            } else {
                //if comming from an other group dont add trash
                if(oldGrp == 0){
                    ui.item.find('div').prepend($('<a>')
                                                    .attr('href','/Process_Group/delete_student/'+ui.item.data('group-id')+'/'+ui.item.data('student-id')+'/'+semesterId)
                                                    .append($('<i>')
                                                                .addClass('material-icons')
                                                                .html('delete')
                                                            )
                                                );
                }

                $.ajax({
                    dataType: 'json',
                    data: {'groupId': ui.item.data('group-id'), 'studentId':  ui.item.data('studentId')},
                    url: '/Process_Group/add_student/'+semesterId,
                    type: 'POST',
                    success: function(data) {

                        if(data.type == 'danger'){
                            window.location.reload();
                        }else{
                            Materialize.toast(data.text,4000,data.type);
                        }
                    },
                    error: function(data){
                        window.location.reload();
                    }

                });
            }

        }
    }
    $( ".connectedSortable" ).sortable(params);

  } );
