$(function(){
    $("#pnHasResult").buttonset();
    $("#rbNoResult").change(function(){
        $("#pnResult").slideUp();
    });
    $("#rbResult").change(function(){
        $("#pnResult").slideDown();
    });
    if ( $("#rbNoResult").is(":checked") ) {
        $("#pnResult").hide();
    }
    $("#bSubmit").click(function(){
        if ( !$("#ddlType").val() ) {
            alert("Veuillez sélectionner un type de travail");
            $("#ddlType").focus();
            return false;
        }
        if ( !$("#ddlSubject").val() ) {
            alert("Veuillez sélectionner un cours");
            $("#ddlSubject").selectmenu("widget").focus().click();
            return false;
        }
        if ( $("#rbResult").is(":checked") ) {
            if ( $("#ddlType").val() == "1" ) {
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
                $("#ddlResult").focus();
                return false;
            }
        }
    });
});
