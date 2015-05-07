$(document).ready(function(){
    $("#score .level").click(function(){
        document.location = "score.php?id=" + $(this).data('idlevel');
    });
    $("#score .btn-primary").click(function(){
        document.location = "score.php";
    });
});