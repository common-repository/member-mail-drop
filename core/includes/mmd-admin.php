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
			<h2><img src="<?php echo esc_url(MMDHG_PLUGIN_URL . 'assets/img/mmd_100.png'); ?>"><span><?php esc_html_e('Member Mail Drop'); ?></span></h2>
		</div>
		<div class="mmd-meta-box-body">
			<div class="mmdrow">
				<div class="mmdcols">
					<div class="mmd-main-stage">
						<?php 
						
							$offset = MMDHGMemberMailDrop()->mmdhg_offset_query( 25 );
							$args = array(
								'post_type'  		=> '_mmd',
								'posts_per_page'	=> $offset['num_per_page'],
								'number'	 		=> $offset['num_per_page'],
								'offset'  			=> $offset['offset'],
								'orderby'    		=> 'date',
								'order'      		=> 'DESC',
								'post_status '		=> 'publish',
								'meta_query'		=> array( 
									'relation'		=> 'AND',
									array (
										'key'			=> 'deleted',
										'value'   		=> '0',
										'compare' 		=> '=',
									)
								),
							);

							$search_query = '';
							if(isset($_GET['ms'])){
								$search_query = sanitize_text_field($_GET['ms']);

								if($search_query === 'admin'){
									$user_id = 'admin';
								} else {

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
							do_action('mmdhg_mail_list', $offset, $args); 
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="mmd-meta-box-footer">
			<input type="hidden" name="mmd-gen-nonce" id="mmd-gen-nonce" value=""/>
		</div>
	</div>
</div>