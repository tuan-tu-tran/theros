$(function(){
    $("input:checkbox").switchButton({
        labels_placement:"right",
        on_label:"OUI",
        off_label:"NON",
    }).change(function(e){
        var $this=$(this);
        var checked=$this.is(":checked");
        var id=$this.siblings("input[name='hfTeacherId']").val();
        var parentRow = $this.parents("tr").first();
        parentRow.css("cursor","wait");
        var siblings=$this.siblings();
        siblings.css("pointer-events","none");
        $.ajax({
            method:"POST",
            error:function(){
                alert("Une erreur s'est produite sur le serveur");
                location.reload(true);
            },
            success:function(){
                parentRow.css("cursor","");
                siblings.css("pointer-events","auto");
            },
            data:{
                id:id,
                admin:checked
            }
        });
    });
});
