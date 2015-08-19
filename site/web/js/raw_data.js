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
                var patterns=$(text.split(/\s+/)).filter(function(i,p){ return p.length > 1});
                var shown=0;
                var total=0;
                $("#subject tr").each(function(i, row){
                    total+=1;
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
                    shown+=match;
                    if (match){
                        $(row).show();
                    }else{
                        $(row).hide();
                    }
                });
                $("#lFilterCounts").text(shown+"/"+total+" affichés");
            };
            $("#tbFilter").keyup(filter);

            //setup the clear button
            $("#pbClear").click(function(){
                $("#tbFilter").val("").focus();
                filter();
            });

            //autofill the filter
            var initFilter = $("#hRawDesc").val()
                .toLowerCase()
                .replace(/[\.,\(\):\-0-9]/g," ")
                .replace(/\s+/g," ")
                .replace(/\ble\b/g,"")
                .replace(/\bsi\b/g,"")
                .replace(/\brn\b/g,"")
                .replace(/\bràn\b/g,"")
                .replace(/\brem [àa] n\b/g,"")
                .replace(/\bremise à niveau\b/g,"")
                .replace(/\brem [aà] niv\b/g,"")
                .replace(/\btrav\b/g,"")
                .replace(/\btravail de vacances\b/g,"")
                .replace(/\blecture\b/g,"francais")
                .replace(/\borthographe\b/g,"francais")
                .replace(/\bortho\b/g,"francais")
                .replace(/\bee\b/g,"francais")
                .replace(/\bexpr écrite\b/g,"francais")
                .replace(/\bfr\b/g,"francais")
                .replace(/\bedm\b/g,"milieu")
                .replace(/\bmaths\b/g,"math")
                .replace(/\bndl?s\b/g,"neerlandais")
                .replace(/\ban\b/g,"anglais")
                .replace(/\s+/g," ")
                .replace(/[éè]/g,"e")
                .replace("ç","c")
                .trim()
            ;
            $("#tbFilter").val(initFilter);
            filter();
        });
    });
});
