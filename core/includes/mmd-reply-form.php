<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit; 

if($meta['is_reply'] == '1'): ?>

    <div class="mmd-reply-btn-box">
        <div class="mmdrow">
            <div class="mmdcols mmdcols10 mob-mmdcols10">
                <a href="#" class="mmd-reply-btn" id="mmd-mail-reply"><img src="<?php echo esc_url(MMDHGMemberMailDrop::mmdhg_icons('reply')); ?>" class="mmd-lm-icon"><?php esc_html_e( 'Reply', 'member-mail-drop' ); ?></a>
            </div>
        </div>
    </div>


    <div class="mmd-reply-box dn" id="mmd-reply-box">
        <div class="mmdrow">
            <div class="mmdcols mmdcols10 mob-mmdcols10">
                    <div class="mmd-mail-form-stage">
                        <form id="mmd-add-mail-form" class="mmd-form">
                            <?php 
                                echo MMDHGMemberMailDrop::mmdhg_nonce('mmd-add-mail-form'); 
                            
                                printf(
                                    '<input type="hidden" name="mmd-id" id="mmd-id" value="%s"><input type="hidden" name="mmd-user-to" id="mmd-user-to" value="%s"><input type="hidden" name="mmd-subject" class="mmd-inputs mmd-fw" id="mmd-subject" value="Reply From: %s">',
                                    esc_html($id),
                                    esc_html('admin'),
                                    esc_html($meta['subject'])
                                );
                            ?>
                            <div class="mmd-input-h">
                                <label class="mmd-input-lbl"><?php esc_html_e( 'Reply', 'member-mail-drop' ); ?></label>
                                <?php MMDHGMemberMailDrop::mmdhg_add_tinymce_field("", "mmd-mail-body"); ?>
                            </div>
                            <div class="mmd-reply-attachement" id="mmd-reply-attachement">
                                <div class="mmd-input-h">
                                    <div class="" draggable="true">
                                        <div id="dropzone" class="dropzone-upload">
                                            <img class="dropzone-img" src="<?php echo esc_url(MMDHG_PLUGIN_URL . 'assets/img/attachment.png'); ?>">
                                            <button class="mmd-btn t1" id="file-drop-btn"><img src="<?php echo esc_url(MMDHG_PLUGIN_URL . 'assets/img/upload.png'); ?>"><div><?php esc_html_e( 'Upload File', 'member-mail-drop' ); ?></div></button>
                                            <strong><?php esc_html_e( 'or drop a file', 'member-mail-drop' ); ?></strong>
                                            <input type="file" class="dn" id="file-drop" accept=".doc,.pdf,image/*" multiple />
                                        </div>
                                    </div>
                                    <div class="file-uploads-list-holder">
                                        <input type="hidden" name="mmd-mail-attachment" id="mmd-mail-attachment">
                                        <ul class="file-uploads-list" id="file-uploads-list"></ul>
                                    </div>
                                </div>
                            </div>
                            <div class="mmd-input-h">
                                <input type="submit" name="mmd-submit-script" id="mmd-submit-script" class="mmd-btn t1" value="<?php esc_html_e( 'Send', 'member-mail-drop' );?>" /><?php echo MMDHGAdminClass::mmdhg_form_notice($text_notice = '');?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php 
else:
    ?>
    
    <div class="mmd-reply-btn-box">
        <div class="mmdrow">
            <div class="mmdcols mmdcols10 mob-mmdcols10">
                <?php    
                    printf(
                        '<span><i>%s</i></span>',
                        esc_html( apply_filters('mmdhg_add_reply_notice', 'You cannot reply to this mail.'), 'member-mail-drop' )
                    );
                ?>
            </div>
        </div>
    </div>
    <?php
endif; ?>