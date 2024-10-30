<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit; 


$mailref = get_post($id)->post_title;
$meta = MMDHGAdminClass::mmdhg_meta_query($id);
$posted = get_the_time('U', $id);
$posted_date = get_the_date('F j, Y', $id);
$posted_time = get_the_time( 'G:i', $id );


if($meta['is_read'] == '1') {
    $read = 'Read'; 
} else {
    $read = 'Not Read';  
}
$recipients = $meta['recipient'];
$attachments = $meta['mail_attchment'];
            
?>

    <div class="mmd-mail-view">
        <?php include MMDHG_PLUGIN_DIR . 'core/includes/mmd-mail-body.php';	?>
        <div class="mmd-reply-list">
			<div class="mmdrow">
                <div class="mmdcols mmdcols10 mob-mmdcols10">
                    <?php 
                        $argsreplies = array(
                            'post_type'  		=> '_mmd',
                            'orderby'    		=> 'date',
                            'order'      		=> 'ASC',
                            'post_status '		=> 'publish',
                            'post_parent'       => $id,
                            'meta_query'		=> array( 
                                'relation'		=> 'AND',
                                array (
                                    'key'			=> 'deleted',
                                    'value'   		=> '0',
                                    'compare' 		=> '=',
                                )
                            ),
                        );
                        
                        $child_posts = get_posts($argsreplies);

                        foreach ($child_posts as $child_post) {

                            setup_postdata( $child_post );

                            $reply_reference = $child_post->post_title;
                            $child_post_content = $child_post->post_content;
                            $reply_id = $child_post->ID;

                            $reply_reference = $child_post->post_title;
                            $reply_meta = MMDHGAdminClass::mmdhg_meta_query($reply_id);
                            $reply_posted = get_the_time('U', $id);
                            $reply_posted_date = get_the_date('F j, Y', $reply_id);
                            $reply_posted_time = get_the_time( 'G:i', $reply_id );
                            
                            
                            if($reply_meta['is_read'] == '1') {
                                $reply_read = 'Read'; 
                            } else {
                                $reply_read = 'Not Read';  
                            }

                            
                            $reply_attachments = $reply_meta['mail_attchment'];
                            
                            include MMDHG_PLUGIN_DIR . 'core/includes/mmd-mail-replies.php';
                        }

                    ?>
                </div>
            </div>
        </div>
        
		<?php do_action('mmdhg_after_mail_view', $id); ?>


    </div>
