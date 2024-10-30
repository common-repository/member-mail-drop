jQuery(function($){

    var mmd_d_attach = $("#mmd-d-attach"),
        mmd_mail_forward = $("#mmd-mail-forward"),
        mmd_mail_reply = $("#mmd-mail-reply"),
        mmd_forward_mail = $("#mmd-forward-mail");
        

    

    
    
    // mmd_d_attach.on('click', function(e){
    //     e.preventDefault();
    //     var event = $(this);
    //     mail_id = event.attr('mail-id');
    //     nonce = event.attr('nonce');
    //     var ajax_data = {
    //         action:             'mmd_zip_attachements',
    //         _nonce:             nonce,
    //         mail_id:            mail_id,

    //     };

    //     $.ajax({
    //         url: mmdscripts.ajaxurl,
    //         type: 'POST',
    //         dataType: 'json',
    //         data: ajax_data,
    //         success: function(response) {
    //             window.location.href = response.data;
    //         },
    //         error: function(xhr, status, error) {
    //             console.log(xhr.responseText);
    //         }
    //     });
    // });

    $('.mmd-d-attach').each(function() {
        var event = $(this);
    
        event.on('click', function(e) {
            e.preventDefault();
    
            var mail_id = event.attr('mail-id');
            var nonce = event.attr('nonce');
            
            var ajax_data = {
                action: 'mmd_zip_attachements',
                _nonce: nonce,
                mail_id: mail_id,
            };
    
            $.ajax({
                url: mmdscripts.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: ajax_data,
                success: function(response) {
                    window.location.href = response.data;
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    });

    // mmd_mail_reply.each(function(){
    //     $(this).click(function(e){
    //         e.preventDefault();
    //         var event = $(this);
    //         console.log('click');
    //         $("#mmd-reply-box").toggleClass("dn");
    //     }); 
    // });
    mmd_mail_reply.click(function(e){
        e.preventDefault();
        var event = $(this);
        $("#mmd-reply-box").toggleClass("dn");
    }); 
    mmd_mail_forward.click(function(e){
        e.preventDefault();
        var event = $(this);
        $("#mmd-forward-box").toggleClass("dn");
    }); 

    mmd_forward_mail.on('submit',function(e) {
        e.preventDefault();
        var _nonce = $(e.currentTarget).find("#_nonce").val(),
            mmd_id = $("#mmd-id").val(),
            mmd_forward_email = $("#mmd-forward-email").val();
        
        var ajax_data = {
            action: 'mmd_forward_mail',
            _nonce: _nonce,
            mmd_id: mmd_id,
            mmd_forward_email: mmd_forward_email,
        };
        console.log(ajax_data);

        $.ajax({
            url: mmdscripts.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: ajax_data,
            success: function(result) {
                if(result.success){
                    mmd_sucess_handler(result.message);
                } else {
                    mmd_error_handler(result.message);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });

        // $.post( mmdscripts.ajaxurl, ajax_data, function( result ){
        //     if(result.success){
        //         mmd_sucess_handler(result.message);
        //     } else {
        //         mmd_error_handler(result.message);
        //     } 
        // });

    });

    if ($('#mmd-default-forward').is(':checked')) {
        $("#mmd-forward-email").prop('disabled', true);
    }

    $('#mmd-default-forward').change(function() {
        if ($(this).is(':checked')) {
            $("#mmd-forward-email").prop('disabled', true);
        } else {
            $("#mmd-forward-email").prop('disabled', false);
        }
    });


    
});

