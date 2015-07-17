// -- Ajax --
var idUser, message;

// -- Users list --
var usersList, usersScroll, accounts, btn_update, btn_delete, btn_banish, btn_unbanish;

// -- Deletions list --
var deletionsList;

function ajaxControls(){
    // -- affectations --
    var ajax   = $("section #ajax");
    var loader = ajax.find("#ajax-loader");
    var closer = ajax.find("#ajax-closer");
    message    = ajax.find("#ajax-message");
    idUser     = ajax.find("#idUser");
    
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
function scrollTo(account){
    $('body').animate({scrollTop: usersList.offset().top}, 600);
    usersScroll.animate({scrollTop: usersScroll.scrollTop() + account.position().top -65}, 600);
}
function focusAccount(account){
    accounts.find(".account-actions")
            .filter(':not(.collapse)')
            .addClass('collapse');
     
    account.find(".account-actions").removeClass('collapse');
    scrollTo(account);
}
function userControls(){
    // -- affectations --
    usersList    = $("section #users-list");
    usersScroll  = usersList.find("#row-scroll-accounts");
    accounts     = usersList.find(".account");
    btn_update   = usersList.find(".btn-success");
    btn_delete   = usersList.find(".btn-danger");
    btn_banish   = usersList.find(".btn-warning");
    btn_unbanish = usersList.find(".btn-primary");

    // -- events --
    usersList.on("click",".panel-heading",function(){
        var account = $(this).parent();
        account.find("[type='radio'][value='"+ account.data('usertype') +"']")
               .prop("checked",true);
        account.find(".account-actions").hasClass('collapse') ?
                focusAccount(account) :
                account.find(".account-actions").addClass('collapse');
    });
    btn_update.click(function(){
        var account     = $(this).parents(".account");
        var newUserType = account.find(":checked").val();
        var data = {
            action   : "updateRights",
            idUser   : account.data('iduser'),
            userType : newUserType
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.updated){
                account.data('usertype', newUserType);
            }
            message.html( response.updated ?
                "<h4 class='ajax-success'>Account updated</h4>" :
                "<h4 class='ajax-error'>Internal error</h4><p>Unable to "
                          + "update the account type of this user.</p>" );
        };
        ajaxOperator(data, callback);
    });
    btn_delete.click(function(){
        var account = $(this).parents(".account");
        var data = {
            action   : "deleteAccount",
            idUser   : account.data('iduser')
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.deleted){
                account.remove();
                var accountDel = deletionsList.find("[data-iduser='"
                               + account.data('iduser') +"']");
                if(accountDel.length > 0){  accountDel.parent().remove();  }
            }
            message.html( response.deleted ?
                "<h4 class='ajax-success'>Account deleted</h4>" :
                "<h4 class='ajax-error'>Internal error</h4><p>Unable to "
                          + "delete this account.</p>" );
        };
        ajaxOperator(data, callback);
    });
    btn_banish.click(function(){
        var account = $(this).parents(".account");
        var data = {
            action   : "banishAccount",
            idUser   : account.data('iduser')
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.banished){
                account.find("[type='radio']").attr("disabled",true);
                account.find(".btn-success").css("display","none");
                account.find(".btn-warning").css("display","none");
                account.find(".btn-primary").removeAttr("style");
                account.find("h3").append(" (banished)");
                setTimeout(function(){ location.reload(); }, 5000);
            }
            message.html( response.banished ?
                "<h4 class='ajax-success'>Account banished</h4>" :
                "<h4 class='ajax-error'>Internal error</h4><p>Unable to "
                          + "banish this account.</p>" );
        };
        ajaxOperator(data, callback);
    });
    btn_unbanish.click(function(){
        var account = $(this).parents(".account");
        var title   = account.find("h3");
        var data = {
            action   : "unbanishAccount",
            idUser   : account.data('iduser')
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.unbanished){
                account.find("[type='radio']").removeAttr("disabled");
                account.find(".btn-success").removeAttr("style");
                account.find(".btn-warning").removeAttr("style");
                account.find(".btn-primary").css("display","none");
                title.html(title.html().replace(' (banished)',''));
            }
            message.html( response.unbanished ?
                "<h4 class='ajax-success'>Account unbanished</h4>" :
                "<h4 class='ajax-error'>Internal error</h4><p>Unable to "
                          + "unbanish this account.</p>" );
        };
        ajaxOperator(data, callback);
    });
}
function deletionControls(){
    // -- affectations --
    deletionsList = $("section #deletions-list");
    
    // -- events --
    deletionsList.on("click","tr",function(){
        var idUser = $(this).data('iduser');
        var account = accounts.filter("[data-iduser="+idUser+"]");
        account.find(".account-actions").hasClass('collapse') ?
            account.children().first().click() :
            scrollTo(account);
    });
}
$(document).ready(function(){
    ajaxControls();
    userControls();
    deletionControls();
});