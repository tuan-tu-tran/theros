$(function(){
    var applyFilter;
    var selectedType;
    $("#rbTypeAll, #rbTypeTdv, #rbTypeRan").change(function(){
        selectedType=this.value;
        applyFilter();
    }).filter(":checked").each(function(){
        selectedType = this.value;
    });

    var resultDoneType = null;
    var _setResultDoneType = function(value){
        if (value === "") {
            resultDoneType = null;
        } else {
            resultDoneType = value == "1";
        }
    }
    $("#rbResultDone, #rbResultNotDone, #rbResultDoneOrNot").change(function(){
        _setResultDoneType(this.value);
        applyFilter();
    }).filter(":checked").each(function(){
        _setResultDoneType(this.value);
    });

    var hasResultType;
    function _setHasResultType(value){
        if (value === "") {
            hasResultType = null;
        } else {
            hasResultType = value == "1";
        }
    }
    $("#rbResultYes, #rbResultNo, #rbResultAll").change(function(){
        _setHasResultType(this.value);
        applyFilter();
    }).filter(":checked").each(function(){
        _setHasResultType(this.value);
    });
    works=$(works);
    var worksById={};
    works.each(function(){
        worksById[this.id] = this;
    });
    var rows=$("tbody tr");
    rows.each(function(){
        var $this=$(this);
        var id=$this.children("input").val();
        var data = worksById[id];
        data.view = $this;
        $this.data("work", data);
    });

    applyFilter=function(){
        var curClass="even";
        var otherClass="odd";
        rows.each(function(){
            var $this=$(this);
            var data = $this.data("work");
            var visible = true;
            visible = visible && (!selectedType || data.type == selectedType);
            visible = visible && (resultDoneType === null || data.hasResult == resultDoneType);
            if (visible && hasResultType !== null) {
                if (resultDoneType === null) {
                    if (hasResultType) {
                        visible = !data.hasResult || data.result;
                    } else {
                        visible = !data.hasResult || !data.result;
                    }
                } else if (resultDoneType) {
                    if (hasResultType) {
                        visible = data.result;
                    } else {
                        visible = !data.result;
                    }
                }
            }
            if(visible) {
                $this.show();
                $this.removeClass("even odd").addClass(curClass);
                var tmp = curClass;
                curClass = otherClass;
                otherClass = tmp;
            } else {
                $this.hide();
            }
        });
    }

    $("#gvResult").css("table-layout","fixed").find("thead th").each(function(){
        $(this).css("width",$(this).width()+10);
    });

    applyFilter();
});
