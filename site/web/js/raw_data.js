var detailsUrl;

$(function(){
    $("div#raw_data tr").click(function(){
        var id=$(this).find("input").val();
        $.get(detailsUrl,{id:id}, function(data){
            $("div#details").html(data);
        });
    });
});
