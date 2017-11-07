$(document).ready(function() {

  $(".deleter").click(function(e) {
      return window.confirm("Êtes-vous sûr de vouloir supprimer cet étudiant ?");
  });


  $("td i[data-group-id]").click(function (e) {
      var subject = $("#subjectId");
      console.log($(e.target).data('group-id'));
      //TODO selectionner le bon subject et group et faire briller
  })
});
