<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="mmd-mail-view-header">
    <div class="mmdrow">
        <div class="mmdcols mmdcols6 mob-mmdcols10">
            <div class="mmd-view-title">
                
                <?php 
                    printf(
                        '<h3>%s</h3>',
                        esc_html( $meta['subject'], 'member-mail-drop' )
                    );
                ?>
                
                <?php
                    $current_status = get_post_status ( $id );
                    if ($current_status === 'future') { 
                        printf(
                            '<span class="mmd-date d-ib" style="margin-right: 10px;">Scheduled ~ %s at %s</span>',
                            esc_html($posted_date),
                            esc_html($posted_time),
                        );
                     } else { 
                        printf(
                            '<span class="mmd-date d-ib" style="margin-right: 10px;">%s at %s</span>',
                            esc_html($posted_date),
                            esc_html($posted_time),
                        );
                    }
                ?>
            </div>
        </div>
        <div class="mmdcols mmdcols4 mob-mmdcols10 t-r">
            <div class="mmd-view-action">
                <ul class="mmd-list-action d-ib">
                    <?php wp_kses_post(MMDHGAdminClass::mmdhg_list_actions(false, array('starred', 'important', 'trash','marked-open'), array('starred', 'important', 'trash','is_read'), $id, 'colored')); ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="mmd-mail-view-body">
        <div class="mmdrow">
            <div class="mmdcols mmdcols10 mob-mmdcols10">
                <div class="mmd-mail-view-content">
                    <?php echo wp_kses_post(get_post($id)->post_content); ?>
                </div>
            </div>
        </div>
    </div>
    <?php  
        if (is_array($attachments)) : 
            $attachment_count = count($attachments) - 1;
            
            if($attachment_count > 0):
    ?>
            
    <div class="mmd-mail-view-attachment">
        <div class="mmdrow">
            <div class="mmdcols mmdcols10 mob-mmdcols10">
                <div class="mmd-attachment-count">
                    <?php
                        printf(
                            '<strong>%s Attachment%s</strong>',
                            esc_html($attachment_count),
                            esc_html(($attachment_count > 1) ? 's' : ''),
                        );
                    ?>
                    <a href="#" class="mmd-d-attach" id="<?php echo esc_attr('mmd-d-attach-'.$id); ?>" mail-id="<?php echo esc_attr($id); ?>" nonce="<?php echo esc_attr(MMDHGMemberMailDrop::mmdhg_link_nonce('download-nonce')); ?>" mmd-tooltip="<?php echo esc_attr('Download All'); ?>">
                        <img src="<?php echo esc_url(MMDHGMemberMailDrop()->mmdhg_icons('download')); ?>" class="mmd-download-all">
                    </a>
                </div>
                <div class="mmd-main-attachement">
                    <ul class="mmd-main-attachement-list">
                        <?php
                            foreach($attachments as $attachment){
                                if($attachment){
                                    $filename = substr($attachment, strrpos($attachment, '/') + 1);
                                    $img_thumb = MMDHGShortcode::mmdhg_get_file_extension_img($filename);
                                    ?>
                                        <li>
                                            <div class="mmd-img-attachement">
                                                <div class="mmd-attachment-name">
                                                    <img src="<?php echo esc_url($img_thumb); ?>">
                                                    <?php
                                                        printf(
                                                            '<span>%s</span>',
                                                            esc_html(substr($filename, 0, 12))
                                                        );
                                                    ?>
                                                </div>
                                                <div class="mmd-attachment-overlay">
                                                    <?php
                                                        printf(
                                                            '<span>%s</span><a href="%s"><img src="%s" class="mmd-attachment-d" mmd-tooltip="Download"></a>',
                                                            esc_html(substr($filename, 0, 30)),
                                                            esc_url($attachment),
                                                            esc_url(MMDHGMemberMailDrop()->mmdhg_icons('download'))
                                                        );
                                                    ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php
                                }
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php 
        endif; 
    endif; 
    ?>
    
</div>