// -- Ajax --
var ajax, loader, message;

// -- Login --


// -- Signup --


// -- Forgot --


// -- Reset --
var btn_login, btn_signup;

/*OK*/function ajaxControls(){
    // -- affectations --
    ajax       = $("#ajax");
    loader     = ajax.find("#ajax-loader");
    var closer = ajax.find("#ajax-closer");
    message    = ajax.find("#ajax-message");
    
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

//function controlsAffectation(){
//    btn_login = $("#loginbox #btn-login");
//    btn_signup = $("#signupbox #btn-signup");
//}
//function controlsEvents(){
//    btn_login.click(function(){});
//    btn_signup.click(function(){});
//}

/*OK*/function resetControls(){
    // -- affectations --
    div_reset  = $("#resetbox");
    reset_back = div_reset.find("#reset-back");
    id         = div_reset.find("#id");
    token      = div_reset.find("#token");
    password1  = div_reset.find("#password1");
    password2  = div_reset.find("#password2");
    btn_valid  = div_reset.find("#btn-valid");
    
    // -- events --
    /*OK*/reset_back.click(function(){
       document.location = "login.php";
    });
    /*OK*/btn_valid.click(function(){
        // check passwords
        if(password1.val()==='' || password2.val()===''){
            loader.css('display','none');
            message.html("<h4>Operation cancelled</h4><p>Empty password found.</p>"
                        +"<span style='color:red'><em>This window will be closed"
                        +" automatically in 5 seconds.</em><span>");
            ajax.modal({backdrop: false});
            setTimeout(function(){ ajax.modal('hide'); }, 5000);
            return;
        }
        if(password1.val() !== password2.val()){
            loader.css('display','none');
            message.html("<h4>Operation cancelled</h4><p>Passwords don't match.</p>"
                        +"<span style='color:red'><em>This window will be closed"
                        +" automatically in 5 seconds.</em><span>");
            ajax.modal({backdrop: false});
            setTimeout(function(){ ajax.modal('hide'); }, 5000);
            return;
        }
        
        // passwords match
        var data = {
            action   : "updatePassword",
            idUser   : id.val(),
            token    : token.val(),
            password1: password1.val(),
            password2: password2.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.updated || response.errorTime || response.errorToken){
                btn_valid.add(password1).add(password2).css('cursor','not-allowed');
                btn_valid.add(password1).add(password2).attr("disabled",true);
            }
            
            if(response.updated){
                message.html("<h4 class='ajax-success'>Password updated</h4>");
            }
            if(response.error){
                message.html("<h4 class='ajax-error'>Internal error</h4>"
                           + "<p>Unable to update your password.</p>");
            }
            if(response.errorTime){
                message.html("<h4 class='ajax-error'>Time error</h4>"
                           + "<p>"+ response.errorTime +".</p>"
                           + "<p>Restart a forgot password process.</p>");
            }
            if(response.errorToken){
                message.html("<h4 class='ajax-error'>Token error</h4>"
                           + "<p>"+ response.errorToken +".</p>"
                           + "<p>Restart a forgot password process.</p>");
            }
        };
        ajaxOperator(data, callback);
    });
}


$(document).ready(function(){
    ajaxControls();
    resetControls();
});