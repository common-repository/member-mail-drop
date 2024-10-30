<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *
 * @package		MMDHGMemberMailDrop
 * @since		1.0.0
 * @author		Hudson Group
*/

MMDHGMemberMailDrop::mmdhg_only_logged_in_allowed();

$current_page = get_queried_object();
?>


		<div class="mmd-meta-box-body">
			
			<div class="mmdrow">
				<div class="mmdcols mmdcols2  mob-mmdcols10">
					<div class="mmd-left-menu-stage">
						<ul class="mmd-left-menu">
							<li><a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>?mmd=inbox"><img src="<?php echo esc_url(MMDHGMemberMailDrop()->mmdhg_icons('inbox')); ?>" class="mmd-lm-icon"><span><?php esc_html_e( 'Inbox', 'member-mail-drop' ); ?></span></a></li>
							<li><a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>?mmd=starred"><img src="<?php echo esc_url(MMDHGMemberMailDrop()->mmdhg_icons('starred-c')); ?>" class="mmd-lm-icon mmd-c"><span><?php esc_html_e( 'Starred', 'member-mail-drop' ); ?></span></a></li>
							<li><a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>?mmd=important"><img src="<?php echo esc_url(MMDHGMemberMailDrop()->mmdhg_icons('important')); ?>" class="mmd-lm-icon"><span><?php esc_html_e( 'Important', 'member-mail-drop' ); ?></span></a></li>
							<li><a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>?mmd=allmail"><img src="<?php echo esc_url(MMDHGMemberMailDrop()->mmdhg_icons('allmail')); ?>" class="mmd-lm-icon"><span><?php esc_html_e( 'All Mail', 'member-mail-drop' ); ?></span></a></li>
							<li><a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>?mmd=trash"><img src="<?php echo esc_url(MMDHGMemberMailDrop()->mmdhg_icons('trash')); ?>" class="mmd-lm-icon"><span><?php esc_html_e( 'Trash', 'member-mail-drop' ); ?></span></a></li>
						</ul>
					</div>			
				</div>
				<div class="mmdcols mmdcols8 mob-mmdcols10">
					<?php 
						if(!isset($_GET['mail'])){
							do_action('mmdhg_mail_top_folder');
						} 
					?>
						<?php 
							global $current_user;

							

							$search_query = get_search_query();
							$offset = MMDHGMemberMailDrop()->mmdhg_offset_query( 25 );
							$args = array(
								'post_type'  		=> '_mmd',
								'posts_per_page'	=> $offset['num_per_page'],
								'number'	 		=> $offset['num_per_page'],
								'offset'  			=> $offset['offset'],
								'orderby'    		=> 'date',
								'order'      		=> 'DESC',
								'post_status '		=> 'publish',
								'post_parent' 		=> 0,
								'meta_query'		=> array( 
									'relation'		=> 'AND',
									array (
										'key'			=> 'deleted',
										'value'   		=> '0',
										'compare' 		=> '=',
									)
								),
							);

							if ($current_page->post_parent) {
								$main_front_page = get_page_by_path(parent::$main_page, OBJECT, 'page');
								$parent = get_post($current_page->post_parent);
								if($parent->ID === $main_front_page->ID){
									$args['meta_query'][] = array(
										'relation' => 'AND',
										array(
											'key'     => 'mail_folder',
											'value'   => $current_page->ID,
											'compare' => '=',
										)
									);
								}
							}

							if(isset($_GET['mmd'])){
								if($_GET['mmd'] !== 'allmail' ){
									$args['meta_query'][] = array(
										'relation' => 'AND',
										array(
											'key'     => 'mtype',
											'value'   => sanitize_text_field($_GET['mmd']),
											'compare' => '=',
										)
									);
								}
							} else {
								$args['meta_query'][] = array(
									'relation' => 'AND',
									array(
										'key'     => 'mtype',
										'value'   => 'inbox',
										'compare' => '='
									)
								);
							}

							if ($current_user->ID) { 
								$args['meta_query'][] = array(
									'relation' => 'AND',
									array(
										'key'     => 'recipient',
										'value'   => $current_user->ID,
										'compare' => '='
									)
								);
							}

							
							$search_query = '';
							if(isset($_GET['ms'])){
								$search_query = sanitize_text_field($_GET['ms']);

								// Get users matching the search name
								$users = get_users( array(
									'search'         => '*' . esc_attr( $search_query ) . '*',
									'search_columns' => array( 'user_login', 'user_nicename', 'user_email', 'display_name' )
								) );

								$user_id = '';
								if ( ! empty( $users ) ) {
									foreach ( $users as $user ) {
										$user_id = $user->ID;
									}
								}
								$args['meta_query'][] = array(
									'relation' => 'OR',
									array(
										'key'     => 'recipient',
										'value'   => $user_id,
										'compare' => '='
									),
									array(
										'key'     => 'subject',
										'value'   => $search_query,
										'compare' => 'LIKE'
									)
								);

							}

							do_action('mmdhg_mail_list', $offset, $args, true); 
							wp_reset_postdata();
						?>
				</div>
			</div>
		</div>