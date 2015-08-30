$(function(){
    var pnResultRan = $("#pnResultRan").detach();
    var pnResultTdv = $("#pnResultTdv").detach();
    var pnResult = $("#pnResult");
    var ddlType = $("#ddlType");
    var rbResult = $("#rbResult");
    var rbNoResult = $("#rbNoResult");
    function isTdv() {
        return ddlType.val() == "1";
    }

    function showResult() {
        if ( isTdv() ) {
            rbResult.button("option","label","Rendu");
            rbNoResult.button("option","label","Non rendu");
            pnResultTdv.appendTo(pnResult).show();
            pnResultRan.detach();
        } else {
            rbResult.button("option","label","Présent");
            rbNoResult.button("option","label","Absent");
            pnResultRan.appendTo(pnResult).show();
            pnResultTdv.detach();
        }
    }
    ddlType.change(function(){
        showResult();
    });

    $("#pnHasResult").buttonset();
    showResult();

    rbNoResult.change(function(){
        pnResult.slideUp();
    });
    rbResult.change(function(){
        pnResult.slideDown();
    });
    if ( rbNoResult.is(":checked") ) {
        pnResult.hide();
    }
    $("#bSubmit").click(function(){
        if ( !ddlType.val() ) {
            alert("Veuillez sélectionner un type de travail");
            ddlType.selectmenu("widget").focus().click();
            return false;
        }
        if ( !$("#ddlSubject").val() ) {
            alert("Veuillez sélectionner un cours");
            $("#ddlSubject").selectmenu("widget").focus().click();
            return false;
        }
        if ( rbResult.is(":checked") ) {
            if ( isTdv() ) {
                var result = $("#tbResult").val().trim();
                if ( !result ) {
                    alert("Veuillez entrer une note");
                    $("#tbResult").focus();
                    return false;
                }
                if ( !/[0-9]{1,3}/.test(result) || parseInt(result) > 100 ) {
                    alert("La note doit être sur 100");
                    $("#tbResult").focus();
                    return false;
                }
            } else if ( !$("#ddlResult").val() ) {
                alert("Veuillez sélectionner une note");
                $("#ddlResult").selectmenu("widget").focus().click();
                return false;
            }
        }
    });
});
