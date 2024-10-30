<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 *
 * @package		MMDHGMemberMailDrop
 * @since		1.0.0
 * @author		Hudson Group
*/
?>

<div class="wrap">
	<div class="mmd-meta-box-main">
		<?php do_action('mmdhg_list_before_header'); ?>
		<div class="mmd-meta-box-header">
			<h2><span><?php echo esc_html(apply_filters('mmdhg_add_new_mail_title', 'Add New Mail')); ?></span></h2>
		</div>
		<div class="mmd-meta-box-body">
			<div class="mmdrow">
				<div class="mmdcols">
					<div class="mmd-main-stage">
                        <div class="mmd-table-header">
                            <div class="mmd-stage-title mmd-title-form">
                                <div class="mmd-title">
                                    <span><?php echo esc_html(apply_filters('mmdhg_add_new_mail_title_form', 'Add New Mail')); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="mmd-mail-form-stage">
                            <form id="mmd-add-mail-form" class="mmd-form">
                                <?php echo MMDHGMemberMailDrop::mmdhg_nonce('mmd-add-mail-form'); ?>
                                <input type="hidden" name="mmd-id" id="mmd-id" value="">
                                <div class="mmd-input-h">
                                    <label class="mmd-input-lbl"><?php esc_html_e( 'Send Email To', 'member-mail-drop' ); ?></label>
                                    <!-- <input type="text" name="mmd-user-to" class="mmd-inputs" id="mmd-user-to" /> -->
                                    <select name="mmd-user-to" id="mmd-user-to" class="mmd-inputs mmd-hfsselect" multiple>
                                        <?php 
                                            echo MMDHGAdminClass()->mmdhg_get_user_option();
                                        ?>
                                    </select>
                                    <label class="mmd-checkbox-container" mmd-tooltip="Click to disable reply">
                                        <input type="checkbox" name="mmd-allow-reply" class="mmd-inputs mmd-checkbox mmd-fw dn" id="mmd-allow-reply" checked>
                                        <span class="mmd-checkmark"></span>
                                        <?php esc_html_e( 'Allow Reply', 'member-mail-drop' ); ?>
                                    </label>
                                    <label class="mmd-checkbox-container" mmd-tooltip="Click to notify external user email">
                                        <input type="checkbox" name="mmd-send-notif" class="mmd-inputs mmd-checkbox mmd-fw dn" id="mmd-send-notif">
                                        <span class="mmd-checkmark"></span>
                                        <?php esc_html_e( 'Send Email Notification', 'member-mail-drop' ); ?>
                                    </label>
                                </div>
                                <div class="mmd-input-h">
                                    <label class="mmd-input-lbl"><?php esc_html_e( 'Subject', 'member-mail-drop' ); ?></label>
                                    <input type="text" name="mmd-subject" class="mmd-inputs mmd-fw" id="mmd-subject" />
                                </div>
                                <div class="mmd-input-h">
                                    <label class="mmd-input-lbl"><?php esc_html_e( 'Body', 'member-mail-drop' ); ?></label>
                                    <?php MMDHGMemberMailDrop::mmdhg_add_tinymce_field("", "mmd-mail-body"); ?>
                                </div>
                                <div class="mmd-input-h">
                                    <label class="mmd-input-lbl"><?php esc_html_e( 'Select Folder', 'member-mail-drop' ); ?></label>
                                    <select name="mmd-folder" id="mmd-folder" class="mmd-inputs">
                                        <option value="select-folder"><?php esc_html_e( '-- Select Folder --', 'member-mail-drop' ); ?></option>
                                        <?php
                                    
                                            $child_pages_args = MMDHGAdminClass::mmdhg_folder_query();
                                            if ($child_pages_args) {
                                                
                                                if (!empty($search_query)) {
                                                    $child_pages_args['s'] = $search_query;
                                                }

                                                $child_pages_query = new WP_Query($child_pages_args);

                                                if ($child_pages_query->have_posts()) {
                                                    while ($child_pages_query->have_posts()) {
                                                        $child_pages_query->the_post();
                                                        printf(
                                                            '<option value="%s">%s</option>',
                                                            esc_attr(get_the_ID()),
                                                            esc_html(get_the_title())
                                                        );
                                                    }
                                                    wp_reset_postdata();
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="mmd-input-h">
                                    <div class="" draggable="true">
                                        <div id="dropzone" class="dropzone-upload">
                                            <img class="dropzone-img" src="<?php echo esc_url(MMDHG_PLUGIN_URL . 'assets/img/attachment.png'); ?>">
                                            <button class="mmd-btn t1" id="file-drop-btn"><img src="<?php echo esc_url(MMDHG_PLUGIN_URL . 'assets/img/upload.png'); ?>"><div><?php esc_html_e( 'Upload File', 'member-mail-drop' ); ?></div></button>
                                            <strong><?php esc_html_e( 'or drop a file', 'member-mail-drop' ); ?></strong>
                                            <input type="file" class="dn" id="file-drop" multiple />
                                        </div>
                                    </div>
                                    <div class="file-uploads-list-holder">
                                        <input type="hidden" name="mmd-mail-attachment" id="mmd-mail-attachment">
                                        <ul class="file-uploads-list" id="file-uploads-list"></ul>
                                    </div>
                                </div>
                                <div class="mmd-input-h">
                                    <div class="mmd-sub-field-holder" style="display: inline-block;">
                                        <label class="mmd-input-lbl"><?php esc_html_e( 'Publish', 'member-mail-drop' ); ?></label>
                                        <select name="mmd-publish" id="mmd-publish" class="mmd-inputs">
                                            <option value="immediately"><?php esc_html_e( 'Immediately', 'member-mail-drop' ); ?></option>
                                            <option value="schedule"><?php esc_html_e( 'Schedule', 'member-mail-drop' ); ?></option>
                                        </select>
                                    </div>
                                    <div class="mmd-sub-field-holder dn" id="mmd-publish-sched" style="display: inline-block;">
                                        <div class="mmd-sub-field">
                                            <label class="mmd-input-lbl"><?php esc_html_e( 'Date', 'member-mail-drop' ); ?></label>
                                            <input type="date" class="mmd-inputs mmd-inputs-date" id="mmd-publish-date" name="mmd-publish-date">
                                        </div>
                                        <div class="mmd-sub-field">
                                            <label class="mmd-input-lbl"><?php esc_html_e( 'Time', 'member-mail-drop' ); ?></label>
                                            <input type="time" class="mmd-inputs mmd-inputs-date" id="mmd-publish-time" name="mmd-publish-time">
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
		<div class="mmd-meta-box-footer">
		</div>
	</div>
</div>
