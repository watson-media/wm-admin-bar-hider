<?php
/**
 * Plugin Name: WM Admin Bar Hider
 * Plugin URI:
 * Description: Hide the admin bar on the frontend based on page, post type or user roles.
 * Version: 1.0.2
 * Author: Watson Media
 * Author URI: https://www.watsonmedia.net
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// WM Admin Bar Hider Activation
function wm_admin_bar_hider_activate() {
    $default_options = array(
        'pages' => array(),
        'custom_post_types' => array(),
        'user_roles' => array(),
    );
    add_option( 'wm_admin_bar_hider_options', $default_options );
}
register_activation_hook( __FILE__, 'wm_admin_bar_hider_activate' );

// WM Admin Bar Hider De-Activation
function wm_admin_bar_hider_deactivate() {
    delete_option( 'wm_admin_bar_hider_options' );
}
register_deactivation_hook( __FILE__, 'wm_admin_bar_hider_deactivate' );

// WM Admin Bar Hider Primary File
require_once(plugin_dir_path(__FILE__).'wm-admin-bar-hider.php');