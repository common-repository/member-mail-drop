<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="mmd-forward-btn-box">
    <a href="#" class="mmd-reply-btn" id="mmd-mail-forward" mmd-tooltip="<?php echo esc_attr('Forward this mail to any email.')?>"><img src="<?php echo esc_url(MMDHGMemberMailDrop::mmdhg_icons('forward')); ?>" class="mmd-lm-icon"><?php esc_html_e( 'Forward Externally', 'member-mail-drop' ); ?></a>
</div>

<div class="mmd-reply-box dn" id="mmd-forward-box">
    <div class="mmdrow">
        <div class="mmdcols mmdcols10 mob-mmdcols10">
            <div class="mmd-mail-form-stage">
                <form id="mmd-forward-mail" class="mmd-form">
                
                    <div class="mmd-form-heading">
                        <?php
                            printf(
                                '<span>%s</span>',
                                esc_html(apply_filters('mmdhg_forward_heading', 'Forward to external email.'))
                            );
                        ?>
                    </div>
                    <?php echo MMDHGMemberMailDrop::mmdhg_nonce('mmd-forward-mail-form'); ?>
                    <input type="hidden" name="mmd-id" id="mmd-id" value="<?php echo $id; ?>">
                    <div class="mmd-input-h">
                        <label class="mmd-input-lbl"><?php esc_html_e( 'Email', 'member-mail-drop' ); ?></label>
                        <input type="text" name="mmd-forward-email" class="mmd-inputs mmd-fw" id="mmd-forward-email" value="<?php echo esc_attr(MMDHGFrontClass::mmdhg_get_logged_in_user_email()); ?>" disabled />
                        <label class="mmd-checkbox-container" mmd-tooltip="<?php echo esc_attr('Click to forward on different email.'); ?>">
                            <input type="checkbox" name="mmd-default-forward" class="mmd-inputs mmd-checkbox mmd-fw dn" id="mmd-default-forward" checked="">
                            <span class="mmd-checkmark"></span>
                            <?php esc_html_e( 'Forward to default email  ', 'member-mail-drop' ); ?>                                 
                        </label>
                    </div>
                    <div class="mmd-input-h">
                        <input type="submit" name="mmd-submit-script" id="mmd-submit-script" class="mmd-btn t1" value="<?php esc_html_e( 'Send', 'member-mail-drop' );?>" /><?php echo MMDHGAdminClass::mmdhg_form_notice($text_notice = '');?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>