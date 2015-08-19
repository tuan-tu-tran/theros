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

            //setup the filter
            function filter(){
                var text=$("#tbFilter").val().toLowerCase().trim();
                var patterns=$(text.split(/\s+/));
                $("#subject tr").each(function(i, row){
                    var rowText = $(row).text().toLowerCase().trim();
                    var match;
                    if(!text || $(row).hasClass("selected")){
                        match=true;
                    }else{
                        match=false;
                    }
                    patterns.each(function(i, p){
                        if(rowText.indexOf(p) >= 0){
                            match=true;
                            return false;
                        }
                    });
                    if (match){
                        $(row).show();
                    }else{
                        $(row).hide();
                    }
                });
            };
            $("#tbFilter").keyup(filter);
        });
    });
});
