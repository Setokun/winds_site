var tabs,           // li
    contents;       // div

function controlsAffectation(){
    tabs        = $("div#categories li");
    contents    = $("div#categories div.tab-pane");
}

function controlsEvents(){
    /*ok*/tabs.click(function(){
        // deactive the current tab and content
        tabs.filter(".active").removeClass("active");
        contents.filter(".active").removeClass("active");
        // active the clicked tab and its content
        $(this).addClass("active");
        var id = $(this).find("a").attr("href");//.substr(1);
        contents.filter(id).addClass("active");
    });
    
}

function initializePage(){
    tabs.first().addClass("active");
    contents.first().addClass("active");
}

$(document).ready(function(){
    controlsAffectation();
    controlsEvents();
    initializePage();
});