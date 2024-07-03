<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WDLB_Settings {
    private static $instance = null;

	/**
	 * The WordPress database instance.
	 *
	 * @var $wpdb
	 */
	private $wpdb;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Retrieves the instance of the WDLB_Settings class.
	 *
	 * If an instance of the class does not exist, it creates a new instance and returns it.
	 *
	 * @return WDLB_Settings The instance of the WDLB_Settings class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new WDLB_Settings();
		}
		return self::$instance;
	}

	/**
	 * Retrieves the value of a specific setting.
	 *
	 * @param string $setting The name of the setting to retrieve.
	 * @return mixed The value of the specified setting.
	 */
	public function get_settings($setting) {
		return get_option($setting);
	}

	/**
	 * Retrieves the access status for the current user based on their roles.
	 *
	 * @return bool Returns true if the current user has an authorized role, false otherwise.
	 */
	public function get_user_acces() {
	    $authorised_roles = json_decode($this->get_settings('wd_lib_auth_roles'));
		$authorised_roles = is_array($authorised_roles) ? $authorised_roles : [];

		$current_user = wp_get_current_user()->roles;

		if (!count($authorised_roles) || (is_array($authorised_roles) && array_intersect($current_user, $authorised_roles))) {
            return true;
        }

        return false;
	}
}
