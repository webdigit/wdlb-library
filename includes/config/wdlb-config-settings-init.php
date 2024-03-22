<?php
/**
 * Main settings initialization file.
 *
 * @package Webdigit
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

add_action( 'admin_init', 'wdlb_settings_initialization' );

/**
 * Initialize the settings.
 */
function wdlb_settings_initialization() {

	/**
	 * General settings tab
	 */

	register_setting( 'wdlb_settings', 'wd_lib_limit_dl' );
	register_setting( 'wdlb_settings', 'wd_lib_auth_roles' );
	register_setting( 'wdlb_settings', 'wd_lib_active_search' );
	register_setting( 'wdlb_settings', 'wd_lib_admin_mails' );
    register_setting( 'wdlb_settings', 'wd_lib_sender_mails' );
	register_setting( 'wdlb_settings', 'wd_lib_mail_title' );
	register_setting( 'wdlb_settings', 'wd_lib_mail_message' );

	add_settings_section(
		'wdlb_settings_section',
		'',
		'wdlb_settings_section_callback',
		'wdlb_settings'
	);
}

add_filter('mce_buttons', 'wdlb_register_buttons');
function wdlb_register_buttons($buttons) {
	array_push($buttons, 'wdlb_button');
	return $buttons;
}

add_filter('mce_external_plugins', 'wdlb_register_tinymce_javascript');
function wdlb_register_tinymce_javascript($plugin_array) {
	$plugin_array['wdlb'] = WD_LIBRARY_URL . '/js/dist/wdlb.tinymceNewContent.bundle.js';
	return $plugin_array;
}