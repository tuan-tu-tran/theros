$(function(){
    var applyFilter;
    var selectedType;
    $("#rbTypeAll, #rbTypeTdv, #rbTypeRan").change(function(){
        selectedType=this.value;
        applyFilter();
    }).filter(":checked").each(function(){
        selectedType = this.value;
    });

    var pnHasResult=$("#pnHasResult");
    var resultDoneType = null;
    var _setResultDoneType = function(value){
        if (value === "") {
            resultDoneType = null;
        } else {
            resultDoneType = value == "1";
        }
        if(resultDoneType === false) {
            pnHasResult.hide();
        } else {
            pnHasResult.show();
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

    var selectedStudent=null;
    selectedStudent = $("#ddlStudent").combobox({resetOnInvalid:true, clear:true}).change(function(){
        selectedStudent = this.value;
        applyFilter();
    }).val();

    var selectedClass = null;
    selectedClass = $("#ddlClass").combobox({resetOnInvalid:true, clear:true, size:"auto"}).change(function(){
        selectedClass = this.value;
        applyFilter();
    }).val();

    var selectedTeacher = null;
    selectedTeacher = $("#ddlTeacher").combobox({resetOnInvalid:true, clear:true, size:"auto"}).change(function(){
        selectedTeacher = this.value;
        applyFilter();
    }).val();

    var selectedSubject = null;
    selectedSubject = $("#ddlSubject").combobox({resetOnInvalid:true, clear:true}).change(function(){
        selectedSubject = this.value;
        applyFilter();
    }).val();

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

    var lCount = $("#lCount");
    applyFilter=function(){
        var curClass="even";
        var otherClass="odd";
        var visibleCount = 0;
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
            visible = visible && (!selectedStudent || selectedStudent == "-1" || data.studentId == selectedStudent);
            visible = visible && (!selectedClass || selectedClass == "-1" || data.classId == selectedClass);
            visible = visible && (!selectedTeacher || selectedTeacher == "-1" || data.teacherId == selectedTeacher);
            visible = visible && (!selectedSubject || selectedSubject == "-1" || data.subjectId == selectedSubject);
            if(visible) {
                $this.show();
                $this.removeClass("even odd").addClass(curClass);
                var tmp = curClass;
                curClass = otherClass;
                otherClass = tmp;
                ++visibleCount;
            } else {
                $this.hide();
            }
        });
        lCount.text(visibleCount);
    }

    $("#gvResult").css("table-layout","fixed").find("thead th").each(function(){
        $(this).css("width",$(this).width()+10);
    });
    $("#pnFilters").css("opacity",1);

    applyFilter();
});
