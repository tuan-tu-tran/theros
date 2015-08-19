var detailsUrl;

$(function(){
    $("div#raw_data tr").click(function(){
        if(!$(this).hasClass("selected")){
            $("tr.selected").removeClass("selected");
            $(this).addClass("selected");
        }
        var id=$(this).find("input").val();
        $.get(detailsUrl,{id:id}, function(data){
            $("div#details").html(data);
			$("#subject tr").click(function(){
				var text=$(this).find("td").map(function(){return $(this).text();}).toArray().join(" - ");
				$("#selectedSubjectText").text(text);
				$(this).siblings(".selected").removeClass("selected");
				$(this).addClass("selected");
			});
        });
    });
});
