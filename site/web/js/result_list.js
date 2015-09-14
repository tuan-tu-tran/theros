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

    applyFilter();
});
