$(document).ready(function(){
    $("#shop .custom-level").click(function(){
        var description = $(this).find(".description");
        var isDisplayed = description.css("display") === "inline-block";
        $("#shop .custom-level .description").css("display","none");
        if(isDisplayed){ description.slideUp(400);
        }else{           description.slideDown(400); }
    });
});