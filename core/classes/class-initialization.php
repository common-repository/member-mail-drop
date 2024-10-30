<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'MMDHGMemberMailDropInitialization' ) ) :

	/**
	 * MMDHGMemberMailDropInitialization Class.
	 *
	 * @package		MMDHGMemberMailDrop
	 * @subpackage	Classes/MMDHGMemberMailDropInitialization
	 * @since		1.0.0
	 * @author		Hudson Group
	*/
	class MMDHGMemberMailDropInitialization extends MMDHGMemberMailDrop {

		/**
		 * The initialization instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|MMDHGMemberMailDropInitialization
		 */
		private static $instance;

				
		/**
		 * __construct
		 *
		 * @return void
		 */
		function __construct() {
			self::$instance	= $this;
			self::$instance->init();
		}

				
		/**
		 * Function for initializing actions and filters during plugin initialization.
		 *
		 * @since   1.0.0
		 */
		private function init(){
			add_action( 'admin_menu', array( self::$instance, 'mmdhg_register_admin_menu_items' ) );
			add_action( 'admin_enqueue_scripts', array( self::$instance, 'mmdhg_admin_enqueue_style_scripts' ), 20 );
			add_action( 'wp_enqueue_scripts', array( self::$instance, 'mmdhg_enqueue_style_scripts' ), 20 );
			add_action( 'init', array( self::$instance, 'mmdhg_register_post_type' ) );
			add_filter( 'plugin_action_links', array( self::$instance, 'mmdhg_add_action_plugin'), 10, 5 );
		}


				
		/**
		 * Function for registering admin menu items.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_register_admin_menu_items($admin_bar){
			if (current_user_can( parent::$capability )){
				add_menu_page(
					__( 'Member Mail Drop', 'member-mail-drop' ),
					'Member Mail Drop',
					'manage_options',
					'mmd-admin',
					array( self::$instance, 'mmdhg_options_page_contents' ),
					plugins_url( 'assets/img/mmd-white.png', MMDHG_PLUGIN_BASE ),
					80 
				);

				add_submenu_page( 
					'mmd-admin', 
					__( 'Add New Mail', 'member-mail-drop' ), 
					__( 'Add New Mail', 'member-mail-drop' ), 
					'manage_options', 
					'mmd-new-mail',
					array( self::$instance, 'mmdhg_add_new_mail_contents' ),
				);

				add_submenu_page( 
					'mmd-admin', 
					__( 'Add New Folder', 'member-mail-drop' ), 
					__( 'Add New Folder', 'member-mail-drop' ), 
					'manage_options', 
					'mmd-new-folder',
					array( self::$instance, 'mmdhg_add_new_folder' ),
				);
			}
		}



				
		/**
		 * Function for displaying contents on the Member Mail Drop options page.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_options_page_contents(){
			if ( is_file( include_once MMDHG_PLUGIN_DIR . 'core/includes/mmd-admin.php' ) ) {
	            include_once MMDHG_PLUGIN_DIR . 'core/includes/mmd-admin.php';
	        }
		}

				
		/**
		 * Function for displaying contents on the page for adding new mail.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_add_new_mail_contents(){
			if ( is_file( include_once MMDHG_PLUGIN_DIR . 'core/includes/mmd-add-new-mail.php' ) ) {
	            include_once MMDHG_PLUGIN_DIR . 'core/includes/mmd-add-new-mail.php';
	        }
		}
				
		/**
		 * Function for displaying contents on the page for adding new folder.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_add_new_folder(){
			if ( is_file( include_once MMDHG_PLUGIN_DIR . 'core/includes/mmd-add-new-folder.php' ) ) {
	            include_once MMDHG_PLUGIN_DIR . 'core/includes/mmd-add-new-folder.php';
	        }
		}

				
		/**
		 * Function for enqueueing styles and scripts in the admin area.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_admin_enqueue_style_scripts(){
			$cScreen = get_current_screen();
			if ($cScreen->id == "toplevel_page_mmd-admin" || $cScreen->id == "member-mail-drop_page_mmd-new-mail" || $cScreen->id == "member-mail-drop_page_mmd-new-folder") {
			wp_enqueue_style( 'mmd-select-styles', MMDHG_PLUGIN_URL . 'assets/css/select.min.css', array(), MMDHG_VERSION, 'all' );
			wp_enqueue_script( 'mmd-jquery-dropdown', MMDHG_PLUGIN_URL . 'assets/js/select.min.js', array('jquery'), MMDHG_VERSION, false );
			wp_enqueue_script( 'mmd', MMDHG_PLUGIN_URL . 'assets/js/mmd.js', array('jquery'), MMDHG_VERSION, false );
			wp_enqueue_script( 'mmd', MMDHG_PLUGIN_URL . 'assets/js/mmd.js', array('jquery'), MMDHG_VERSION, false );
			wp_enqueue_script( 'mmd-admin-scripts', MMDHG_PLUGIN_URL . 'assets/js/mmd-scripts.js', array('jquery'), MMDHG_VERSION, false );
				wp_localize_script( 'mmd-admin-scripts', 'mmdscripts', array(
					'plugin_name'   	=> esc_html( 'Member Mail Drop', 'member-mail-drop' ),
			        'ajaxurl' => admin_url( 'admin-ajax.php' ),
			        'plugin_url' => MMDHG_PLUGIN_URL,
				));

				if($cScreen->id == "member-mail-drop_page_mmd-new-mail"){
					wp_enqueue_editor();
				}
			}
			
			wp_enqueue_style( 'mmd-styles', MMDHG_PLUGIN_URL . 'assets/css/mmd-styles.css', array(), MMDHG_VERSION, 'all' );
		}

				
		/**
		 * Enqueues styles and scripts for the MMD plugin.
		 *
		 * @since 1.0.0
		 */
		public static function mmdhg_enqueue_style_scripts(){
			wp_enqueue_script( 'mmd', MMDHG_PLUGIN_URL . 'assets/js/mmd.js', array('jquery'), MMDHG_VERSION, false );
			wp_enqueue_script( 'mmd-admin-scripts', MMDHG_PLUGIN_URL . 'assets/js/mmd-scripts.js', array('jquery'), MMDHG_VERSION, false );
			wp_enqueue_script( 'mmd-front-scripts', MMDHG_PLUGIN_URL . 'assets/js/mmd-front-scripts.js', array('jquery'), MMDHG_VERSION, false );
			wp_localize_script( 'mmd-front-scripts', 'mmdscripts', array(
				'plugin_name'   	=> esc_html( 'Member Mail Drop', 'member-mail-drop' ),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'plugin_url' => MMDHG_PLUGIN_URL,
			));
			
			wp_enqueue_editor();
			
			wp_enqueue_style( 'mmd-styles', MMDHG_PLUGIN_URL . 'assets/css/mmd-styles.css', array(), MMDHG_VERSION, 'all' );
		}
		

				
		/**
		 * Registers a custom post type.
		 *
		 * @since 1.0.0
		 */
		public function mmdhg_register_post_type(){
			$labels = array(
		        'name'                  => _x( 'MMDHGMemberMailDrop', 'MMDHGMemberMailDrop', 'member-mail-drop' ),
		        'singular_name'         => _x( 'MMDHGMemberMailDrop', 'MMDHGMemberMailDrop', 'member-mail-drop' ),
		        'menu_name'             => _x( 'MMDHGMemberMailDrop', 'MMDHGMemberMailDrop', 'member-mail-drop' ),
		    );
		 
		    $args = array(
		        'labels'             => $labels,
		        'public'             => false,
		        'publicly_queryable' => false,
		        'show_ui'            => false,
		        'show_in_menu'       => false,
		        'query_var'          => true,
		        'rewrite'            => array( 'slug' => 'mmd' ),
		        'capability_type'    => 'post',
		        'has_archive'        => false,
		        'hierarchical'       => true,
		        'menu_position'      => null,
		        'supports'           => array( 'title', 'editor', 'page-attributes' ),
		    );
		 
		    register_post_type( '_mmd', $args );
		}
		 
				
		/**
		 * Adds custom actions to the plugin in the plugin list.
		 *
		 * @since 1.0.0
		 */
		public static function mmdhg_add_action_plugin( $actions, $plugin_file ){
			static $plugin;
		    if (!isset($plugin))
		    if (MMDHG_PLUGIN_BASE == $plugin_file) {
		    	$settings = array('settings' => '<a href="admin.php?page=mmd-admin">' . __('Get Started', 'General') . '</a>');
		    	$actions = array_merge($settings, $actions);
		    }
		     
		   return $actions;
		}

		 


	}



endif; // End if class_exists check.