$(function(){
    var items=$("#works tr:not(:first)");
    items.each(function(i){
        $(this).removeClass("odd even").addClass(i%2?"odd":"even");
    });


    var initText = "par nom, par classe, par cours, par type, ...";
    function getText(item){
        return $(item).text();
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
            var terms=$(this).val().trim().split(/\s+/g);
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
        })
        .change(function(){
            console.log("change");
        })
    ;
});
