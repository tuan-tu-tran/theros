$(function(){
    $("#bSubmit").click(function(){
        var pwd = $("#tbPassword").val(),
            conf=$("#tbConfirm").val();
        if (!pwd || !conf) {
            alert("Veuillez remplir tous les champs.");
            $("input:password").filter(function(){return !$(this).val();}).first().focus();
            return false;
        } else if (pwd != conf) {
            alert("Les mots de passes ne correspondent pas.");
            $("#tbConfirm").focus();
            return false;
        } else if (pwd.length < 6) {
            alert("Veuillez entrer au moins 6 caractères");
            return false;
        }
    });
});
