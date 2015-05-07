$(document).ready(function(){
    var categories = $("#menu li.category");
    categories.click(function(){
        categories.filter(".active").removeClass("active");
        $(this).addClass("active");
    });
});