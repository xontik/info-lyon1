$(function(){
  $('#tableSemestre button').click(function(e){
    let parent = $(e.target).parent();

    alert('Ajout de ' + parent.find("input").val()+' au semstre : ')
  })
});
