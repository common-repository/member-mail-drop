<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'MMDHGFrontClass' ) ) :


	/**
	 * MMDHGFrontClass Class.
	 *
	 * @package		MEMBERSMAILDROP
	 * @subpackage	Classes/MMDHGFrontClass
	 * @since		1.0.0
	 * @author		Hudson Group
	*/
	class MMDHGFrontClass extends MMDHGMemberMailDrop {
        private static $instance;

		/**
		 * Construct initialization
		 * 
		 * @since	1.0.0
		 * @since	1.0.0
		 */
		function __construct() {
			// trigger MMDHGAdminClass main function
			self::$instance	= $this;
			self::$instance->main();
		}

        
				
		/**
		 * Main function for initializing actions and filters related to Mail List functionality.
		 *
		 * @since   1.0.0
		 */
		private function main(){

            
			// Mail List
			add_action( 'mmdhg_mail_list', array( self::$instance, 'mmdhg_mail_list' ), 10, 3 );

			// Action Buttons callback
			add_action( 'wp_ajax_mdd_actions_trigger', array( self::$instance, 'mmdhg_actions_trigger_ajax_callback' ) );

			// Action Buttons callback
			add_action( 'wp_ajax_mmd_zip_attachements', array( self::$instance, 'mmdhg_zip_attachements_ajax_callback' ) );

			// Action Buttons callback
			add_action( 'mmdhg_after_mail_view', array( self::$instance, 'mmdhg_after_mail_view_function' ), 10, 1 );

			// Action Buttons callback
			add_action( 'mmdhg_after_mail_view', array( self::$instance, 'mmdhg_after_mail_view_forward_function' ), 20, 1 );

			// Reply Notice Filter
			add_filter( 'mmdhg_add_reply_notice', array( self::$instance, 'mmdhg_add_reply_notice_function' ) );

			// Reply Notice Filter
			add_filter( 'mmdhg_forward_heading', array( self::$instance, 'mmdhg_add_reply_notice_function' ) );

			// Action Buttons callback
			add_action( 'wp_ajax_mmd_forward_mail', array( self::$instance, 'mmdhg_forward_mail_ajax_callback' ) );

			// Mail List
			add_action( 'mmdhg_mail_top_folder', array( self::$instance, 'mmdhg_mail_top_folder' ), 10 );
		}
		
        
		/**
		 * Mail List function for displaying mail items or individual mail view.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_mail_list( $offset, $args, $is_front = "" ){
			// The Query
            if(!isset($_GET['mail'])){	
                $the_query = new WP_Query($args);
			    include MMDHG_PLUGIN_DIR . 'core/includes/mmd-mail-list.php';	
            } else {
                $id = sanitize_text_field($_GET['mail']);
				update_post_meta( $id, 'is_read', '1' );
                include MMDHG_PLUGIN_DIR . 'core/includes/mmd-mail-view.php';	
            }
		}

        

				
		/**
		 * AJAX callback function for handling mail actions triggered by buttons.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_actions_trigger_ajax_callback(){

			$vnonce = parent::mmdhg_v_nonce(sanitize_text_field($_REQUEST['_nonce']), 'action-nonce');
			if ( $vnonce == false ) exit;

			$data_unique = absint($_POST['data_unique']); // Validate as integer
			$data_action = sanitize_key($_POST['data_action']);

			if($data_action === 'starred' || $data_action === 'important' || $data_action === 'trash'){
				$dataaction = $data_action;
				$dataaction_rev = 'inbox';
				$metakey = 'mtype';
				echo self::$instance->mmdhg_change_meta_action($data_unique, $metakey, $dataaction, $dataaction_rev);
			} else if($data_action === 'is_read'){
				$dataaction = '1';
				$dataaction_rev = '0';
				$metakey = $data_action;
				echo self::$instance->mmdhg_change_meta_action($data_unique, $metakey, $dataaction, $dataaction_rev);
			} else if($data_action === 'change_status'){
				// folder change status
				$dataaction = '1';
				$dataaction_rev = '0';
				$metakey = 'mmd_folder_status';
				echo self::$instance->mmdhg_change_meta_action($data_unique, $metakey, $dataaction, $dataaction_rev);
			} else if($data_action === 'trash_folder'){
				// folder change status
				echo self::$instance->mmdhg_trash_folder_action($data_unique);
			} else if($data_action === 'edit_folder'){
				// folder change status
				echo self::$instance->mmdhg_edit_folder_action($data_unique);
			}
			

			wp_die();

		}

		/**
		 * Function for changing meta action based on specified parameters.
		 *
		 * @since   1.0.0
		 */
		function mmdhg_change_meta_action($data_unique, $metakey, $dataaction, $dataaction_rev){
			try {
				$curr_metaval = get_post_meta( $data_unique, $metakey, true );
				if($curr_metaval !== $dataaction){
					update_post_meta( $data_unique, $metakey, $dataaction );
					return wp_json_encode(array( 'success' => true, 'message' => 'Action completed.', 'data_action' => $metakey, 'is_reload' => true));
				} else {
					update_post_meta( $data_unique, $metakey, $dataaction_rev );
					return wp_json_encode(array( 'success' => true, 'message' => 'Action completed.', 'data_action' => $metakey, 'is_reload' => true));
				}
			} catch (\Exception $ex) {
				return $ex->getMessage();
			}
		}

		/**
		 * Function for handling the trash action on a folder.
		 *
		 * @since   1.0.0
		 */
		function mmdhg_trash_folder_action($id){
			if (get_post_status($id) === 'trash') {
				return wp_json_encode(array( 'success' => true, 'message' => 'Folder is already deleted.', 'data_action' => 'trash_folder', 'is_reload' => true));
			} else {
				// Move the page to the trash.
				if (wp_trash_post($id)) {
					return wp_json_encode(array( 'success' => true, 'message' => 'Success', 'message' => 'Action completed.','data_action' => 'trash_folder', 'is_reload' => true));
				} else {
					return wp_json_encode(array( 'success' => false, 'message' => 'Error deleting the folder', 'data_action' => 'trash_folder', 'is_reload' => true));
				}
			}
		}

		/**
		 * Function for handling the edit action on a folder.
		 *
		 * @since   1.0.0
		 */
		function mmdhg_edit_folder_action($id){
			$page_title = get_the_title($id);
			$page = get_post($id);
			$page_slug = $page->post_name;
			return wp_json_encode(array( 'success' => true, 'data_action' => 'edit_folder', 'message' => 'Success', 'title' => $page_title, 'slug' => $page_slug, 'id' => $id, 'is_reload' => false));
		}

		/**
		 * Generates the URL for accessing a specific mail in the mail system.
		 *
		 * @since   1.0.0
		 */
        public static function mmdhg_mail_url($id) {
			global $wp;
		
			// Sanitize the parent page name using sanitize_title
			$parent_post = get_page_by_path(parent::$main_page, OBJECT, 'page');
		
			// Use site_url() for safe base URL construction
			$baseurl = site_url('/') . sanitize_title($parent_post->post_name) . '/';
		
			// Escape the ID for safe URL inclusion
			$escaped_id = esc_attr($id);
		
			$link = '';
			if (str_contains(sanitize_url($_SERVER['REQUEST_URI']), '?mmd')) {
				$mmd_query = isset($_GET['mmd']) ? sanitize_text_field($_GET['mmd']) : 'inbox';
				$link .= '?mmd=' . $mmd_query . '&mail=' . $escaped_id;
			} else {
				$link .= '?mail=' . $escaped_id;
			}
		
			return $baseurl . $link;
		}

		/**
		 * AJAX callback function for creating a zip file containing mail attachments.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_zip_attachements_ajax_callback(){
			
			$vnonce = parent::mmdhg_v_nonce(sanitize_text_field($_REQUEST['_nonce']), 'download-nonce');
    		if ( $vnonce == false ) exit;

			
			$id = absint($_POST['mail_id']);
			$meta = MMDHGAdminClass::mmdhg_meta_query($id);
			
			$attachments = array_map('esc_url', $meta['mail_attchment']);

			$zip = new ZipArchive();
			$zip_ame = date('Ymd') . '.zip'; 

			$wp_upload_dir = wp_upload_dir();
			$zip_path = $wp_upload_dir['basedir'] . '/' . $zip_ame;

			if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
				if (is_array($attachments)){
					foreach ($attachments as $url) {
						if($url){
							$file_path = str_replace($wp_upload_dir['baseurl'], $wp_upload_dir['basedir'], $url);
							$file_name = sanitize_file_name(basename($url));
							$file_content = file_get_contents($file_path);
							$zip->addFromString($file_name, $file_content);
						}
					}
				}

				$zip->close();
				$zip_moved = wp_upload_bits($zip_ame, null, file_get_contents($zip_path));

				if ($zip_moved['error'] === false) {
					$zip_url = $zip_moved['url'];
				
					wp_send_json(array('status' => 'success', 'message' => 'Downloading...', 'data' => $zip_url));
				} else {
					
					$response = 
					wp_send_json(array('status' => 'error', 'message' => 'Failed to upload zip file!', 'data' => $zip_url));
				}

				unlink($zip_path);
			} else {
				wp_send_json(array('status' => 'error', 'message' => 'Failed to create zip file!'));
			}
			
			wp_die();

		}

		/**
		 * Function for displaying additional content after viewing a mail.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_after_mail_view_function($id){
            $meta = MMDHGAdminClass::mmdhg_meta_query($id);
			if ( is_file( include_once MMDHG_PLUGIN_DIR . 'core/includes/mmd-reply-form.php' ) ) {
	            include_once HFS_PLUGIN_DIR . 'core/includes/mmd-reply-form.php';
	        }
		}

		/**
		 * Function for displaying additional content after viewing a mail for forwarding.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_after_mail_view_forward_function($id){
            $meta = MMDHGAdminClass::mmdhg_meta_query($id);
			if ( is_file( include_once MMDHG_PLUGIN_DIR . 'core/includes/mmd-forward.php' ) ) {
	            include_once HFS_PLUGIN_DIR . 'core/includes/mmd-forward.php';
	        }
		}

		/**
		 * Filter function for adding reply notice.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_add_reply_notice_function($notice){
			return $notice;
		}

		/**
		 * AJAX callback function for forwarding mail.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_forward_mail_ajax_callback(){

			$vnonce = parent::mmdhg_v_nonce(sanitize_text_field($_REQUEST['_nonce']), 'mmd-forward-mail-form');
			if ( $vnonce == false ) exit;

			$id = absint($_POST['mmd_id']); 
			$mail_recipient = sanitize_email($_POST['mmd_forward_email']);

			$meta = MMDHGAdminClass::mmdhg_meta_query($id);

			$site_title = esc_html(get_bloginfo('name'));
			$subject = esc_html($meta['subject']);
			$subject .= " - Forwarded email from " . $site_title;
			
			$mail_body = wp_kses_post(get_post($id)->post_content);
    		$mail_body .= "<br/><br/> This email is forwarded from Member Mail Drop";
			
			// Add attachments (if any)
			$attachment = $meta['mail_attchment'];

			// Set additional headers for HTML content
			$headers = array('Content-Type: text/html; charset=UTF-8');

			try {
				$mail = self::$instance->mmdhg_send_forwarded_mail($mail_recipient, $subject, $mail_body, $headers, $attachment);
				if($mail){
					echo wp_json_encode(array("success" => true, 'message' => 'Email sent successfully!'));
				} else {
					echo wp_json_encode(array("success" => false, 'message' => 'Error occured!'));
				}
			} catch (\Exception $ex) {
				echo wp_json_encode(array("success" => false, 'message' => $ex->getMessage()));
			}

			wp_die();

		}

		/**
		 * Function for sending a forwarded email.
		 *
		 * @since   1.0.0
		 */
		function mmdhg_send_forwarded_mail($mail_recipient, $subject, $mail_body, $headers, $attachment = ''){
			
			if($attachment !== ''){
				$attachments = self::$instance->mmdhg_process_attachments($attachment);
				return wp_mail($mail_recipient, $subject, $mail_body, $headers, $attachments);
			} else {
				return wp_mail($mail_recipient, $subject, $mail_body, $headers);
			}
		}

		/**
		 * Function for processing attachment URLs and returning an array of file paths.
		 *
		 * @since   1.0.0
		 */
		function mmdhg_process_attachments($attachment_urls) {
			$attachments = array();
		
			foreach ($attachment_urls as $url) {
				$attachment_path = self::$instance->mmdhg_get_attachment_path($url); // Define a function to get the attachment path from URL
				if ($attachment_path) {
					$attachments[] = $attachment_path;
				}
			}
		
			return $attachments;
		}
		
		/**
		 * Function for getting the attachment path from the attachment URL.
		 *
		 * @since   1.0.0
		 */
		function mmdhg_get_attachment_path($attachment_url) {
			// Get the upload directory info
			$upload_dir = wp_upload_dir();
		
			// Extract the path from the URL
			$attachment_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $attachment_url);
		
			// Check if the file exists at the calculated path
			if (file_exists($attachment_path)) {
				return $attachment_path;
			} else {
				return false; // Return false if the file doesn't exist
			}
		}

		/**
		 * Function for displaying the top mail folders.
		 *
		 * @since   1.0.0
		 */
		public static function mmdhg_mail_top_folder(){
			$main_page = get_page_by_path(parent::$main_page, OBJECT, 'page');
			$child_pages_args = MMDHGAdminClass::mmdhg_folder_query(false);
			$baseurl = site_url('/') . sanitize_title($main_page->post_name) . '/';
			if ($child_pages_args) {
                

				$child_pages_query = new WP_Query($child_pages_args);

				if ($child_pages_query->have_posts()) {
					
					echo '<ul class="mmd-top-folder">';
					echo '<li><a href="' . esc_url($baseurl) . '">Primary</a></li>';
					while ($child_pages_query->have_posts()) {
						$child_pages_query->the_post();
						$id = get_the_ID();
						$title = get_the_title();
						$permalink = get_permalink();
						echo '<li><a href="' . esc_url($permalink) . '">' .$title. '</a></li>';
						
					}
					echo '</ul>';
					wp_reset_postdata();
				}
			}
		}

		public static function mmdhg_get_logged_in_user_email() {
			$current_user = wp_get_current_user();
			if ( $current_user->ID != 0 ) {
				$user_email = $current_user->user_email;
				return $user_email;
			} else {
				return;
			}
		}

    }




endif; // End if class_exists check.}