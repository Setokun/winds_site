// -- Ajax --
var message;

// -- Moderation --
var table_moderate, btn_accept, btn_refuse;

function ajaxControls(){
    // -- affectations --
    var ajax   = $("section #ajax");
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
function moderationControls(){
    // -- affectations --
    table_moderate = $("section #table-moderate");
    btn_accept     = table_moderate.find(".btn-success");
    btn_refuse     = table_moderate.find(".btn-danger");
    
    // -- events --
    btn_accept.click(function(){
        var levelRow = $(this).parents("tr");
        var data = {
            action : "acceptLevel",
            idLevel: levelRow.data('idlevel')
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.DBdown){
                message.html("<h4 class='ajax-error'>Operation canceled</h4>"
                            +"<p>The database is down.</p>");
                return;
            }
            if(response.accepted){
                levelRow.remove();
            }
            message.html( response.accepted ?
                "<h4 class='ajax-success'>Level acceptance succeeded</h4>" :
                "<h4 class='ajax-error'>Internal error</h4><p>Unable "
                             + "to accept this level.</p>" );
        };
        ajaxOperator(data, callback);
    });
    btn_refuse.click(function(){
        var levelRow = $(this).parents("tr");
        var data = {
            action : "refuseLevel",
            idLevel: levelRow.data('idlevel')
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.DBdown){
                message.html("<h4 class='ajax-error'>Operation canceled</h4>"
                            +"<p>The database is down.</p>");
                return;
            }
            if(response.refused){
                levelRow.remove();
            }
            message.html( response.refused ?
                "<h4 class='ajax-success'>Level refusal succeeded</h4>" :
                "<h4 class='ajax-error'>Internal error</h4><p>Unable "
                             + "to refuse this level.</p>" );
        };
        ajaxOperator(data, callback);
    });
}
$(document).ready(function(){
    ajaxControls();
    moderationControls();    
});