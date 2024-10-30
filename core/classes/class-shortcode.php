<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'MMDHGShortcode' ) ) :

	/**
	 * MMDHGShortcode Class.
	 *
	 * @package		MEMBERSMAILDROP
	 * @subpackage	Classes/MMDHGShortcode
	 * @since		1.0.0
	 * @author		Hudson Group
	*/
	class MMDHGShortcode extends MMDHGMemberMailDrop {

		/**
		 * The initialization instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|MMDHGShortcode
		 */
		private static $instance;

				
		/**
		 * Constructor for the class.
		 *
		 * @since 1.0.0
		 */
		function __construct() {
			self::$instance	= $this;
			self::$instance->mmdhg_shortcode_main();
		}

				
		/**
		 * Registers shortcodes for the main functionality.
		 *
		 * @since 1.0.0
		 */
		private function mmdhg_shortcode_main(){
			add_shortcode( 'mmd_list', array( self::$instance, 'mmdhg_member_mail_drop_sc_list' ) );
			add_shortcode( 'mmd_folder', array( self::$instance, 'mmdhg_member_mail_drop_folder' ) );
		}
		
		/**
		 * Shortcode callback function for displaying a list.
		 *
		 * @since 1.0.0
		 */
		public function mmdhg_member_mail_drop_sc_list() {
			ob_start();
			if ( is_file( include_once MMDHG_PLUGIN_DIR . 'core/includes/mmd-sc-list.php' ) ) {
	            include_once HFS_PLUGIN_DIR . 'core/includes/mmd-sc-list.php';
	        }
			return ob_get_clean();
		}
		
		/**
		 * Shortcode callback function for displaying a folder.
		 *
		 * @since 1.0.0
		 */
		public function mmdhg_member_mail_drop_folder() {

			// Use a safer output buffering approach
			$output = '';
			ob_start();

			$folder_shortcode = esc_html__('folder shortcode', 'member-mail-drop'); 
			echo $folder_shortcode;
		
			$output = ob_get_contents();
			ob_end_clean();
		
			return $output;
		}

		/**
		 * Retrieves the URL for the image thumbnail based on file extension.
		 *
		 * @since 1.0.0
		 *
		 * @param string $filename The name of the file.
		 * @return string The URL of the image thumbnail.
		 */
		public static function mmdhg_get_file_extension_img($filename){
			$extension = pathinfo($filename, PATHINFO_EXTENSION);
			$img_base = MMDHG_PLUGIN_URL . 'assets/img/';
			switch ($extension) {
				case 'pdf':
					$img_thumb = "prev_pdf.png";
					break;
				case 'xlsx':
				case 'csv':
					$img_thumb = "prev_xls.png";
					break;
				case 'docx':
					$img_thumb = "prev_doc.png";
					break;
				case 'zip':
				case 'rar':
					$img_thumb = "prev_zip.png";
					break;
				default:
					$img_thumb = "prev_default.png";
			}

			return $img_base.$img_thumb;
			
		}


	}



endif; // End if class_exists check.