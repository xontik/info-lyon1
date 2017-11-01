$(function(){

  $(".deleter").click(function (e){
      console.log("delete");
      return (window.confirm("Etes vous sur de vouloir supprimer cet étudiant"));
  });
});
