$(function (){
    $("html").keypress(function (e){
        if(e.keyCode == 100){
            $("#debug").toggle();
        }

    });
});