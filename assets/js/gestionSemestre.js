$(function(){
  $('#tableSemestre button').click(function(e){
    let parent = $(e.target).parent();
    let grp;
    grp = prompt("Entrez le nom du groupe :");
    console.log(parent);
    alert('Ajout de ' + grp+' au semstre : ' );
  })
});
