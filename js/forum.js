// -- Ajax --
var ajax, idUser, message;

// -- Subject --
var div_newSubject, title_newSubject, msg_newSubject,
    btn_createSubject, btn_cancelSubject;
var div_subject, btn_newSubject, table_subjects;

// -- Post --
var div_newPost, msg_newPost, btn_createPost, btn_cancelPost;
var div_post, p_statusSubject, btn_newPost,
    btn_closeSubject, btn_deleteSubject, btn_back,
    table_posts, btn_deletePost, idSubject;
    
function ajaxControls(){
    // -- affectations --
    ajax   = $("section #ajax");
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
function subjectControls(){
    // -- affectations --
    div_newSubject    = $("section #subject #new-subject");
    title_newSubject  = div_newSubject.find("#title-new-subject");
    msg_newSubject    = div_newSubject.find("#message-new-subject");
    btn_createSubject = div_newSubject.find("#create-subject");
    btn_cancelSubject = div_newSubject.find("#cancel-subject");
    div_subject       = $("section #subject #display-subject");
    btn_newSubject    = div_subject.find("#btn-new-subject");
    table_subjects    = div_subject.find("#table-subjects");

    // -- events --
    table_subjects.on("click",".subject",function(){
        document.location = "forum.php?id=" + $(this).data('idsubject');
    });
    btn_newSubject.click(function(){
        div_subject.slideToggle();
        div_newSubject.slideToggle(400,function(){
            title_newSubject.val(undefined);
            msg_newSubject.val(undefined);
        });
    });
    btn_createSubject.click(function(){
        var data = {
            action  : "createSubject",
            idUser  : idUser.val(),
            title   : title_newSubject.val(),
            message : msg_newSubject.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(!response.created){
                message.html("<h4 class='ajax-error'>Internal error</h4>"
                           + "<p>Unable to create this subject.</p>" );
                return;
            }
            ajax.modal('hide');
            table_subjects.append(response.created);
            btn_newSubject.click();            
        };
        ajaxOperator(data, callback);
    });
    btn_cancelSubject.click(function(){
        btn_newSubject.click();
    });
}
function postControls(){
    // -- affectations --
    div_newPost       = $("section #post #new-post");
    msg_newPost       = div_newPost.find("#message-new-post");
    btn_createPost    = div_newPost.find("#create-post");
    btn_cancelPost    = div_newPost.find("#cancel-post");
    div_post          = $("section #post #display-post");
    p_statusSubject   = div_post.find("#status-subject");
    btn_newPost       = div_post.find("#btn-new-post");
    btn_closeSubject  = div_post.find("#btn-close-subject");
    btn_deleteSubject = div_post.find("#btn-delete-subject");
    btn_back          = div_post.find("#btn-back");
    table_posts       = div_post.find("#table-posts");
    idSubject         = $("section #post #idSubject");
    
    // -- events --
    btn_back.click(function(){ backToForum(); });
    btn_closeSubject.click(function(){
        var data = {
            action   : "closeSubject",
            idSubject: idSubject.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.closed){
                btn_newPost.add(btn_closeSubject).parent().css("display","none");
                p_statusSubject.html("Status : closed");
                p_statusSubject.css('color','red');
            }
            message.html( response.closed ?
                "<h4 class='ajax-success'>Subject closed</h4>" :
                "<h4 class='ajax-error'>Internal error</h4><p>"
                    +"Unable to close this subject.</p>" );
        };
        ajaxOperator(data, callback);
    });
    btn_deleteSubject.click(function(){
        var data = {
            action   : "deleteSubject",
            idSubject: idSubject.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            response.deleted ?
                backToForum() :
                message.html("<h4 class='ajax-error'>Internal error</h4>"
                            +"<p>Unable to delete this subject and "
                            +"its associated posts.</p>");
        };
        ajaxOperator(data,callback);
    });
    btn_newPost.click(function(){
        div_post.slideToggle();
        div_newPost.slideToggle(400,function(){
            msg_newPost.val(undefined);
        });
    });
    btn_createPost.click(function(){
        var data = {
            action   : "createPost",
            idUser   : idUser.val(),
            idSubject: idSubject.val(),
            message  : msg_newPost.val()
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.empty){
                message.html("<h4 class='ajax-error'>Operation canceled</h4>"
                            +"<p>The input message is empty.</p>");
                return;
            }
            if(!response.created){
                message.html("<h4 class='ajax-error'>Internal error</h4>"
                            +"<p>Unable to create this new post.</p>");
                return;
            }            
            ajax.modal('hide');
            table_posts.append(response.created);
            btn_newPost.click();
        };
        ajaxOperator(data, callback);
    });
    btn_cancelPost.click(function(){
        btn_newPost.click();
    });
    table_posts.on('click','.btn-danger',function(){
        var row = $(this).parents(".row-post");
        var data = {
            action : "deletePost",
            idPost : $(this).data('idpost')
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.deleted){
                row.remove();
            }
            message.html( response.deleted ?
                "<h4 class='ajax-success'>Post deleted</h4>" :
                "<h4 class='ajax-error'>Internal error</h4>"
                    +"<p>Unable to delete this post.</p>");
        };
        ajaxOperator(data,callback);
    });
}
function backToForum(){
    document.location = "forum.php";
}
$(document).ready(function(){
    ajaxControls();
    subjectControls();
    postControls();
});