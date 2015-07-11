// -- Ajax --
var idUser, message;

// -- Upload --
var upload_addon, inp_name, inp_description, select_type, inp_file, btn_upload;

// -- Remove --
var remove_addon, table, btn_remove;

/*OK*/function ajaxControls(){
    // -- affectations --
    var ajax   = $("section #ajax");
    var loader = ajax.find("#ajax-loader");
    var closer = ajax.find("#ajax-closer");
    message    = ajax.find("#ajax-message");
    idUser     = ajax.find("#idUser");
    
    // -- events --
    /*OK*/$(document).ajaxStart(function(){
        loader.css('display','block');
        closer.removeClass('btn-info');
        closer.addClass('disabled');
        message.css('display','none');
        message.html("<h4 class='ajax-error'>Internal error"
                    +"</h4><p>Timeout reached.</p>");
        ajax.modal({backdrop: false});
    });
    /*OK*/$(document).ajaxStop(function(){
        loader.toggle();
        closer.removeClass('disabled');
        closer.addClass('btn-info');
        message.toggle();
        message.append("<span style='color:red'><em>This window will"
                      +" be closed automatically in 5 seconds.</em><span>");
        setTimeout(function(){ ajax.modal('hide'); }, 5000);
    });
}
/*OK*/function ajaxOperator(data, callback){
    $.ajax({
        url     : "ajax.php",
        data    : data,
        datatype: "json",
        method  : "post",
        timeout : 5000
    }).done(callback);
}
function uploadControls(){
    // -- affectations --
    upload_addon     = $('section #upload-addon');
    form             = upload_addon.find("form")[0];
    inp_name         = upload_addon.find("[name='addon-name']");
    inp_description  = upload_addon.find("[name='addon-description']");
    select_addonType = upload_addon.find("[name='addon-type']");
    inp_file         = upload_addon.find("[name='addon-file']");
    btn_upload       = upload_addon.find('#btn-upload');
    
    // -- events --
    btn_upload.click(function(){
        // checks
        var valid = inp_name.val() !== '' && inp_description !== ''
                 && select_addonType.find(':selected').val() !== "-1"
                 && inp_file.prop('files').length === 1;
        if( !valid ){ return; }
        
        // upload
        var formData = new FormData(form);
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.data){
                inp_name.val(undefined);
                inp_description.val(undefined);
                select_addonType.find('option').first().prop('selected',true);
                inp_file.val(undefined);
            }
            message.html( response.data ?
                "<h4 class='ajax-success'>"+ response.data +".</h4>" :
                "<h4 class='ajax-error'>Error</h4><p>"+ response.error +".</p>" );
        };
        $.ajax({
            url        : "upload.php",
            data       : formData,
            method     : "post",
            cache      : false,
            processData: false,
            contentType: false
        }).done(callback);
    });
}
/*OK*/function removeControls(){
    // -- affectations --
    remove_addon = $("section #remove-addon");
    table        = remove_addon.find("table");
    btn_remove   = remove_addon.find("#btn-remove");
    
    // -- events --
    /*OK*/btn_remove.click(function(){
        var checkeds   = table.find(":checked");
        var checkedIds = checkeds.map(function(){
                              return $(this).data('idlevel');
                         }).toArray();
        var data = {
            action  : "removeAddons",
            idLevels: checkedIds
        };
        var callback = function(data){
            var response = $.parseJSON(data);
            if(response.deleted){
                checkeds.each(function(){
                    $(this).parents('tr').remove();
                });
            }
            message.html( response.deleted ?
                "<h4 class='ajax-success'>Levels deleted</h4>" :
                "<h4 class='ajax-error'>Internal error</h4><p>Unable to delete "
                          + "these levels and their associated scores.</p>" );
        };
        ajaxOperator(data, callback);
    });
}
/*OK*/$(document).ready(function(){
    ajaxControls();
    uploadControls();
    removeControls();
});