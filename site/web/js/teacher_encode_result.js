$(function(){
    $("#pnHasResult").buttonset();
    $("#rbNoResult").change(function(){
        $("#pnResult").slideUp();
    });
    $("#rbResult").change(function(){
        $("#pnResult").slideDown();
    });
});