$(document).ready(function(){
    $("section .custom-level").click(function(){
        var description = $(this).find(".description");
        var isDisplayed = description.css("display") === "inline";
        $("section .custom-level .description").css("display","none");
        if(isDisplayed){ description.fadeOut(400);
        }else{           description.fadeIn(400); }
    });
});