/*OK*/function ajaxControls(){
    // -- affectations --
    var ajax   = $("section #ajax");
    var loader = ajax.find("#ajax-loader");
    var closer = ajax.find("#ajax-closer");
    message    = ajax.find("#ajax-message");
    idUser     = ajax.find("#idUser");
    
    // -- events --
    /*OK*/$(document).ajaxStart(function(){
        loader.css('display','block');
        closer.removeClass('btn-info');
        closer.addClass('disabled');
        message.css('display','none');
        message.html("<h4 class='ajax-error'>Internal error"
                    +"</h4><p>Timeout reached.</p>");
        ajax.modal({backdrop: false});
    });
    /*OK*/$(document).ajaxStop(function(){
        loader.toggle();
        closer.removeClass('disabled');
        closer.addClass('btn-info');
        message.toggle();
        message.append("<span style='color:red'><em>This window will"
                      +" be closed automatically in 5 seconds.</em><span>");
        setTimeout(function(){ ajax.modal('hide'); }, 5000);
    });
}
/*OK*/function ajaxOperator(data, callback){
    $.ajax({
        url     : "ajax.php",
        data    : data,
        datatype: "json",
        method  : "post",
        timeout : 5000
    }).done(callback);
}

/*OK*/function profileControls(){
    // -- afectations --
    btn_changePwd  = $("section #change-pwd");
    btn_accountDel = $("section #account-deletion");
    var email      = $("section #profile [type='email']");
    
    // -- events --
    /*OK*/btn_changePwd.click(function(){
        var data = {
            action: "forgotPassword",
            email : email.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            message.html( response.forgotten ?
                "<h4 class='ajax-success'>Password forgotten</h4><p>An "
                    +"e-mail has been sended to reset your password.</p>" :
                "<h4 class='ajax-error'>Internal error</h4><p>Unable"
                    + " to forgot your current password.</p>");
        };
        ajaxOperator(data, callback);
    });
    /*OK*/btn_accountDel.click(function(){
        var data = {
            action   : "askDeletion",
            idUser   : idUser.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.deleting){
                
            }
            message.html( response.deleting ?
                "<h4 class='ajax-success'>Account deletion asked</h4>" :
                "<h4 class='ajax-error'>Internal error</h4><p>Unable "
                          + "to ask your account deletion.</p>" );
        };
        ajaxOperator(data, callback);
    });
}
/*OK*/$(document).ready(function(){
    ajaxControls();
    profileControls();    
});