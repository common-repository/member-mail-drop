jQuery(function($){

    var dropzone = $('#dropzone'),
    filedrop = $('#file-drop'),
    filedropbtn = $('#file-drop-btn'),
    mmd_publish = $("#mmd-publish"),
    mmd_publish_sched = $("#mmd-publish-sched"),
    mmd_publish_date = $("#mmd-publish-date"),
    mmd_publish_time = $("#mmd-publish-time"),
    mmd_publish_sched = $("#mmd-publish-sched"),
    mmd_add_mail_form = $("#mmd-add-mail-form"),
    mmd_add_folder_form = $("#mmd-add-folder-form"),
    mmd_subject = $("#mmd-subject"),
    mmd_id = $("#mmd-id"),
    mmd_s_user_to = $("select#mmd-user-to"),
    mmd_i_user_to = $("input#mmd-user-to"),
    mmd_allow_reply = $("input#mmd-allow-reply"),
    mmd_send_notif = $("input#mmd-send-notif"),
    mmd_action_icon = $(".mmd-action-icon"),
    mmd_folder = $("#mmd-folder"),
    mmd_folder_name = $("#mmd-folder-name"),
    mmd_folder_key = $("#mmd-folder-key"),
    mmd_folder_id = $("#mmd-folder-id"),
    mmd_mail_attachment = $('#mmd-mail-attachment');

    if(mmd_s_user_to.length > 0){

        var mmd_select = new hfsSelect({ 
            select: '#mmd-user-to', 
            placeholder: 'Select Users', 
            showSearch: true, 
            searchText: 'User not found.',
            settings: {
                hideSelected: true,
            }
        });
    }

    function init(){
        mmd_publish_change();
    }

    filedropbtn.click(function(e){
        e.preventDefault();
        filedrop.click();
    });

    // mmd_add_new_folder_btn.click(function(e){
    //     e.preventDefault();
    //     mmd_new_folder_action.toggleClass('dn');
    //     mmd_folder_name.focus();
    // });

    dropzone.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
      })

    dropzone.on('dragover dragenter', function() {
        $(this).addClass('is-dragover');
      })
    dropzone.on('dragleave dragend drop', function() {
        $(this).removeClass('is-dragover');
      })  
      
    dropzone.on('drop',function(e) {
        var files = e.originalEvent.dataTransfer.files;
        upload_image_mp(files);
    });

    filedrop.on('change',function(e) {
        // var files = e.originalEvent.dataTransfer.files;
        $this = $(this);
        files = $this.prop('files');
        upload_image_mp(files);
    });

    // ajax upload
    var upload_image_mp = function(files){

        $this = $('#file-drop').prop('files',files);
        file_list = $('#file-uploads-list');

        // console.log($this);

            file_obj = $this.prop('files');
            form_data = new FormData();
            for(i=0; i<file_obj.length; i++) {
                form_data.append('file[]', file_obj[i]);
            }
            form_data.append('action', 'file_upload');

            $.ajax({
                url: mmdscripts.ajaxurl,
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data,
                dataType : "json",
                success: function (response) {
                    if(response.success){
                        $this.val('');
                        add_remove_img(0, response.message[0]);
                        file_list.append('<li class="file-uploads-list-item">'+response.message[3]+'<div class="fu-file-item-close"><span class="lr-close-login" path-id="'+response.message[1]+'/'+response.message[2]+'/'+response.message[3]+'" full-url="'+response.message[0]+'"><img src="'+mmdscripts.plugin_url+'assets/img/delete.png"></span></div></li>');
                        remove_upload();
                    } else {
                        mmd_error_handler(response.message, false);
                    }
                }
            });
    }

    // remove upload
    var remove_upload = function(e){
        remove_img = $('.lr-close-login');
        remove_img.click( function(){
            $this = $(this);
            path_id = $this.attr('path-id');
            full_img_url = $this.attr('full-url');

            var remove_data = {
                action:                'mp_delete_img_upload',
                path_id:               path_id,
            };

            // $.post( mmdscripts.ajaxurl, remove_data, function( data ){
            //     console.log(data);
                
            // });

            
            $.ajax({
                url: mmdscripts.ajaxurl,
                type: 'POST',
                data: remove_data,
                dataType : "json",
                success: function (response) {
                    if(response.success){
                        add_remove_img(1, full_img_url);
                        $this.parents("li").fadeOut().remove();
                    } else {
                        mmd_error_handler(response.message, false);
                    }
                }
            });
        });
    }

    // remove added image
    var add_remove_img = function( $is_remove, $full_img_url ){
        images_url = mmd_mail_attachment.val();
        if ($is_remove === 1) {
            $full_img_url = ","+$full_img_url;
            new_url = images_url.replace($full_img_url, "");
            mmd_mail_attachment.val(new_url);
        } else if($is_remove === 0){
            mmd_mail_attachment.val(images_url+','+$full_img_url);
        }
    }

    var mmd_publish_change = function(){
        mmd_publish.on('change', function(){
        var selectval = $(this).val();
        if (selectval == "schedule"){
            mmd_publish_sched.fadeIn(500).removeClass('dn');
        } else {
            mmd_publish_sched.fadeOut(500).addClass('dn');
        }
        });
    }

    
    mmd_add_mail_form.on('submit', function(e){
        e.preventDefault();
        var event = $(this);
        submit_mmd_mail_form(event, e);
    });

    var submit_mmd_mail_form = function(event, e){
        
        var _nonce = $(e.currentTarget).find("#_nonce").val();
        
        var mail_id = mmd_id.val();
        var mmd_user_to = mmd_i_user_to.val();
        if(mail_id === ''){
            var recipient = mmd_select.selected();
            recipient = recipient.join(",");
        } else {
            recipient = mmd_user_to;
        }
        var mail_subject = mmd_subject.val();
        var mail_body = tinymce.get("mmd-mail-body").getContent();
        var mail_attchment = mmd_mail_attachment.val();
        var is_publish = mmd_publish.val();
        var pub_date = mmd_publish_date.val();
        var pub_time = mmd_publish_time.val();
        var folder = mmd_folder.val();
        var allow_reply = '0';
        var send_notif = '0';

        if (mmd_allow_reply.prop("checked")) {
            allow_reply = '1';
        }
        if (mmd_send_notif.prop("checked")) {
            send_notif = '1';
        }

        var ajax_data = {
            action:                     'mmd_submit_mail',
            _nonce:                     _nonce,
            recipient:                  recipient,
            mail_subject:               mail_subject,
            mail_body:                  mail_body,
            mail_attchment:             mail_attchment,
            is_publish:                 is_publish,
            pub_date:                   pub_date,
            pub_time:                   pub_time,
            mail_id:                    mail_id,
            allow_reply:                allow_reply,
            send_notif:                 send_notif,
            current_url:                window.location.href,
            folder:                     folder
        };

        // $.post( mmdscripts.ajaxurl, ajax_data, function( result ){
        //     alert(result);
        //     if(result.success){
        //         mmd_sucess_handler(result.message, true);
        //     } else {
        //         mmd_error_handler(result.message, false);
        //     }
        // });

        
        $.ajax({
            url: mmdscripts.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: ajax_data,
            success: function(result) {
                if(result.success){
                    mmd_sucess_handler(result.message, true, result.redirect_url);
                } else {
                    mmd_error_handler(result.message, false);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
        
    }  

    function folder_key_generate(inputString) {
        return inputString.replace(/\s+/g, '-').toLowerCase();
    }
    
    mmd_folder_name.on('input', function(e){
        var folder_name = $(this).val();
        var folder_name = folder_name.replace(/[^a-zA-Z0-9\s-]/g, ''); // Allow only alphanumeric and spaces
        $(this).val(folder_name);
        var folder_key = folder_key_generate(folder_name);
        mmd_folder_key.val(folder_key);
    });

    mmd_folder_key.on('input', function(e){
        var folder_key = $(this).val();
        var folder_key = folder_key.replace(/[^a-zA-Z0-9\s-]/g, ''); // Allow only alphanumeric and spaces
        var folder_key = folder_key_generate(folder_key);
        $(this).val(folder_key);
    });

    mmd_add_folder_form.on('submit', function(e){
        e.preventDefault();
        
        var _nonce = $(e.currentTarget).find("#_nonce").val(),
        mmd_folder_id = $("#mmd-folder-id").val(),
        mmd_folder_name = $("#mmd-folder-name").val(),
        mmd_folder_key = $("#mmd-folder-key").val();

        console.log(_nonce);
        var ajax_data = {
            action:                     'mmd_add_new_folder_mail',
            _nonce:                     _nonce,
            mmd_folder_id:              mmd_folder_id,
            mmd_folder_name:            mmd_folder_name,
            mmd_folder_key:             mmd_folder_key
        };

        // $.post( mmdscripts.ajaxurl, ajax_data, function( result ){
        //     console.log(result);
        //     if(result.success){
        //         mmd_sucess_handler(result.message);
        //     } else {
        //         mmd_error_handler(result.message);
        //     }
        // });

        
        $.ajax({
            url: mmdscripts.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: ajax_data,
            success: function(result) {
                if(result.success){
                    mmd_sucess_handler(result.message, true);
                } else {
                    mmd_error_handler(result.message, false);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    });

    
    mmd_action_icon.click(function(e){
        e.preventDefault();
        var event = $(this);
        mmd_actions(event);
    }); 

    var mmd_actions = function(thisevent){
        data_unique = thisevent.attr('data-unique');
        data_action = thisevent.attr('data-action');
        nonce = thisevent.attr('nonce');

        var ajax_data = {
                action:             'mdd_actions_trigger',
                _nonce:             nonce,
                data_unique:        data_unique,
                data_action:        data_action,

        };
        $.post( mmdscripts.ajaxurl, ajax_data, function( data ){
            var data = JSON.parse(data);
            if(data.success == true){
                if(data.data_action == 'edit_folder'){
                    mmd_folder_name.val(data.title).focus();
                    mmd_folder_key.val(data.slug);
                    mmd_folder_id.val(data.id);
                } else {
                    alert(data.message);
                    is_reload(data.is_reload);
                }
            } else {
                alert(data.message);
                is_reload(data.is_reload);
            } 
        });
    }

    var is_reload = function(checkdata){
        if(checkdata){
            setTimeout(function(){
                location.reload();
            }, 500); 
        }
    }
    

    init();


});

