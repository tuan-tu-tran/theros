$(function(){
    $("#bClear")
        .button({
            icons:{primary:"ui-icon-closethick"},
            text:false
        })
        .hide()
        .click(function(){
            $("#tbFilter").val("").focus().trigger("keyup");
        })
        .parent().buttonset();

    $("#works").css("table-layout","fixed").find("th").each(function(){
        $(this).css("width",$(this).width()+10);
    });
    var items=$("#works tr:not(:first)");
    items.each(function(i){
        $(this).removeClass("odd even").addClass(i%2?"odd":"even");
    });


    var initText = "par nom, par classe, par cours, par type, ...";
    function getText(item){
        return $(item).text();
    }
    function onSearch(e){
        if(e.text){
            $("#bClear").show();
        } else {
            $("#bClear").hide();
        }
    }
    function onFiltered(e){
        $("#lCountVisible").text(e.count);
        $("#lCountTotal").text(e.total);
        if(e.count){
            $("#works").show();
        } else {
            $("#works").hide();
        }
        Cookies.set("searchFilter", e.text, {path:""});
    }
    function _clear()
    {
        $(this).val("");
        $(this).off("focusin",_clear);
    }
    var enabled=false;
    $("#tbFilter")
        .prop("autocomplete","off")
        .focusin(_clear)
        .focusin(function(){
            enabled = true;
        })
        .focusout(function(){
            enabled=false;
        })
        .val(initText)
        .focusout(function(){
            if(!$(this).val()){
                $(this).val(initText).focusin(_clear);
            }
        })
        .keyup(function(e){
            if(!enabled){
                return;
            }
            var search=$(this).val().trim();
            onSearch({
                text:search
            })
            var terms=search.split(/\s+/g);
            terms=$(terms).filter(function(){ return this!="";}).map(function(i,s){ return RegExp(s,"i"); });
            console.log("match terms "+terms.toArray());
            var count=0;
            items.each(function(){
                var text=getText(this);
                var show;
                if(!terms.length){
                    show=true;
                } else {
                    $(terms).each(function(){
                        if (!this.test(text)) {
                            show=false;
                            return false;
                        } else {
                            show=true;
                        }
                    });
                }
                if (show) {
                    $(this).removeClass("odd even").addClass(count%2?"odd":"even");
                    ++count;
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            onFiltered({
                text:search,
                count:count,
                total:items.length
            });
        })
        .change(function(){
            console.log("change");
        })
    ;
    $("button.delete").button().tooltip({
        content:"Supprimer le résultat encodé",
        items:"*"
    }).click(function(event){
        if ( !confirm("Etes-vous sûr de vouloir supprimer le résultat encodé pour ce travail ?") ) {
            event.stopPropagation();
            event.preventDefault();
            $(this).tooltip("close");
            return false;
        }
    });

    var searchFilter = Cookies.get("searchFilter");
    if(searchFilter){
        $("#tbFilter").focus().val(searchFilter).trigger("keyup");
    }
});
