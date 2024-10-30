<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'MMDHGAdminClass' ) ) :


	/**
	 * MMDHGAdminClass Class.
	 *
	 * @package		MEMBERSMAILDROP
	 * @subpackage	Classes/MMDHGAdminClass
	 * @since		1.0.0
	 * @author		Hudson Group
	*/
	class MMDHGAdminClass extends MMDHGMemberMailDrop {

		/**
		 * Instance of the class.
		 *
		 * @var object
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Slug for the MMD front end.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		private static $mmdhg_front_slug = 'mmd-mail';

		/**
		 * Construct initialization
		 * 
		 * @since	1.0.0
		 * @since	1.0.0
		 */
		function __construct() {
			// Set the instance of the class
			self::$instance = $this;
    
			// Call the main function of MMDHGAdminClass
			self::$instance->main();
		}



				
		/**
		 * Main function for setting up hooks and actions.
		 *
		 * @since 1.0.0
		 */
		private function main(){
			// hook for list of added scripts for header in hfs
			// add_action( 'mmdhg_new_mail_form', array( $this, 'mmdhg_new_mail_form_callback' ) );
			
			// Ajax Image Upload on Dashboard
			add_action( 'wp_ajax_file_upload', array( self::$instance, 'mmdhg_file_upload_callback') );
			// Ajax Image Delete on Dashboard
			add_action( 'wp_ajax_mp_delete_img_upload', array( self::$instance, 'mmdhg_delete_img_upload_callback') );

			// Submit Mail Ajax Callback
			add_action( 'wp_ajax_mmd_submit_mail', array( self::$instance, 'mmdhg_submit_mail_ajax_callback' ) );

			// Add New Mail Title Filter
			add_filter( 'mmdhg_add_new_mail_title', array( self::$instance, 'mmdhg_add_new_mail_title_function' ) );

			// Add New Mail Title Filter
			add_filter( 'mmdhg_add_new_mail_title_form', array( self::$instance, 'mmdhg_add_new_mail_title_function' ) );

			// Submit Mail Ajax Callback
			add_action( 'wp_ajax_mmd_add_new_folder_mail', array( self::$instance, 'mmdhg_add_new_folder_mail_ajax_callback' ) );
		}

		
				
		/**
		 * Generate a form notice.
		 *
		 * @param string $text_notice Custom text for the notice. Default is an empty string.
		 *
		 * @return string HTML for the generated form notice.
		 * @since 1.0.0
		 */
		public static function mmdhg_form_notice($text_notice = ''){
			// Default notice text
			$default_notice = ' action.';

			// If a custom notice text is provided, update the default notice
			if ($text_notice !== '')
			$default_notice = $text_notice;

			// Generate the HTML notice
			$notice = '<div class="mmd-submit-notice dn" id="mmd-submit-notice">';
			$notice .= '<span>scriptrun</span>'; // Placeholder text, you may want to replace it
			$notice .= $default_notice;
			$notice .='</div>';

			// Return the generated HTML notice
			return $notice;
		}

		
					
		/**
		 * mmdhg_file_upload_callback
		 *
		 * @since	1.0.0
		 * @return void
		 */
		public static function mmdhg_file_upload_callback() {

			require_once(MMDHG_PLUGIN_DIR . 'core/resources/tcpdf/tcpdf.php');

			$allowed_types = self::$instance->mmdhg_allow_file_type();
			for($i = 0; $i < count($_FILES['file']['name']); $i++) {
				$file_type = sanitize_mime_type($_FILES['file']['type'][$i]);
				$file_name = sanitize_file_name($_FILES['file']['name'][$i]);
				// Check if the file type is allowed
				if (in_array($file_type, $allowed_types)) {
					$upload_return = array();
					$upload = wp_upload_bits(
						$file_name, 
						null, 
						file_get_contents($_FILES['file']['tmp_name'][$i]));
					
					if ($upload['error']) {
						echo wp_json_encode(array("success" => false, 'message' => $upload['error']));
					} else {
						if (self::$instance->mmdhg_image_type($file_type)) {
							$image_path = $upload['file'];

							// Create new TCPDF object
							$pdf = new TCPDF();

							// Set PDF properties
							$pdf->SetCreator(MMDHG_NAME);
							$pdf->SetAuthor(MMDHG_AUTHOR);
							$pdf->SetTitle(MMDHG_T_DOMAIN);
							$pdf->SetSubject(MMDHG_T_DOMAIN);
							$pdf->SetKeywords('image, pdf, conversion');

							list($image_width, $image_height) = getimagesize($image_path);

							// Add a new page
							$pdf->AddPage();

							// Get the dimensions of the PDF page
							$page_width = $pdf->getPageWidth();
							$page_height = $pdf->getPageHeight();

							$padding = 20; // Adjust the padding value as per your requirement

							// Calculate the available width and height for the image considering the padding
							$available_width = $page_width - 2 * $padding;
							$available_height = $page_height - 2 * $padding;

							// Check if the image needs to be scaled down to fit within the available space
							if ($image_width > $available_width || $image_height > $available_height) {
								// Calculate the aspect ratio of the image
								$aspect_ratio = $image_width / $image_height;

								// Calculate the adjusted dimensions to fit within the available space while maintaining the aspect ratio
								if ($image_width > $image_height) {
									$adjusted_width = $available_width;
									$adjusted_height = $adjusted_width / $aspect_ratio;
								} else {
									$adjusted_height = $available_height;
									$adjusted_width = $adjusted_height * $aspect_ratio;
								}

								$x = $padding + ($available_width - $adjusted_width) / 2;
								$y = $padding + ($available_height - $adjusted_height) / 2;
							} else {
								// The image is smaller than the available space, so use its original dimensions
								$adjusted_width = $image_width;
								$adjusted_height = $image_height;
								$x = $padding + ($available_width - $adjusted_width) / 2;
								$y = $padding + ($available_height - $adjusted_height) / 2;
							}

							// Add the image to the PDF with the adjusted dimensions and position
							$pdf->Image($image_path, $x, $y, $adjusted_width, $adjusted_height, '', '', '', false, 300, '', false, false, 0);

							// Generate a unique filename for the PDF
							$pdf_filename = $file_name . '.pdf';

							// Get the WordPress uploads directory path
							$upload_dir = wp_upload_dir();
							$upload_path = $upload_dir['path'];

							// Full path of the PDF file
							$pdf_path = $upload_path . '/' . $pdf_filename;

							// Output the PDF to the file
							$pdf->Output($pdf_path, 'F');

							// Upload the PDF file to the WordPress uploads folder
							$uploaded = wp_upload_bits($pdf_filename, null, file_get_contents($pdf_path));

							// Check if the upload was successful
							if ($uploaded['error']) {
								echo wp_json_encode(array("success" => false, 'message' => $error_message));
								continue; // Skip to the next iteration
							}

							
							$upload_return[0] = $uploaded['url'];
							$split = explode("/", $uploaded['file']);
						} else {
							$upload_return[0] = $upload['url'];
							$split = explode("/", $upload['file']);
						}
					}

					$upload_return[1] = $split[count($split) - 3];
					$upload_return[2] = $split[count($split) - 2];
					$upload_return[3] = $split[count($split) - 1];

					echo wp_json_encode(array("success" => true, 'message' => $upload_return));
				} else {
					echo wp_json_encode(array("success" => false, 'message' => 'Invalid file type.'));
				}
				
			}
			wp_die();
		}

		/**
		 * Define allowed file types for file uploads in the MMD (Member Mail Drop) plugin.
		 *
		 * @return array Array of allowed file types.
		 */
		private static function mmdhg_allow_file_type(){
			$ext = array(
				'image/png', 
				'image/jpeg', 
				'image/jpg', 
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
				'application/x-zip-compressed', 
				'application/octet-stream', 
				'text/csv', 
				'text/plain', 
				'application/pdf', 
				'application/doc', 
				'application/docx', 
				'application/csv'
			);
			return $ext;
		}

		/**
		 * Check if the given image type is allowed in the MMD (Member Mail Drop) plugin.
		 *
		 * @param string $imgtype The image type to check.
		 *
		 * @return bool True if the image type is allowed, false otherwise.
		 */
		private function mmdhg_image_type($imgtype){
			$ext = array(
				'image/png', 
				'image/jpeg', 
				'image/jpg'
			);

			if(in_array($imgtype, $ext)){
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Callback function to delete an uploaded image.
		 *
		 * @since	1.0.0
		 * @return void
		 */
		public static function mmdhg_delete_img_upload_callback(){

			// Validate and sanitize path ID
			if (!isset($_POST['path_id']) || !is_string($_POST['path_id'])) {
				echo wp_json_encode(array("success" => false, 'message' => 'Missing or invalid path ID')); // Missing or invalid path ID
				wp_die();
			}

			// $path_id = sanitize_file_name($_POST['path_id']); // Sanitize for path traversal prevention
			
			$path_components = array_map('sanitize_file_name', explode('/', $_POST['path_id']));
			// Ensure path components are valid
			if (empty($path_components) || in_array('.', $path_components) || in_array('..', $path_components)) {
				echo wp_json_encode(array("success" => false, 'message' => 'Invalid path'));
				wp_die();
			}

			// Construct sanitized path
			$path_id = implode('/', $path_components);

			// Get the upload directory path
			$img_upload_dir = wp_upload_dir();
			$img_dir = $img_upload_dir['basedir'].'/'.$path_id;

			// Ensure path is within the uploads directory
			if (strpos($img_dir, $img_upload_dir['basedir']) !== 0) {
				echo wp_json_encode(array("success" => false, 'message' => 'Invalid path')); // Invalid path
				wp_die();
			}

			// Check if the image directory exists
			if ( file_exists($img_dir) ) {
				// Attempt to delete the image file
				$is_unlink = unlink($img_dir);
				// Check if the deletion was successful
				if ( $is_unlink === true ) {
					echo wp_json_encode(array("success" => true, 'message' => 'Successful deletion.')); // Successful deletion
				} else {
					echo wp_json_encode(array("success" => false, 'message' => 'Error occurred during deletion.')); // Deletion failed
				}
			} else {
				echo wp_json_encode(array("success" => false, 'message' => 'Image does not exist.')); // Image directory does not exist
			}

			wp_die(); 

		}
		
		/**
		 * Get user options for a dropdown.
		 *
		 * @since 1.0.0
		 */
		public function mmdhg_get_user_option(){
			$users = get_users();
				$mmd_sel_options = '';
				$curr_arr = 0;
				$option_selected_user = array();
				foreach ($users as $user) {
					$user_fname = $user->first_name;
					$user_lname = $user->last_name;
					$user_dname = $user->display_name;
					$user_id = $user->ID;
					if ($user_fname !== "" || $user_lname !== "" ) {
						$user_name = $user_fname . ' ' .$user_lname;
					} else {
						$user_name = $user_dname;
					}

					$mmd_sel_options .= '<option value="'.$user_id.'">'.$user_name.'</option>' ;
					
					$curr_arr++;
				}
			return $mmd_sel_options;
		}
		
		/**
		 * AJAX callback to submit a mail 
		 *
		 * Handles the AJAX request to submit a new mail, including recipient, subject, body, attachments, and other details.
		 * Inserts the mail as a post, notifies the recipient, and returns success or failure JSON response.
		 *
		 * @return void Outputs JSON response indicating success or failure, along with optional redirect URL.
		 * @since 1.0.0
		 */
		public static function mmdhg_submit_mail_ajax_callback(){

			$vnonce = parent::mmdhg_v_nonce(sanitize_text_field($_REQUEST['_nonce']), 'mmd-add-mail-form');
			if ( $vnonce == false ) exit;

			// Validate and sanitize inputs
			$recipient = sanitize_text_field($_POST['recipient']);
			$mail_subject = sanitize_text_field($_POST['mail_subject']);
			$mail_body = wp_kses_post($_POST['mail_body']); // Sanitize HTML content
			$attachment_urls = array_map('esc_url_raw', explode(',', $_POST['mail_attchment'])); // Sanitize attachment URLs
			$is_publish = sanitize_text_field($_POST['is_publish']); // Validate publish option
			$mail_id = absint($_POST['mail_id']); // Validate as integer
            $allow_reply = sanitize_text_field($_POST['allow_reply']);
            $send_notif = sanitize_text_field($_POST['send_notif']);
			$current_url = esc_url_raw($_POST['current_url']);
			$folder = sanitize_text_field($_POST['folder']);

			$a_rec = explode(",",$recipient);
			$curr_save = false;
			
			// Sanitize and save each URL as post meta
			$attachment_meta = array_map('esc_url_raw', $attachment_urls);


			foreach($a_rec as $rec){
				$mail_title = self::$instance->mmdhg_generate_mail_unique();
				$save_args = array(
						'post_type'		=>'_mmd',
					  	'post_title'    => wp_strip_all_tags( $mail_title ),
					  	'post_content'	=> $mail_body,
					  	'post_author'   => get_current_user_id(),
						'meta_input'   	=> array(
							'recipient' => $rec,
							'subject' => $mail_subject,
							'mail_attchment' => $attachment_meta,
							'is_read' => '0',
							'mtype' => 'inbox',
							'deleted' => '0',
							'is_reply' => $allow_reply,
							'send_notif' => $send_notif,
							'mail_folder' => $folder
						),
				);

				if ($mail_id) {
					$save_args['post_parent'] = $mail_id;
				}
	
				if ($is_publish === 'immediately') {
					$save_args['post_status'] = 'publish';
					$save_args['post_date'] = date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 ) );
				} else {
					$save_args['post_status'] = 'future';
					$postdate_gmt = date(sanitize_text_field($_POST['pub_date']).' '. sanitize_text_field($_POST['pub_time']));
					$save_args['post_date_gmt'] = $postdate_gmt;
				}

				try {
					$curr_save = wp_insert_post( $save_args );
					self::$instance->mmdhg_notify_user_email($rec, $mail_subject, $curr_save);

				} catch (\Exception $ex) {
					echo wp_json_encode(array("success" => false, 'message' => $ex->getMessage()));
				}
	
			}

			if($curr_save){
				if($mail_id){
					$redirect_url = $current_url;
				} else {
					$redirect_url = admin_url('admin.php?page=mmd-admin');
				}
				echo wp_json_encode(array( 'success' => true, 'message' => 'Mail sent successfully.', 'redirect_url' => $redirect_url, 'is_reload' => true));
			}

			wp_die();

		}

		/**
		 * Notify a user via email about a new email 
		 *
		 * @param int    $user_id      The user ID to notify.
		 * @param string $mail_subject The subject of the email.
		 * @param int    $id           The ID of the new email.
		 *
		 * @return bool True if the email is sent successfully, false otherwise.
		 */
		function mmdhg_notify_user_email($user_id, $mail_subject, $id) {

			$user_info = get_userdata($user_id);
			if($user_info){
				$mail_recipient = $user_info->user_email;
			
				$site_title = get_bloginfo('name'); // Escape for HTML context
				$subject = $mail_subject . " - You have new email in " . $site_title;
			
				$url = esc_url(self::$mmdhg_front_slug . '/?mail=' . $id); // Escape for URL context
				$mail_body = 'You have a new email in ' . $site_title . '. Click <a href="' . site_url($url, 'https') . '">here</a> to view';
				$mail_body .= "<br/><br/> This email is forwarded from Member Mail Drop";
			
				$headers = array('Content-Type: text/html; charset=UTF-8');
			
				$mmd_front_class = new MMDHGFrontClass();
				return $mmd_front_class->mmdhg_send_forwarded_mail($mail_recipient, $subject, $mail_body, $headers);
			} else {
				return;
			}
		
		}


		
		
		/**
		 * Conditionally generates HTML for a list of mail actions with corresponding icons.
		 *
		 * This function checks if admin actions are allowed and generates the HTML for mail actions
		 * using the mmdhg_list_actions_loop function accordingly.
		 *
		 * @param bool   $allow_admin If true, allows admin actions; if false, restricts admin actions.
		 * @param array  $icons       An array of corresponding icons for each mail action.
		 * @param array  $actions     An array of mail action names.
		 * @param int    $id          The ID of the mail post for which to generate actions.
		 * @param string $colored     Optional. If set, adds colored styling to the icons.
		 *
		 * @return void Outputs the generated HTML for the mail actions.
		 * @since 1.0.0
		 */
		public static function mmdhg_list_actions($allow_admin, $icons, $actions, $id, $colored){
			if(!$allow_admin){
				if (is_admin()) {
					// do nothing
				} else {
					self::$instance->mmdhg_list_actions_loop($actions, $icons, $id, $colored);
				}
			} else {
				self::$instance->mmdhg_list_actions_loop($actions, $icons, $id, $colored);
			}
		}
		
		/**
		 * Generate HTML for a list of mail actions with corresponding icons.
		 *
		 * This function creates an HTML list of mail actions, each represented by an icon.
		 * The icons are determined based on the provided array of action names and corresponding icons.
		 * Additional details such as mail type, read status, and colored styling are considered for icon customization.
		 *
		 * @param array  $actions An array of mail action names.
		 * @param array  $icons   An array of corresponding icons for each mail action.
		 * @param int    $id      The ID of the mail post for which to generate actions.
		 * @param string $colored Optional. If set, adds colored styling to the icons.
		 *
		 * @return void Outputs the generated HTML for the mail actions.
		 * @since 1.0.0
		 */
		private function mmdhg_list_actions_loop($actions, $icons, $id, $colored=""){
			$x = 0;
			$mtype = get_post_meta($id, 'mtype', true );
			$is_read = get_post_meta($id, 'is_read', true );
			$c = ($colored) ? ' mmd-c' : '';
			foreach ($actions as $action) {
				$escaped_action = esc_attr($action); // Escape for attribute output
				$escaped_id = esc_attr($id); // Escape for attribute output
				$nonce = esc_attr(parent::mmdhg_link_nonce('action-nonce')); // Escape nonce
				$tooltip = esc_attr(ucfirst(str_replace('_', ' ', $action))); // Escape tooltip
		
				echo '<li><a href="#" id="action-' . $escaped_id . '" class="mmd-action-icon ' . $escaped_action . '" data-unique="' . $escaped_id . '" data-action="' . $escaped_action . '" nonce="' . $nonce . '"><img src="' . esc_url(MMDHGMemberMailDrop()->mmdhg_icons($icons[$x], $mtype, $is_read)) . '" class="mmd-lm-icon' . $c . '" mmd-tooltip="' . $tooltip . '"></a></li>';
				$x++;
			}
		}
		
			
		/**
		 * Retrieve metadata for a given mail post based on its ID.
		 *
		 * This function fetches various metadata fields associated with a mail post,
		 * including mail attachments, recipient, subject, read status, starred status,
		 * archived status, and reply status. The metadata is returned in an associative array.
		 *
		 * @param int $id The ID of the mail post for which to retrieve metadata.
		 *
		 * @return array An associative array containing various mail metadata.
		 * @since 1.0.0
		 */
		public static function mmdhg_meta_query($id){

	        $meta = array(
	        	'mail_attchment' 	=> get_post_meta($id, 'mail_attchment', true ),
                'recipient' 		=> get_post_meta($id, 'recipient', true ),
                'subject'			=> get_post_meta($id, 'subject', true ),
                'is_read'			=> get_post_meta($id, 'is_read', true ),
                'is_starred'		=> get_post_meta($id, 'is_starred', true ),
                'is_archived'		=> get_post_meta($id, 'is_archived', true ),
                'is_reply'			=> get_post_meta($id, 'is_reply', true ),
	        );

	        return $meta;
		}
		
		/**
		 * Generate a unique code for mail identification.
		 *
		 * This function generates a random alphanumeric code for identifying mails. It utilizes
		 * the code string and length obtained from the mmdhg_get_qr_unique function.
		 *
		 * @return string The generated unique mail identification code.
		 * @since 1.0.0
		 */
		function mmdhg_generate_mail_unique() {

			$get_qr = self::$instance->mmdhg_get_qr_unique();
			$code_string = $get_qr['cstring'];
			$code_length = $get_qr['clength'];
	
			$random_code = '';
			$length = 8;
	
			for ($i = 0; $i < $length; $i++) {
				$random_code .= $code_string[rand(0, $code_length - 1)];
			}
			$gen_code = strtoupper($random_code);
	
			return $gen_code;
			
		}
	
			
		/**
		 * Generate a unique code string and its length for QR code generation.
		 *
		 * This function generates a code string consisting of alphanumeric characters (0-9, a-z, A-Z)
		 * and returns both the code string and its length in an associative array.
		 *
		 * @return array Associative array containing 'cstring' (code string) and 'clength' (code length).
		 * @since 1.0.0
		 */
		function mmdhg_get_qr_unique(){
			$code_string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$code_length = strlen($code_string);
	
			return $r_unique = array( 'cstring' => $code_string, 'clength' => $code_length );
		}

		/**
		 * Placeholder function for modifying the title of a new mail
		 *
		 * This function is currently not performing any modification to the provided title.
		 * If you have a specific use case for modifying the mail title, you can implement the logic here.
		 *
		 * @param string $title The original title of the new mail.
		 *
		 * @return string The modified or unchanged title.
		 * @since	1.0.0
		 */
		public static function mmdhg_add_new_mail_title_function($title){
			return $title;
		}

		/**
		 * Format a given date for display
		 *
		 * The function calculates the difference between the given date and the current date
		 * to determine the appropriate format for displaying the date.
		 *
		 * @param string $input_date The input date to be formatted.
		 *
		 * @return string The formatted date based on the time difference.
		 * @since	1.0.0
		 */
		public static function mmdhg_format_date($input_date) {
			$current_date = new DateTime();
			$given_date = new DateTime($input_date);
			
			$interval = $current_date->diff($given_date);
			$days_difference = $interval->days;
		
			if ($days_difference <= 1) {
				return $given_date->format('g:i A');
			} elseif ($days_difference < 365) {
				return $given_date->format('F j');
			} else {
				return $given_date->format('F j, Y');
			}
		}

		/**
		 * Limit the length of a given string for display
		 *
		 * If the length of the input string is less than or equal to the specified maximum length,
		 * the original string is returned. If the length exceeds the maximum, the string is truncated,
		 * and '...' is appended to indicate that it has been shortened.
		 *
		 * @param string $inputString The input string to be limited.
		 * @param int    $maxLength    The maximum length allowed for the string.
		 *
		 * @return string The limited or unchanged string.
		 * @since	1.0.0
		 */
		public static function mmdhg_limit_content($inputString, $maxLength) {
			if (strlen($inputString) <= $maxLength) {
				return $inputString;
			} else {
				return substr($inputString, 0, $maxLength) . '...';
			}
		}
		
		/**
		 * AJAX callback to add or update a new folder for mail
		 *
		 * Handles the AJAX request to add a new folder or update an existing one.
		 *
		 * @return void Outputs JSON response indicating success or failure.
		 * @since	1.0.0
		 */
		public static function mmdhg_add_new_folder_mail_ajax_callback(){

			$main_front_page = get_page_by_path(parent::$main_page, OBJECT, 'page');

			$vnonce = parent::mmdhg_v_nonce(sanitize_text_field($_REQUEST['_nonce']), 'mmd-add-folder-form');
			if ( $vnonce == false ) exit;

			$mmd_folder_id = absint($_POST['mmd_folder_id']); // Validate as integer
			$mmd_folder_name = sanitize_text_field($_POST['mmd_folder_name']);
			$mmd_folder_key = sanitize_title($_POST['mmd_folder_key']); // Sanitize for post slug


			if ($main_front_page) {
				$child_post_args = array(
					'post_title'    	=> $mmd_folder_name,
					'post_content' 		=> '[mmd_list]',
					'post_type'     	=> 'page', 
					'post_status'   	=> 'publish',
					'post_parent'   	=> $main_front_page->ID,
					'mmd_folder_status' => '0',
				);
				
				if($mmd_folder_key){
					$child_post_args['post_name'] = $mmd_folder_key;
				}

				if($mmd_folder_id){
					$child_post_args['ID'] = $mmd_folder_id;
					$folder_id = wp_update_post($child_post_args);
					$message = 'Folder Updated.';
				} else {
					$folder_id = wp_insert_post($child_post_args);
					$message = 'New Folder Added.';
				}

				if ($folder_id) {
					echo wp_json_encode(array("success" => true, 'message' => $message));
				} else {
					echo wp_json_encode(array("success" => false, 'message' => 'Error occured!'));
				}
			} else {
				echo wp_json_encode(array("success" => false, 'message' => 'Error occured!'));
			}

			
			wp_die();

		}

		/**
		 * Retrieve query arguments for fetching folders.
		 *
		 * This function defines query arguments to retrieve child pages (folders) of the main MMD page.
		 * Optionally, it can include or exclude folders based on the 'mmdhg_folder_status' meta field.
		 *
		 * @param bool $all If true, includes all folders; if false, includes only folders with 'mmdhg_folder_status' set to '0'.
		 *
		 * @return array|false Array of query arguments if successful, false if the main page is not found.
		 * @since 1.0.0
		 */
		public static function mmdhg_folder_query($all = true){
			$parent_post = get_page_by_path(parent::$main_page, OBJECT, 'page'); // Change 'page' to your parent post type
			if ($parent_post) {
				$child_pages_args = array(
					'post_parent' => $parent_post->ID,
					'post_type' => 'page',
					'post_status' => 'publish',
					'orderby'     => 'title',
					'order'       => 'ASC'
				);

				if(!$all){
					$args['meta_query'][] = array(
						'relation' => 'AND',
						array(
							'key'     => 'mmd_folder_status',
							'value'   => '0',
							'compare' => '=',
						)
					);
				}

				return $child_pages_args;
			} else {
				return false;
			}
		}
		/**
		 * Get the status of the folder based on its ID.
		 *
		 * Retrieves and interprets the 'mmd_folder_status' meta field of a folder to determine its status.
		 *
		 * @param int $id The ID of the folder to retrieve the status for.
		 *
		 * @return string The interpreted status of the folder ('Active' or 'Inactive').
		 * @since 1.0.0
		 */
		public static function mmdhg_get_folder_status($id){
			$status = get_post_meta( $id, 'mmd_folder_status', true );
			if($status){
				// status is 1
				$t_status = 'Inactive';
			} else {
				// status is 0
				$t_status = 'Active';
			}
			return $t_status;
		}
		
		

	}
endif; // End if class_exists check.


if(!function_exists('MMDHGAdminClass')){
    function MMDHGAdminClass(){
		return new MMDHGAdminClass();
	}
}