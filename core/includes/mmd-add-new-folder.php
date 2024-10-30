<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @package MMDHGMemberMailDrop
 * @since   1.0.0
 * @author  Hudson Group
 */

// Initialize search query
$search_query = isset($_GET['ms']) ? sanitize_text_field($_GET['ms']) : '';

// Generate admin URL for the "Clear" button
$admin_url = admin_url('admin.php?page=mmd-new-folder');
$reset_btn = '<a href="' . esc_url($admin_url) . '" class="mmd-clear-form">Clear</a>';


$current_url = sanitize_url($_SERVER['REQUEST_URI']);
?>

<div class="wrap">
    <div class="mmd-meta-box-main">
        <?php do_action('mmdhg_list_before_header'); ?>
        <div class="mmd-meta-box-header">
            <?php
                printf(
                    '<h2><span>%s</span></h2>',
                    esc_html(apply_filters('mmdhg_add_new_mail_title', 'Add New Folder'))
                );
            ?>
        </div>
        <div class="mmd-meta-box-body">
            <div class="mmdrow">
                <div class="mmdcols">
                    <div class="mmd-main-stage">
                        <div class="mmd-table-header">
                            <div class="mmd-stage-title mmd-title-form">
                                <div class="mmd-title">
                                    <?php
                                        printf(
                                            '<span>%s</span>',
                                            esc_html(apply_filters('mmdhg_add_new_mail_title_form', 'Add New Folder'))
                                        );
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="mmd-mail-form-stage">
                            <form id="mmd-add-folder-form" class="mmd-form">
                                <?php echo MMDHGMemberMailDrop::mmdhg_nonce('mmd-add-folder-form'); ?>
                                
                                <input type="hidden" name="mmd-folder-id" class="mmd-inputs mmd-fw" id="mmd-folder-id" value="" />
                                <div class="mmd-input-h">
                                    <label class="mmd-input-lbl"><?php esc_html_e('Folder Name', 'member-mail-drop'); ?></label>
                                    <input type="text" name="mmd-folder-name" class="mmd-inputs mmd-fw" id="mmd-folder-name" placeholder="Folder Key" />
                                </div>
                                <div class="mmd-input-h">
                                    <label class="mmd-input-lbl"><?php esc_html_e('Folder Key', 'member-mail-drop'); ?></label>
                                    <input type="text" name="mmd-folder-key" class="mmd-inputs mmd-fw" id="mmd-folder-key" placeholder="folder-key" />
                                </div>
                                <div class="mmd-input-h">
                                    <input type="submit" name="mmd-submit-new-folder" id="mmd-submit-new-folder" class="mmd-btn t1" value="<?php esc_html_e('Submit', 'member-mail-drop'); ?>" />
                                    <?php echo MMDHGAdminClass::mmdhg_form_notice($text_notice = ''); ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mmd-meta-box-footer"></div>
        <div class="mmd-main-stage">
            <div class="mmd-table-list">
                <div class="mmd-table-header">
                    <div class="mmd-stage-title">
                        <div class="mmd-title">
                            <span><?php esc_html_e( 'Folders', 'member-mail-drop' ); ?></span>
                        </div>
                        <div class="mmd-search">
                            <form action="<?php echo esc_url(sanitize_url($current_url)); ?>" method="GET">
                                <?php
                                    if(isset($_GET['page'])){
                                        $page = sanitize_text_field($_GET['page']);
                                        echo '<input type="hidden"class="mmd-inputs mmd-table-s" name="page" value="'.$page.'" />';
                                    }
                                    printf(
                                        '<input class="mmd-inputs mmd-table-s" name="ms" value="%s" placeholder="Search" />',
                                        esc_html(sanitize_text_field($search_query))
                                    );
                                ?>
                                <button class="mmd-table-btn t1"><img src="<?php echo esc_url(MMDHG_PLUGIN_URL . 'assets/img/search.png'); ?>"></button>
                                <?php echo $reset_btn; ?>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="mmd-table-stage">
                    <table class="mmd-table table-responsive" cellspacing="0">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Folder', 'member-mail-drop' ); ?></th>
                                <th><?php esc_html_e( 'Status', 'member-mail-drop' ); ?></th>
                                <th><?php esc_html_e( 'Action', 'member-mail-drop' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
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
                                                $id = get_the_ID();
                                                printf(
                                                    '<tr class="mmd-tr"><td id="%s">%s</td><td><span>%s</span></td>',
                                                    esc_attr($id),
                                                    esc_html(get_the_title()),
                                                    esc_html(MMDHGAdminClass::mmdhg_get_folder_status($id))
                                                );
                                                echo '<td><ul class="mmd-list-action">';
                                                echo wp_kses_post(MMDHGAdminClass::mmdhg_list_actions(true, array('edit', 'change-status', 'trash'), array('edit_folder', 'change_status', 'trash_folder'), $id, 'colored'));
                                                echo '</ul></td> </tr>';
                                                

                                            }
                                            wp_reset_postdata();
                                        } else {
                                            printf(
                                                '<tr><td id="">%s</td></tr>',
                                                esc_html('No folder found.')
                                            );
                                        }
                                    }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
