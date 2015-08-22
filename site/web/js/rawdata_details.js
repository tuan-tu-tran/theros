var treatedUrl;

$(function(){
        //setup click on subject
        $("#subject tr").click(function(){
            var text=$(this).find("td").map(function(){return $(this).text();}).toArray().join(" - ");
            $("#selectedSubjectText").text(text).show();
            $(this).addClass("selected").siblings().removeClass("selected");
            $(this).find("input")[0].checked = true;
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
            .replace(/\bfgs\b/g,"format.geog")
            .replace(/\s+/g," ")
            .replace(/[éè]/g,"e")
            .replace("ç","c")
            .trim()
        ;
        $("#tbFilter").val(initFilter);
        filter();

        //setup highlight of selected type
        $("#pnType input").change(function(){
            $(this).parent().addClass("selected").siblings().removeClass("selected");
        });

        //preselect right type if possible
        var rawDesc=$("#hRawDesc").val();
        rawDesc=rawDesc
            .toLowerCase()
            .replace(/[\.,\(\):\-0-9]/g," ")
            .replace(/[éè]/g,"e")
            .replace("ç","c")
            .replace(/\s+/g," ")
            .trim()
        ;
        var isRan = false;
        var isTdv = false;
        function matches(relist){
            var match=false;
            $(relist).each(function(i, re){
                if(re.test(rawDesc)){
                    match=true;
                    return false;
                }
            });
            return match;
        };
        var isRan = matches([
            /\brn\b/g,
            /\bràn\b/g,
            /\brem [àa] n\b/g,
            /\bremise à niveau\b/g,
            /\brem [aà] niv\b/g,
        ]);
        var isTdv = matches([
            /\btrav\b/g,
            /\btravail( de vacances)?\b/g,
        ]);
        if(isRan && !isTdv)
        {
            $("#rbRan").click().change();
        }else if (isTdv && !isRan){
            $("#rbTdv").click().change();
        }

        //setup the add button
        $("#bAdd").click(function(){
            var selectedSubject=$("#hfSelectedSubject").val();
            if($("#pnType :checked").length == 0){
                alert("Veuillez sélectionner un type");
                return false;
            } else if($("#subject :checked").length==0){
                alert("Veuillez sélectionner une branche et un professeur");
                return false;
            }
        });

        //setup the delete button
        $("#fsWorks td.delete").hover(function(){
            $(this).parent().addClass("selected");
        }, function(){
            $(this).parent().removeClass("selected");
        });
});
