<?php
/**
 * This file is responsible to manage categories.
 *
 * @package Webdigit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to manage categories.
 */
class WDLB_Categories {
	private static $instance = null;

	/**
	 * The WordPress database instance.
	 *
	 * @var $wpdb
	 */
	private $wpdb;

	/**
	 * The name of the table.
	 *
	 * @var $table_name
	 */
	private $table_name;

	/**
	 * Constructor.
	 */
	private function __construct() {
		global $wpdb;
		$this->wpdb                = $wpdb;
		$this->table_name          = $wpdb->prefix . 'wdlb_library_categories';
	}

	/**
	 * Get the instance of the class.
	 *
	 * @return wdlb_Categories
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new WDLB_Categories();
		}
		return self::$instance;
	}


	/**
	 * Creates a table in the database for storing categories.
	 */
	public function create_table() {
		$charset_collate = $this->wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $this->table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			category_name text NOT NULL,
			image_url text,
			email_link text,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Inserts a new category into the database.
	 *
	 * @param array $datas The category data to be inserted.
	 *                    - category_name (string): The name of the category.
	 *                    - image_url (string): The URL of the category image.
	 *                    - email_link (string): The email link associated with the category.
	 *
	 * @return void
	 */
	public function insert_categories( $datas ) {
		$now = current_time( 'mysql' );
		$this->wpdb->insert(
			$this->table_name,
			array(
				'category_name' => $datas['category_name'],
				'image_url'     => $datas['image_url'],
				'email_link'    => $datas['email_link'],
				'created_at'    => $now,
			)
		);
	}
}
