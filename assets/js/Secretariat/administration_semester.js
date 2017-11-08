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

        if(teacherId){
            teacher.find(':selected').prop('selected',false);
            teacher.find('option[value='+teacherId+']').prop('selected', true);
            teacher.material_select();
        }

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
    $( ".collection" ).sortable({
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
            console.log('received : ' + ui.item.data('student-id') + ' from '+ ui.item.data('group-id'));
            //TODO ajax call here
            ui.item.data('group-id',ui.item.parent().data('group-id'));
        }
    });


  } );
