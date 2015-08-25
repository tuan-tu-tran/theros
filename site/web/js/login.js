$(function(){
    $("#cbTeacher").prop("selectedIndex", -1).combobox();
    $("#bSubmit").button().click(function(){
        var teacher=$("#cbTeacher").val();
        if (!teacher) {
            alert("Veuillez entrer votre login");
            return false;
        } else if (!$("#tbPassword").val()) {
            alert("Veuillez entrer votre mot de passe");
            return false;
        }
    });
    $("body").show();
});
