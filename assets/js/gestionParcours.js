$(function (){
    var UEin = $("#UEin");
    var UEout = $("#UEout");

    $("#parcours").change(function(){
      //console.log("send" + $("#parcours").val());

      $.ajax({
        dataType: 'json',
        data: {'idParcours':$("#parcours").val()},
        url: '/Process_secretariat/getUEs',
        success: function(d){
          UEin.find('option').remove();
          UEout.find('option').remove();
          $.each(d,function(key,ues){
            $.each(ues,function(k,value){
              if(key == 'in'){
                UEin.append('<option value="'+value.idUE+'">'+value.anneeCreation+' '+value.codeUE+' - '+value.nomUE+'</option>')
              }else{
                UEout.append('<option value="'+value.idUE+'">'+value.anneeCreation+' '+value.codeUE+' - '+value.nomUE+'</option>')
              }
            });
          });
          //console.log(d);
        }
      });
    });

    $("#parcours").change();

    $("#add").click(function(e){
      var ids = UEout.val();
        if(ids.length > 0){
          $.ajax({
            dataType: 'json',
            data: {'idParcours':$("#parcours").val(), 'idUEs': ids},
            url: '/Process_secretariat/addUEtoParcours',
            success: function(d){
              $.each(d,function(key,val){
                UEout.find('option').filter(function(i){ return this.value == val}).detach().appendTo('#UEin')
              });
              //
            }
          });
        }
    });
    $("#remove").click(function(e){
      var ids = UEin.val();
        if(ids.length > 0){
          $.ajax({
            dataType: 'json',
            data: {'idParcours':$("#parcours").val(), 'idUEs': ids},
            url: '/Process_secretariat/removeUEtoParcours',
            success: function(d){
              $.each(d,function(key,val){
                UEin.find('option').filter(function(i){ return this.value == val}).detach().appendTo('#UEout')
              });
              //
            }
          });
        }
    });

});
