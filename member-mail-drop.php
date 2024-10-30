<?php
/*
 * Plugin Name: Member Mail Drop
 * Description: Digitize Physical Mail for Your Members. Member Mail Drop is a WordPress plugin primarily crafted to digitize physical mail for your members, converting it into an easily accessible virtual format.
 * Version: 1.0.0
 * Author: HG Pro
 * Plugin URI: https://membermaildrop.com/
 * Text Domain: member-mail-drop
 * Domain Path: /languages
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


// Plugin name
define( 'MMDHG_NAME', 'Member Mail Drop' );

// Plugin Author
define( 'MMDHG_AUTHOR', 'Member Mail Drop' );

// Plugin text domain
define( 'MMDHG_T_DOMAIN', 'member-mail-drop' );

// Plugin version
define( 'MMDHG_VERSION',       '1.0.0' );

// Plugin Root File
define( 'MMDHG_PLUGIN_FILE',   __FILE__ );

// Plugin base
define( 'MMDHG_PLUGIN_BASE',   plugin_basename( MMDHG_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'MMDHG_PLUGIN_DIR',    plugin_dir_path( MMDHG_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'MMDHG_PLUGIN_URL',    plugin_dir_url( MMDHG_PLUGIN_FILE ) );


/**
 * Load the main class for the core functionality
 */
$file_path = MMDHG_PLUGIN_DIR . 'core/class-member-mail-drop.php';

if (file_exists($file_path)) {
    include_once $file_path;
} else {
    wp_die("Error: The required file does not exist.");
}



/**
 * The main function to load the only instance
 * of the master class.
 *
 * @author  Hudson Group
 * @since   1.0.0
 * @return  object|MMDHGMemberMailDrop
 */
function mmdhg_main_membermaildrop() {
    return MMDHGMemberMailDrop::mmdhg_instance();
}

mmdhg_main_membermaildrop();