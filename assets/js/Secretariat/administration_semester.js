$(document).ready(function() {

    $(".deleter").click(function(e) {
      return window.confirm("Êtes-vous sûr de vouloir supprimer cet étudiant ?");
    });


    $("td i[data-group-id]").click(function (e) {
        var subject = $("#subjectId");
        var groupe = $("#groupId");
        var groupId = $(e.target).data('group-id');
        var subjectId = $(e.target).data('subject-id');

        var s

        console.log(groupId + ' ' + subjectId );

        subject.find(':selected').prop('selected',false);
        subject.find('option[value='+subjectId+']').prop('selected', true);
        subject.material_select();

        groupe.find(':selected').prop('selected',false);
        groupe.find('option[value='+groupId+']').prop('selected', true);
        groupe.material_select();

        $('html, body').animate({
            scrollTop: $("#assoctiationCard").offset().top - 100
        }, 200);

        $("#assoctiationCard").addClass('z-depth-5')

        setTimeout(function(){
            $("#assoctiationCard").removeClass('z-depth-5');
        },1000);

    })
});
