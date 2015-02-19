$(document).ready(function(){
    $("#score .level").click(function(){
        document.location = "score.php?id=" + $(this).data('idlevel');
    });
    $("#score .btn-back").click(function(){
        document.location = "score.php";
    });
});