$(document).ready(function(){
    $("#forum .subject").click(function(){
        document.location = "forum.php?id=" + $(this).data('idsubject');
    });
    $("#forum .btn-back").click(function(){
        document.location = "forum.php";
    });
});