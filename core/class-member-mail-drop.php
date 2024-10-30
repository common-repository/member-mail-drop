<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'MMDHGMemberMailDrop' ) ) :

	/**
	 * Main MMDHGMemberMailDrop Class.
	 *
	 * @package		MMDHGMemberMailDrop
	 * @since		1.0.0
	 * @author		Hudson Group
	*/
	class MMDHGMemberMailDrop {

		
		/**
		 * Instance of the class.
		 *
		 * @var object
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Nonce value for security purposes.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		private $mmdhg_nonce = 'mmd-nonce';

		/**
		 * Slug for the main MMD page.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public static $main_page = 'mmd-mail';

		
		/**
		 * user capability
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|MMDHGMemberMailDrop
		 */
		public static $capability = 'unfiltered_html';

				
		/**
		 * Clone method to prevent class cloning.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'member-mail-drop' ), '1.0.0' );
		}

				
		/**
		 * Wakeup method to prevent class unserialization.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'member-mail-drop' ), '1.0.0' );
		}

				
		/**
		 * Get the instance of the MMDHGMemberMailDrop class.
		 *
		 * @return MMDHGMemberMailDrop Instance of the MMDHGMemberMailDrop class.
		 * @since 1.0.0
		 */
		public static function mmdhg_instance() {
			// Return if the instance is already set.
			if ( isset( self::$instance ) && self::$instance instanceof MMDHGMemberMailDrop ) {
				return self::$instance;
			}
		
			// Create a new instance if not already set.
			self::$instance = new MMDHGMemberMailDrop;
		
			// Initialize the plugin.
			self::$instance->mmdhg_initialize();
		
			return self::$instance;
		}

		/**
		 * Initialize the plugin.
		 * 
		 * @since 1.0.0
		 */
		private function mmdhg_initialize() {
			// Perform clean-up tasks.
			$this->mmdhg_clean_up();

			// Include necessary files.
			$this->mmdhg_includes();

			// Load the text domain for translations.
			$this->mmdhg_load_textdomain();

			// Fire the plugin initialization.
			$this->mmdhg_init_plugin_components();

			// Fire a custom action after the successful plugin setup.
			do_action( 'MMDHGMemberMailDrop/plugin_loaded' );
		}

				
		/**
		 * Include necessary files.
		 *
		 * @since 1.0.0
		 */
		public static function mmdhg_includes() {
			$init_class_file = MMDHG_PLUGIN_DIR . 'core/classes/class-initialization.php';
			if ( file_exists( $init_class_file ) ) {
				require_once $init_class_file;
			} else {
				wp_die("Member Mail Drop: Initialization class file is missing.");
			}
		
			$admin_class_file = MMDHG_PLUGIN_DIR . 'core/classes/class-admin.php';
			if ( file_exists( $admin_class_file ) ) {
				require_once $admin_class_file;
			} else {
				wp_die( 'Member Mail Drop: Admin class file is missing.' );
			}
		
			$shortcode_class_file = MMDHG_PLUGIN_DIR . 'core/classes/class-shortcode.php';
			if ( file_exists( $shortcode_class_file ) ) {
				require_once $shortcode_class_file;
			} else {
				wp_die( 'Member Mail Drop: Shortcode class file is missing.' );
			}
		
			$front_class_file = MMDHG_PLUGIN_DIR . 'core/classes/class-front.php';
			if ( file_exists( $front_class_file ) ) {
				require_once $front_class_file;
			} else {
				wp_die( 'Member Mail Drop: Frontend class file is missing.' );
			}
		}

				
		/**
		 * Load the plugin text domain.
		 *
		 * @since 1.0.0
		 */
		public static function mmdhg_load_textdomain() {
			load_plugin_textdomain( 'member-mail-drop-td', FALSE, dirname( plugin_basename( MMDHG_PLUGIN_DIR ) ) . '/languages/' );
		}

				
		/**
		 * Clean up tasks during plugin activation and deactivation.
		 *
		 * @since 1.0.0
		 */
		function mmdhg_clean_up(){
			register_activation_hook(MMDHG_PLUGIN_FILE, array(self::$instance, 'mmdhg_activate_hook'));
			register_deactivation_hook( MMDHG_PLUGIN_FILE, array( self::$instance, 'mmdhg_deactivate_hook' ));
		}


				
		/**
		 * Activation hook for the plugin.
		 *
		 * @since 1.0.0
		 */
		public static function mmdhg_activate_hook() {
			self::mmdhg_create_settings_table();
    		self::mmdhg_create_mmd_mail_page();
		}

		/**
		 * Create the settings table for Member Mail Drop.
		 *
		 * This function is responsible for creating the necessary database table
		 * to store settings related to Member Mail Drop. It checks if the table
		 * already exists and creates it if not.
		 *
		 * @since 1.0.0
		 * @access private
		 * @static
		 */
		private static function mmdhg_create_settings_table() {
			
			global $wpdb;
		
			$settings_table_name = $wpdb->prefix . 'mmd_settings';
		
			if (is_plugin_active(MMDHG_PLUGIN_BASE)) {
				deactivate_plugins(MMDHG_PLUGIN_BASE);
			}
		
			if ($wpdb->get_var("SHOW TABLES LIKE '$settings_table_name'") != $settings_table_name) {
				$charset_collate = $wpdb->get_charset_collate();
		
				$settings_sql = "CREATE TABLE $settings_table_name (
					id INT(11) NOT NULL AUTO_INCREMENT,
					setting_type VARCHAR(255) NOT NULL,
					settings TEXT NOT NULL,
					mmd_deleted INT(11) NOT NULL,
					PRIMARY KEY (id)
				) $charset_collate;";
		
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($settings_sql);
			}
		
		}

		/**
		 * Create the Member Mail Drop Mail View page.
		 *
		 * This function checks if the 'mmd-mail' page exists and creates it if not.
		 * The page includes the [mmd_list] shortcode in its content.
		 *
		 * @since 1.0.0
		 * @access private
		 * @static
		 */
		private static function mmdhg_create_mmd_mail_page() {
			
			$page = get_page_by_path('mmd-mail');
		
			if (!$page) {
				$page_args = array(
					'post_title'   => 'Mail View',
					'post_name'    => 'mmd-mail',
					'post_content' => '[mmd_list]',
					'post_status'  => 'publish',
					'post_type'    => 'page',
				);
		
				wp_insert_post($page_args);
			}
		}
		
				
		/**
		 * Deactivation hook for the plugin.
		 *
		 * @since 1.0.0
		 */
		public static function mmdhg_deactivate_hook(){
			global $wpdb;

			$settings_table_name = $wpdb->prefix . 'mmd_settings';
		    flush_rewrite_rules();
		}

		/**
		 * Initialize various components of the plugin.
		 * 
		 * @since 1.0.0
		 */
		private function mmdhg_init_plugin_components() {
			// Initialize the plugin initialization class.
			new MMDHGMemberMailDropInitialization();

			// Initialize the admin class.
			new MMDHGAdminClass();

			// Initialize the shortcode class.
			new MMDHGShortcode();

			// Initialize the front class.
			new MMDHGFrontClass();
		}

		

				
		/**
		 * Generate and return a nonce value.
		 *
		 * @param string $nonce_type Nonce type.
		 *
		 * @return string Nonce value.
		 * @since 1.0.0
		 */
		public static function mmdhg_nonce($nonce_type) {
			$created_nonce = '';
			if( is_user_logged_in() ){
				$created_nonce = wp_create_nonce( $nonce_type.self::$instance->mmdhg_nonce );
			}
			$created_nonce ='<input type="hidden" id="_nonce" name="_nonce" value="'.$created_nonce.'">';

			return $created_nonce;
		}


				
		/**
		 * Generate and return a nonce value for a link.
		 *
		 * @param string $nonce_type Nonce type.
		 *
		 * @return string Nonce value.
		 * @since 1.0.0
		 */
		public static function mmdhg_link_nonce($nonce_type) {
			$created_nonce = '';
			if( is_user_logged_in() ){
				$created_nonce = wp_create_nonce( $nonce_type.self::$instance->mmdhg_nonce );
			}
			$created_nonce = $created_nonce;

			return $created_nonce;
		}

				
		/**
		 * Verify a nonce value.
		 *
		 * @param string $security    Nonce value to verify.
		 * @param string $v_nonce_type Nonce type.
		 *
		 * @return bool Whether the nonce value is valid.
		 * @since 1.0.0
		 */
		public static function mmdhg_v_nonce($security, $v_nonce_type){
			$rv_nonce = wp_verify_nonce(  $security, $v_nonce_type.self::$instance->mmdhg_nonce );
			return $rv_nonce;
		}
		
		
		/**
		 * Generate an offset query for pagination.
		 *
		 * @param int $no Number of items per page.
		 *
		 * @return array Offset query parameters.
		 * @since 1.0.0
		 */
		public static function mmdhg_offset_query( $no ){
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			if($paged==1){
			  $offset=0;  
			} else {
			   $offset= ($paged-1)*$no;
			}
			$offset_query = array('offset' => $offset, 'num_per_page' => $no, 'paged' => $paged );
			
			return $offset_query;
		}
		
		/**
		 * Display pagination for a given query.
		 *
		 * @param WP_Query $query   WordPress query object.
		 * @param int      $paged   Current page number.
		 * @param int      $perpage Number of items per page.
		 * @param string   $colspan Colspan attribute for table cell.
		 * @since 1.0.0
		 */
		public static function mmdhg_pagination( $query, $paged, $perpage, $colspan = '' ) {
			
			echo '<tr><td colspan="'.$colspan.'" style="text-align:right;">';
						$total_query = $query->found_posts;  
						$total_pages=ceil($total_query/$perpage);
						$big = 999999999;
						  echo paginate_links(array(  
							  'base' => preg_replace('/\?.*/', '/', get_pagenum_link()) . '%_%',
							  'format' => '?paged=%#%',  
							  'current' => $paged,  
							  'total' => $total_pages,  
							  'prev_text' => '<i class="fa fa-angle-left"></i> prev',  
							  'next_text' => 'next <i class="fa fa-angle-right"></i>',
							  'type'     => 'list',
							)); 
			echo '</td></tr>';
		}
		
		
		
		/**
		 * Get the URL of an icon based on mail type and read status.
		 *
		 * @param string $icons  Icon name.
		 * @param string $mtype  Mail type.
		 * @param string $is_read Read status.
		 * @param string $ext    Icon file extension.
		 *
		 * @return string Icon URL.
		 * @since 1.0.0
		 */
		public static function mmdhg_icons($icons, $mtype="", $is_read="", $ext=""){
			if($mtype === $icons || ($icons == 'marked-open' && $is_read == '1')){
				$c = '-c';
			} else {
				$c = '';
			}

			if($ext){
				$ico = $icons.$c.'.'.$ext;
			} else {
				$ico = $icons.$c.'.png';
			}
			return MMDHG_PLUGIN_URL . 'assets/img/'. $ico;
		}
		
		/**
		 * Check if only logged-in users are allowed, redirecting to 404 if not logged in.
		 *
		 * @since 1.0.0
		 */
		public static function mmdhg_only_logged_in_allowed(){
			global $wp_query;
			if ( is_user_logged_in() ) {
				// do nothing
			} else {
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				get_template_part( 404 ); exit();
			}
		}

		public static function mmdhg_add_tinymce_field($content = '', $editor_id = '') {
			$settings = array(
				'media_buttons' => false, // Set to false to remove the "Add Media" button
			);
		
			wp_editor($content, $editor_id, $settings);
		}






	}

endif; // End if class_exists check.


if(!function_exists('MMDHGMemberMailDrop')){
    function MMDHGMemberMailDrop(){
		return new MMDHGMemberMailDrop();
	}
}