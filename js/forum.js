// -- DIV subject --
var div_newSubject, title_newSubject, msg_newSubject,
    btn_createSubject, btn_cancelSubject;
var div_subject, btn_newSubject, table_subjects,subject;

// -- DIV post --
var div_newPost, msg_newPost, btn_createPost, btn_cancelPost;
var div_post, btn_newPost, btn_closeSubject,
    btn_deleteSubject, btn_back, btn_deletePost;

function subjectControls(){
    // -- affectations --
    div_newSubject    = $("#forum #subject #new-subject");
    title_newSubject  = div_newSubject.find("#title-new-subject");
    msg_newSubject    = div_newSubject.find("#message-new-subject");
    btn_createSubject = div_newSubject.find("#create-subject");
    btn_cancelSubject = div_newSubject.find("#cancel-subject");
    div_subject       = $("#forum #subject #display-subject");
    btn_newSubject    = div_subject.find("#btn-new-subject");
    table_subjects    = div_subject.find("#table-subjects");
    subject           = div_subject.find(".subject");

    // -- events --
    /*OK*/subject.click(function(){
        document.location = "forum.php?id=" + $(this).data('idsubject');
    });
    /*OK*/btn_newSubject.click(function(){
        div_subject.slideToggle();
        div_newSubject.slideToggle(400,function(){
            title_newSubject.val(undefined);
            msg_newSubject.val(undefined);
        });
    });
    btn_createSubject.click(function(){
        alert("create subject");
        //btn_newSubject.click();
    });
    /*OK*/btn_cancelSubject.click(function(){
        btn_newSubject.click();
    });
}
function postControls(){
    // -- affectations --
    div_newPost       = $("#forum #post #new-post");
    msg_newPost       = div_newPost.find("#message-new-post");
    btn_createPost    = div_newPost.find("#create-post");
    btn_cancelPost    = div_newPost.find("#cancel-post");
    div_post          = $("#forum #post #display-post");
    btn_newPost       = div_post.find("#btn-new-post");
    btn_closeSubject  = div_post.find("#btn-close-subject");
    btn_deleteSubject = div_post.find("#btn-delete-subject");
    btn_back          = div_post.find("#btn-back");
    btn_deletePost    = div_post.find(".btn-delete-post");
    
    // -- events --
    /*OK*/btn_back.click(function(){
        document.location = "forum.php";
    });
    btn_deletePost.click(function(){ alert("delete post"); });
    btn_closeSubject.click(function(){ alert("close subject"); });
    btn_deleteSubject.click(function(){ alert("delete subject"); });
    /*OK*/btn_newPost.click(function(){
        div_post.slideToggle();
        div_newPost.slideToggle(400,function(){
            msg_newPost.val(undefined);
        });
    });
    btn_createPost.click(function(){ alert("create post"); });
    /*OK*/btn_cancelPost.click(function(){
        btn_newPost.click();
    });
}
$(document).ready(function(){
    subjectControls();
    postControls();
});