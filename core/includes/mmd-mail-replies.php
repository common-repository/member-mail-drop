<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="mmd-mail-view-header">
    <div class="mmdrow">
        <div class="mmdcols mmdcols10 mob-mmdcols10">
            <div class="mmd-view-title">
                <?php
                    
                    printf(
                        '<h3>Reply from: %s</h3>',
                        esc_html( $meta['subject'])
                    );
                ?>
                <?php
                    $reply_current_status = get_post_status ( $id );
                    if ($reply_current_status === 'future') { 
                        printf(
                            '<span class="mmd-date d-ib">Scheduled ~ %s at %s</span>',
                            esc_html($posted_date),
                            esc_html($posted_time),
                        );
                    } else { 
                        printf(
                            '<span class="mmd-date d-ib">%s at %s</span>',
                            esc_html($posted_date),
                            esc_html($posted_time),
                        );
                    }
                ?>
            </div>
        </div>
    </div>
    <div class="mmd-mail-view-body">
        <div class="mmdrow">
            <div class="mmdcols mmdcols10 mob-mmdcols10">
                <div class="mmd-mail-view-content">
                    <?php echo wp_kses_post($child_post_content); ?>
                </div>
            </div>
        </div>
    </div>
    <?php  
        if(is_array($reply_attachments)): 
            $reply_attachment_count = count($reply_attachments) - 1;
            if($reply_attachment_count > 0):
    ?>
            
    <div class="mmd-mail-view-attachment">
        <div class="mmdrow">
            <div class="mmdcols mmdcols10 mob-mmdcols10">
                <div class="mmd-attachment-count">
                    <?php
                        printf(
                            '<strong>%s Attachment%s</strong>',
                            esc_html($reply_attachment_count),
                            esc_html(($reply_attachment_count > 1) ? 's' : ''),
                        );
                    ?>
                    <a href="#" class="mmd-d-attach" id="<?php echo esc_attr('mmd-d-attach-'.$id); ?>" mail-id="<?php echo esc_attr($reply_id); ?>" nonce="<?php echo esc_attr(MMDHGMemberMailDrop::mmdhg_link_nonce('download-nonce')); ?>" mmd-tooltip="Download All">
                        <img src="<?php echo esc_url(MMDHGMemberMailDrop()->mmdhg_icons('download')); ?>" class="mmd-download-all">
                    </a>
                </div>
                <div class="mmd-main-attachement">
                    <ul class="mmd-main-attachement-list">
                        <?php
                            foreach($reply_attachments as $reply_attachment){
                                if($reply_attachment){
                                    $reply_filename = substr($reply_attachment, strrpos($reply_attachment, '/') + 1);
                                    $reply_img_thumb = MMDHGShortcode::mmdhg_get_file_extension_img($reply_filename);
                                    ?>
                                        <li>
                                            <div class="mmd-img-attachement">
                                                <div class="mmd-attachment-name">
                                                    <img src="<?php echo esc_url($reply_img_thumb); ?>">
                                                    <?php
                                                        printf(
                                                            '<span>%s</span>',
                                                            esc_html(substr($reply_filename, 0, 12))
                                                        );
                                                    ?>
                                                </div>
                                                <div class="mmd-attachment-overlay">
                                                    <?php
                                                        printf(
                                                            '<span>%s</span><a href="%s"><img src="%s" class="mmd-attachment-d" mmd-tooltip="Download"></a>',
                                                            esc_html(substr($reply_filename, 0, 30)),
                                                            esc_url($reply_attachment),
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