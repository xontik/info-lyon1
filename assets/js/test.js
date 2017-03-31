$(function (){
    $("html").keypress(function (e){
        if(e.keyCode == 100){
            $("body > div:first-child").toggle();
        }

    });
});