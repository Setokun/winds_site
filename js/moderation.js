// -- DIV AJAX --
var loader, message, infos;

// -- DIV MODERATION --
var table_moderate, btn_accept, btn_refuse;

/*OK*/function Infos(classCss, messageToDisplay){
    this.css = classCss;
    this.msg = messageToDisplay;

    this.reset = function(){
        message.removeClass(this.css);
        message.html("");
        this.css = undefined;
        this.msg = undefined;
    };
    this.show  = function(afterCallback){
        message.addClass(this.css);
        message.html(this.msg);
        message.slideDown("slow").delay(4000).slideUp("slow", function(){
            infos.reset();
            if(afterCallback !== undefined){ afterCallback(); }
        });
    };
}
/*OK*/function ajaxControls(){
    // -- affectations --
    loader  = $("#moderation #div-ajax #ajax-loader");
    message = $("#moderation #div-ajax #ajax-message");
    
    // -- events --
    $(document).ajaxStart(function(){ loader.css("display","initial"); });
    $(document).ajaxStop(function(){ loader.css("display","none"); });
}
/*OK*/function affectsControls(){
    table_moderate = $("#moderation #table-moderate");
    btn_accept     = table_moderate.find(".btn-success");
    btn_refuse     = table_moderate.find(".btn-danger");
}
/*OK*/function eventsControls(){
    /*OK*/btn_accept.click(function(){
        var levelRow = $(this).parents("tr");
        var data = {
            action : "acceptLevel",
            idLevel: levelRow.data('idlevel')
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.accepted){
                levelRow.remove();
            }
            infos = response.accepted ?
                    new Infos("success", "<h4>Level acceptance succeeded</h4>") :
                    new Infos("error", "<h4>Internal error</h4><p>Unable"
                             +" to accept this level.</p>");
        };
        ajaxOperator(data, callback);
    });
    /*OK*/btn_refuse.click(function(){
        var levelRow = $(this).parents("tr");
        var data = {
            action : "refuseLevel",
            idLevel: levelRow.data('idlevel')
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.refused){
                levelRow.remove();
            }
            infos = response.refused ?
                    new Infos("success", "<h4>Level refusal succeeded</h4>") :
                    new Infos("error", "<h4>Internal error</h4><p>Unable"
                             +" to refuse this level.</p>");
        };
        ajaxOperator(data, callback);
    });
}
/*OK*/function ajaxOperator(data, callback){
    $.ajax({
        url     : "ajax.php",
        data    : data,
        datatype: "json",
        method  : "post",
        timeout : 5000
    })
    .done(callback)
    .fail(function(){
        infos = new Infos("error", "<h4>Internal error</h4><p>Timeout reached.</p>");
    })
    .always(function(){ infos.show(); });
}
/*OK*/$(document).ready(function(){
    ajaxControls();
    affectsControls();
    eventsControls();    
});