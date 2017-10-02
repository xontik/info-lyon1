$(function () {
  $("li").on("click", function (e) {

      e.stopPropagation();

      if(!$(e.target).is('img')){
        console.log(e.target);
        $(this).children('ul').toggle();
      }

  });

  $("#expand").click(function(e){
      $("#tree ul ul").show();

  });
  $("#restrain").click(function(e){
      $("#tree ul ul").hide();

  });

});
