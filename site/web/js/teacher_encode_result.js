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
});
