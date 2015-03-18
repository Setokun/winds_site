// -- Common --
var idUser, loader, infos;

// -- Users list --
var userList;

// -- Deletions list --
var deletionList;

/*OK*/function Infos(classCss, message){
    this.class = classCss;
    this.msg   = message;
    this.show  = function(afterCallback){
        infos.addClass(this.class);
        infos.html(this.msg);
        infos.slideDown("slow").delay(5000).slideUp("slow", function(){
            infos.html(undefined);
            infos.removeClass(Infos.class);
            afterCallback();
        });
    }
}
/*OK*/function focusAccount(item){
    account.each(function(){
        $(this).children().last().css('display','none');
        $(this).css('background-color','transparent');
    });
    item.children().last().css('display','initial');
    item.css('background-color','lightskyblue');
    $('body').animate({ scrollTop: item.offset().top }, 800);
}

function commonControls(){
    // -- affectations --
    idUser = $("section #common #idUser");
    loader = $("section #common #loader");
    infos  = $("section #common #infos");
    
    // -- events --
    $(document).ajaxStart(function(){ loader.css("display","initial"); });
    $(document).ajaxStop(function(){ loader.css("display","none"); })
}
function userControls(){
    // -- affectations --
    userList = $("section #list-user");

    // -- events --
    /*OK*/userList.on("click",".account",function(){
        focusAccount( $(this) );
    });
}
function deletionControls(){
    // -- affectations --
    deletionList = $("section #list-deletion");
    
    // -- events --
    /*OK*/deletionList.on("click","h5",function(){
        var idUser = $(this).data('iduser');
        var userAccount = account.filter("[data-iduser='"+idUser+"']");
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