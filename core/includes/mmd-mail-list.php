<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit; 

$current_url = sanitize_url($_SERVER['REQUEST_URI']);
?>

<div class="mmd-main-stage">
    <div class="mmd-table-list">
        <div class="mmd-table-header">
            <div class="mmd-stage-title">
                
                <div class="mmd-title">
                    <span><?php esc_html_e( 'Emails', 'member-mail-drop' ); ?></span>
                </div>
                <div class="mmd-search">

                    <form action="<?php echo esc_url(sanitize_text_field($current_url)); ?>" method="GET">
                        <?php
                            $search_query = '';
                            $admin_url = admin_url('admin.php?page=mmd-admin');
                            
                            if (is_admin()) {
                                if(isset($_GET['page'])){
                                    $page = sanitize_text_field($_GET['page']);
                                    printf(
                                        '<input type="hidden"class="mmd-inputs mmd-table-s" name="page" value="%s" />',
                                        esc_html($page)
                                    );
                                }
                                $reset_btn = '<a href="'.esc_url($admin_url).'" class="mmd-clear-form">Clear</a>';
                            } else {
                                if(isset($_GET['mmd'])){
                                    $mmd = sanitize_text_field($_GET['mmd']);
                                    printf(
                                        '<input type="hidden"class="mmd-inputs mmd-table-s" name="mmd" value="%s" />',
                                        esc_html(sanitize_text_field($mmd))
                                    );
                                }
                                $reset_btn = '';
                            }
                              
                            if(isset($_GET['ms'])){
                                $search_query = sanitize_text_field($_GET['ms']);
                            }


                            printf(
                                '<input class="mmd-inputs mmd-table-s" name="ms" value="%s" placeholder="Search" /><button class="mmd-table-btn t1"><img src="%s"></button>%s',
                                esc_html(sanitize_text_field($search_query)),
                                esc_url(MMDHG_PLUGIN_URL . 'assets/img/search.png'),
                                $reset_btn
                            );
                            
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="mmd-table-stage">
            <table class="mmd-table table-responsive" cellspacing="0">
                
                <?php if(!$is_front){ ?>
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Subject', 'member-mail-drop' ); ?></th>
                        <th><?php esc_html_e( 'Recipient', 'member-mail-drop' ); ?></th>
                        <th><?php esc_html_e( 'Date', 'member-mail-drop' ); ?></th>
                    </tr>
                </thead>
                <?php } ?>
                <tbody>
                <?php
                
                    if ( $the_query->have_posts() ) {
                        while ( $the_query->have_posts() ) {
                                $the_query->the_post();
                                $name = get_post()->post_title;
                                $id = get_post()->ID;
                                $meta = MMDHGAdminClass::mmdhg_meta_query($id);
                                $posted = get_the_time('U');
                                $posted_date = get_the_date('F j, Y');
                                $posted_time = get_the_time( 'G:i' );
                                $recipient = $meta['recipient'];
                                
                                
                                printf(
                                    '<tr class="mmd-tr t1" mmd-unique="%s"><td class="%s"><span class="mmd-list-name"><a href="%s">%s</a></span></td>',
                                    esc_html($id),
                                    esc_html(($is_front) ? 'mmdcols3' : 'mmdcols5'),
                                    esc_html( MMDHGFrontClass::mmdhg_mail_url($id)),
                                    esc_html( ($is_front) ? MMDHGAdminClass::mmdhg_limit_content($meta['subject'], 30) : $meta['subject'], 'member-mail-drop' )
                                );
                                
                                if($is_front){ 
                                    printf(
                                        '<td class="mmdcols4">%s</td>',
                                        wp_kses_post(strip_tags(MMDHGAdminClass::mmdhg_limit_content(get_post()->post_content, 85)))
                                    );
                                } 
                                
                                if(!$is_front){ ?>
                                    <td class="mmdcols2">
                                        <?php 
                                        
                                            if ($recipient === 'admin') {
                                                $rec_name = 'Admin';
                                            } else {
                                                $u_info = get_userdata($recipient);
                                            
                                                if ($u_info && is_object($u_info)) {
                                                    $rec_name = $u_info->display_name;
                                                } else {
                                                    // Handle the case where $u_info is not a valid user object
                                                    $rec_name = 'Unknown';
                                                }
                                            }
                                            
                                            printf(
                                                '<span class="mmd-recipients">%s</span>',
                                                esc_html($rec_name)
                                            );
                                        ?>
                                    </td>
                                <?php } ?>
                                
                                <td class="mmdcols4 tar mmd-list-date">
                                    <?php
                                        $current_status = get_post_status ( $id );
                                        if ($current_status === 'future') { 
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
                                        if (!is_admin()) {
                                    ?>
                                    <ul class="mmd-list-action">
                                        <?php wp_kses_post(MMDHGAdminClass::mmdhg_list_actions(false, array('starred', 'important', 'trash','marked-open'), array('starred', 'important', 'trash','is_read'), $id, 'colored')); ?>
                                    </ul>
                                <?php } ?>
                                </td>
                            </tr>
                        <?php
                        
                        }
                        
                        wp_reset_postdata();
                        parent::mmdhg_pagination( $the_query, $offset['paged'], $offset['num_per_page'], 5 );
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>