// -- Common --
var idUser, loader, infos;

// -- Users list --
var userList, accounts, btn_valid, btn_delete, btn_banish;

// -- Deletions list --
var deletionList;

/*OK*/function Infos(classCss, message){
    this.class = classCss;
    this.msg   = message;
    this.show  = function(){
        infos.addClass(this.class);
        infos.html(this.msg);
        infos.slideDown("slow").delay(5000).slideUp("slow", function(){
            infos.html(undefined);
            infos.removeClass(Infos.class);
        });
}
}
/*OK*/function focusAccount(item){
    // leave focus of the previous account
    var exFocus = accounts.filter(function(){ return $(this).data("focused"); });
    exFocus.children().last().css('display','none');
    exFocus.css('background-color','transparent');
    exFocus.removeData("focused");
    
    // focus the current account
    item.data("focused", true);
    item.children().last().css('display','initial');
    item.css('background-color','lightskyblue');
    
    // reset the radiobutton of the current account
    var radios = item.find("input[type='radio']");
    radios.filter(":checked").removeAttr("checked");
    radios.filter("[value='"+ item.data('usertype') +"']").prop("checked",true);
    
    // animate it
    $('body').animate({ scrollTop: item.offset().top -50 }, 800);
}
/*OK*/function scrollTo(position){
    $('body').animate({scrollTop: position});
}
/*OK*/function commonControls(){
    // -- affectations --
    idUser = $("section #common #idUser");
    loader = $("section #common #loader");
    infos  = $("section #common #infos");
    
    // -- events --
    var animatedLoader;
    $(document).ajaxStart(function(){
        position = $(document).scrollTop();
        loader.css("display","initial");
        animatedLoader = $('body').animate({scrollTop: 0}, 400);
    });
    $(document).ajaxStop(function(){
        $.when(animatedLoader).done(function(){
            loader.css("display","none");
            $('body').animate({scrollTop: position}, 400);
        });
    });
}
/*OK*/function userControls(){
    // -- affectations --
    userList   = $("section #list-user");
    accounts   = userList.find(".account");
    btn_valid  = userList.find(".btn-valid");
    btn_delete = userList.find(".btn-delete");
    btn_banish = userList.find(".btn-banish");

    // -- events --
    /*OK*/userList.on("click",".account h4",function(){
        focusAccount( $(this).parents(".account") );
    });
    /*OK*/btn_valid.click(function(){
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
            var info = response.updated ?
                       new Infos("success", "<h4>Account updated</h4>") :
                       new Infos("error", "<h4>Internal error</h4><p>Unable"
                                +" to update the type of this account.</p>");
            info.show();
        };
        ajaxOperator(data, callback);
    });
    /*OK*/btn_delete.click(function(){
        var account = $(this).parents(".account");
        var data = {
            action   : "deleteAccount",
            idUser   : account.data('iduser')
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.deleted){
                account.remove();
                var accountDel = deletionList.find("h5[data-iduser='"
                               + account.data('iduser') +"']");
                if(accountDel.length > 0){  accountDel.parent().remove();  }
            }
            var info = response.deleted ?
                       new Infos("success", "<h4>Account deleted</h4>") :
                       new Infos("error", "<h4>Internal error</h4><p>Unable"
                                +" to delete this account.</p>");
            info.show();
        };
        ajaxOperator(data, callback);
    });
    /*OK*/btn_banish.click(function(){
        var account = $(this).parents(".account");
        var data = {
            action   : "banishAccount",
            idUser   : account.data('iduser')
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.banished){
                account.find("input[type='radio']").attr("disabled","");
                account.find("input.btn-valid").parent().css("display","none");
                account.find("input.btn-banish").parent().css("display","none");
            }
            var info = response.banished ?
                       new Infos("success", "<h4>Account banished</h4>") :
                       new Infos("error", "<h4>Internal error</h4><p>Unable"
                                +" to banish this account.</p>");
            info.show();
        };
        ajaxOperator(data, callback);
    });
}
/*OK*/function deletionControls(){
    // -- affectations --
    deletionList = $("section #list-deletion");
    
    // -- events --
    /*OK*/deletionList.on("click","h5",function(){
        var idUser = $(this).data('iduser');
        var userAccount = accounts.filter("[data-iduser='"+idUser+"']");
        focusAccount(userAccount);
    });
}

/*OK*/function ajaxOperator(data, callback){
    $.ajax({
        url: "ajax.php",
        data: data,
        datatype: "json",
        method: "post",
    }).done(callback);
}
/*OK*/$(document).ready(function(){
    commonControls();
    userControls();
    deletionControls();
});