// -- Common --
var idUser, loader, infos;

// -- DIV subject --
var div_newSubject, title_newSubject, msg_newSubject,
    btn_createSubject, btn_cancelSubject;
var div_subject, btn_newSubject, table_subjects;

// -- DIV post --
var div_newPost, msg_newPost, btn_createPost, btn_cancelPost;
var div_post, p_statusSubject, btn_newPost, btn_closeSubject,
    btn_deleteSubject, btn_back, btn_deletePost;

/*OK*/function backToForum(){
    document.location = "forum.php";
}
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
/*OK*/function commonControls(){
    // -- affectations --
    idUser = $("#forum #common #idUser");
    loader = $("#forum #common #loader");
    infos  = $("#forum #common #infos");
    
    // -- events --
    $(document).ajaxStart(function(){ loader.css("display","initial"); });
    $(document).ajaxStop(function(){ loader.css("display","none"); })
}
/*OK*/function subjectControls(){
    // -- affectations --
    div_newSubject    = $("#forum #subject #new-subject");
    title_newSubject  = div_newSubject.find("#title-new-subject");
    msg_newSubject    = div_newSubject.find("#message-new-subject");
    btn_createSubject = div_newSubject.find("#create-subject");
    btn_cancelSubject = div_newSubject.find("#cancel-subject");
    div_subject       = $("#forum #subject #display-subject");
    btn_newSubject    = div_subject.find("#btn-new-subject");
    table_subjects    = div_subject.find("#table-subjects");

    // -- events --
    /*OK*/btn_newSubject.click(function(){
        div_subject.slideToggle();
        div_newSubject.slideToggle(400,function(){
            title_newSubject.val(undefined);
            msg_newSubject.val(undefined);
        });
    });
    /*OK*/btn_createSubject.click(function(){
        var data = {
            action  : "createSubject",
            idUser  : idUser.val(),
            title   : title_newSubject.val(),
            message : msg_newSubject.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.subjectRow){
                table_subjects.append(response.subjectRow);
                btn_newSubject.click();
            }
            var info = response.subjectRow ?
                       new Infos("success", "<h4>Insertion succeeded</h4>") :
                       new Infos("error", "<h4>Internal error</h4><p>Unable"
                                +" to save this new subject.</p>");
            info.show();
        };
        ajaxOperator(data,callback);
    });
    /*OK*/btn_cancelSubject.click(function(){
        btn_newSubject.click();
    });
    /*OK*/table_subjects.on("click", "tr.subject", function(){
        document.location = "forum.php?id=" + $(this).data('idsubject');
    });
}
function postControls(){
    // -- affectations --
    div_newPost       = $("#forum #post #new-post");
    msg_newPost       = div_newPost.find("#message-new-post");
    btn_createPost    = div_newPost.find("#create-post");
    btn_cancelPost    = div_newPost.find("#cancel-post");
    div_post          = $("#forum #post #display-post");
    p_statusSubject   = div_post.find("#status-subject");
    btn_newPost       = div_post.find("#btn-new-post");
    btn_closeSubject  = div_post.find("#btn-close-subject");
    btn_deleteSubject = div_post.find("#btn-delete-subject");
    btn_back          = div_post.find("#btn-back");
    btn_deletePost    = div_post.find(".btn-delete-post");
    
    // -- events --
    /*OK*/btn_back.click(function(){ backToForum(); });
    btn_deletePost.click(function(){ alert("delete post"); });
    /*OK*/btn_closeSubject.click(function(){
        var data = {
            action   : "closeSubject",
            idUser   : idUser.val(),
            idSubject: $(this).data("idsubject")
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.updated){
                btn_newPost.add(btn_closeSubject).parent().css("display","none");
                p_statusSubject.html("Status : closed");
            }
            var info = response.updated ?
                       new Infos("success", "<h4>Subject closure succeeded</h4>") :
                       new Infos("error", "<h4>Internal error</h4><p>"
                                +"Unable to close this subject.</p>");
            info.show();
        };
        ajaxOperator(data,callback);
    });
    /*OK*/btn_deleteSubject.click(function(){
        var data = {
            action   : "deleteSubject",
            idUser   : idUser.val(),
            idSubject: $(this).data("idsubject")
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            var info = response.deleted ?
                       new Infos("success", "<h4>Subject deletion succeeded</h4>"
                                +"<h5>Redirection to the forum in few seconds.</h5>") :
                       new Infos("error", "<h4>Internal error</h4><p>Unable to "
                                +"delete this subject and its associated posts.</p>");
            info.show(function(){
                if(response.deleted){ backToForum(); }
            });
        };
        ajaxOperator(data,callback);
    });
    /*OK*/btn_newPost.click(function(){
        div_post.slideToggle();
        div_newPost.slideToggle(400,function(){
            msg_newPost.val(undefined);
        });
    });
    btn_createPost.click(function(){
        var data = {
            action  : "addPost",
            idUser  : idUser.val(),
            message : msg_newPost.val()
        };
        var success = function(response){
            table_subjects.append(response);
            btn_newSubject.click();
        };
        var failure = function(){
            error.html("<h4>Internal error</h4><p>Unable to save"
                      +"this new subject into the database.</p>");
        };
        ajaxOperator(data,success,failure);
    });
    /*OK*/btn_cancelPost.click(function(){
        btn_newPost.click();
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
    subjectControls();
    postControls();
});