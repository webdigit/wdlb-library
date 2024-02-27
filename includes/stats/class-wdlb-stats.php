<?php
/**
 * This file is responsible to manage stats.
 *
 * @package Webdigit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to manage stats.
 */
class WDLB_Stats {
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
		$this->table_name          = $wpdb->prefix . 'wdlb_library_stats';
	}

	/**
	 * Get the instance of the class.
	 *
	 * @return wdlb_Stats
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new WDLB_Stats();
		}
		return self::$instance;
	}

	/**
	 * Create the table.
	 *
	 * @return void
	 */
	public function create_table() {
		$charset_collate = $this->wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
			ressource_name text,
			document text,
			email text NOT NULL,
			name text,
			surname text,
			phone text,
			requestDate datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
        ) $charset_collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

    /**
     * Insert statistics data into the database.
     *
     * @param array $datas The data to be inserted.
     * @return void
     */
    public function insert_stats( $datas ) {
        $now = current_time( 'mysql' );
        $this->wpdb->insert(
            $this->table_name,
            array(
                'ressource_name' => $datas['ressource_name'],
                'document'       => $datas['document'],
                'email'          => $datas['email'],
                'name'           => $datas['name'],
                'surname'        => $datas['surname'],
                'phone'          => $datas['phone'],
                'requestDate'    => $now,
            )
        );
    }
}
