$(function(){
    $("div#raw_data tr").click(function(){
        location.href=$(this).find("input").val();
    });

    var cssTreated=$("#cssTreated");
    $("#cbTreatedToo").change(function(){
        var checked=$(this).is(":checked");
        if(checked){
            cssTreated.detach();
        }else{
            cssTreated.appendTo("head");
        }
    });
});
