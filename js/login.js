function ajaxControls(){
    // -- affectations --
    ajax       = $("#ajax");
    loader     = ajax.find("#ajax-loader");
    var closer = ajax.find("#ajax-closer");
    message    = ajax.find("#ajax-message");
    
    // -- events --
    $(document).ajaxStart(function(){
        loader.css('display','block');
        closer.removeClass('btn-info');
        closer.addClass('disabled');
        message.css('display','none');
        message.html("<h4 class='ajax-error'>Internal error"
                    +"</h4><p>Timeout reached.</p>");
        ajax.modal({backdrop: false});
    });
    $(document).ajaxStop(function(){
        loader.toggle();
        closer.removeClass('disabled');
        closer.addClass('btn-info');
        message.toggle();
        message.append("<span style='color:red'><em>This window will"
                      +" be closed automatically in 5 seconds.</em><span>");
        setTimeout(function(){ ajax.modal('hide'); }, 5000);
    });
}
function ajaxOperator(data, callback){
    $.ajax({
        url     : "ajax.php",
        data    : data,
        datatype: "json",
        method  : "post",
        timeout : 5000
    }).done(callback);
}

function loginControls(){
    // -- affectations --
    div_login          = $('#login-box');
    var email          = div_login.find('#login-email');
    var password       = div_login.find('#login-password');
    var login_toForgot = div_login.find('#login-to-forgot');
    var login_toSignup = div_login.find("#login-to-signup");
    var btn_login      = div_login.find("#btn-login");
    var form           = div_login.find("form");
    
    // -- events --
    login_toForgot.click(function(){
        div_login.toggle();
        div_forgot.toggle();
    });
    login_toSignup.click(function(){
        div_login.toggle();
        div_signup.toggle();
    });
    btn_login.click(function(){
        if( !requiredFilled(div_login) ){ return; }
        
        // checks account
        var data = {
            action   : "checkLogin",
            email    : email.val(),
            password : password.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.DBdown){
                message.html("<h4 class='ajax-error'>Operation canceled</h4>"
                            +"<p>The database is down.</p>");
                return;
            }
            if(response.allowed){
                ajax.modal('hide');
                form.submit();
            }
            if(response.errorID){
                message.html("<h4 class='ajax-error'>Wrong identifiants</h4>"
                           + "<p>"+ response.errorID +".</p>");
            }
            if(response.errorStatus){
                message.html("<h4 class='ajax-error'>You can't log in :(</h4>"
                           + "<p>"+ response.errorStatus +".</p>");
            }
        };
        ajaxOperator(data, callback);       
    });
}
function signupControls(){
    // -- affectations --
    div_signup         = $('#signup-box');
    var signup_toLogin = div_signup.find('#signup-to-login');
    var btn_signup     = div_signup.find('#btn-signup');
    var email          = div_signup.find('#signup-email');
    var pseudo         = div_signup.find('#signup-pseudo');
    var password1      = div_signup.find('#signup-password1');
    var password2      = div_signup.find('#signup-password2');
    
    // -- events --
    signup_toLogin.click(function(){
        div_signup.toggle();
        div_login.toggle();
    });
    btn_signup.click(function(){
        // checks
        if( !requiredFilled(div_signup) ){ return; }
        if( !checkPwdFields(password1, password2) ){ return; }
        
        // all is filled        
        var data = {
            action   : "createAccount",
            email    : email.val(),
            pseudo   : pseudo.val(),
            password1: password1.val(),
            password2: password2.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.DBdown){
                message.html("<h4 class='ajax-error'>Operation canceled</h4>"
                            +"<p>The database is down.</p>");
                return;
            }
            if(response.created){
                div_signup.find("input[type=text], input[type=password]")
                          .each(function(){ this.value = ''; });
                div_signup.toggle();  div_login.toggle();
                message.html("<h4 class='ajax-success'>Account created</h4><p>An "
                            +"e-mail has been sended to active your account.</p>");
            }
            if(response.error){
                message.html("<h4 class='ajax-error'>Internal error</h4>"
                           + "<p>Unable to create this account.</p>");
            }
            if(response.errorEmail){
                message.html("<h4 class='ajax-error'>Email error</h4>"
                           + "<p>"+ response.errorEmail +".</p>"
                           + "<p>Please, type another one.</p>");
            }
            if(response.errorPseudo){
                message.html("<h4 class='ajax-error'>Pseudo error</h4>"
                           + "<p>"+ response.errorPseudo +".</p>"
                           + "<p>Please, type another one.</p>");
            }
            if(response.errorMailing){
                message.html("<h4 class='ajax-error'>Mail error</h4>"
                           + "<p>"+ response.errorMailing +".</p>"
                           + "<p>Please, type a correct e-mail address.</p>");
            }
        };
        ajaxOperator(data, callback);
    });
}
function forgotControls(){
    // -- affectations --
    div_forgot         = $('#forgot-box');
    var forgot_toLogin = div_forgot.find('#forgot-to-login');
    var btn_send       = div_forgot.find('#btn-send');
    var email          = div_forgot.find('#forgot-email');
    
    // -- events --
    forgot_toLogin.click(function(){
        div_forgot.toggle();
        div_login.toggle();
    });
    btn_send.click(function(){
        if( !requiredFilled(div_forgot) ){ return; }
        
        // passwords match
        var data = {
            action: "forgotPassword",
            email : email.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.DBdown){
                message.html("<h4 class='ajax-error'>Operation canceled</h4>"
                            +"<p>The database is down.</p>");
                return;
            }
            if(response.forgotten){
                div_forgot.toggle();
                div_login.toggle();
                message.html("<h4 class='ajax-success'>Password forgotten</h4>"
                    +"<p>An e-mail has been sended to reset your password.</p");
            }
            if(response.error){
                message.html("<h4 class='ajax-error'>Internal error</h4>"
                           + "<p>Unable to forgot the password for "
                           + "this email address.</p>");
            }
            if(response.errorMail){
                message.html("<h4 class='ajax-error'>Email error</h4>"
                           + "<p>"+ response.errorMail +".</p>");
            }
        };
        ajaxOperator(data, callback);
    });
}
function resetControls(){
    // -- affectations --
    var div_reset     = $("#reset-box");
    var reset_toLogin = div_reset.find("#reset-to-login");
    var id            = $("#id");
    var token         = $("#token");
    var password1     = div_reset.find("#password1");
    var password2     = div_reset.find("#password2");
    var btn_valid     = div_reset.find("#btn-valid");
    
    // -- events --
    reset_toLogin.click(function(){
       document.location = "login.php";
    });
    btn_valid.click(function(){
        // check passwords
        if( !requiredFilled(div_reset) ){ return; }
        if( !checkPwdFields(password1, password2) ){ return; }
        
        // passwords match
        var data = {
            action   : "resetPassword",
            idUser   : id.val(),
            token    : token.val(),
            password1: password1.val(),
            password2: password2.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.DBdown){
                message.html("<h4 class='ajax-error'>Operation canceled</h4>"
                            +"<p>The database is down.</p>");
                return;
            }
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

function requiredFilled(div){
    var inputs = div.find("input[type=text], input[type=password]");
    var emptys = inputs.filter(function(){ return !this.value; });
    
    if(emptys.length === 0){ return true; }
    loader.css('display','none');
    message.html("<h4>Operation cancelled</h4><p>Empty fields found."
                +"</p><span style='color:red'><em>This window will "
                +"be closed automatically in 5 seconds.</em><span>");
    ajax.modal({backdrop: false});
    setTimeout(function(){ ajax.modal('hide'); }, 5000);
    return false;
}
function checkPwdFields(pwd1, pwd2){
    if(pwd1.val() !== pwd2.val()){
        loader.css('display','none');
        message.html("<h4>Operation cancelled</h4>"
                    +"<p>Passwords don't match.</p>"
                    +"<span style='color:red'><em>This window will be "
                    +"closed automatically in 5 seconds.</em><span>");
        ajax.modal({backdrop: false});
        setTimeout(function(){ ajax.modal('hide'); }, 5000);
        return false;
    }
    return true;
}
$(document).ready(function(){
    ajaxControls();
    loginControls();
    signupControls();
    forgotControls();
    resetControls();
});