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
	public function __construct() {
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
			categories_name text,
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
    public function insert_stats( $form_data, $files_data ) {
        $files = $files_data['file'];
        $categories = $files_data['categories'];

        $now = current_time( 'mysql' );
        $this->wpdb->insert(
            $this->table_name,
            array(
                'ressource_name' => $this->wdlb_get_all_files_name($files),
                'categories_name' => $this->wdlb_get_all_categories_name($categories),
                'email'           => $form_data['wdlb_email'],
                'name'            => $form_data['wdlb_name'],
                'surname'         => $form_data['wdlb_surname'],
                'phone'           => $form_data['wdlb_phone'],
                'requestDate'     => $now,
            )
        );
    }

    /**
     * Retrieves all the stats from the database.
     *
     * @return array|null The array of stats or null if no stats found.
     */
    public function get_all_stats() {
        $sql = "SELECT * FROM $this->table_name";
        return $this->wpdb->get_results( $sql );
    }

    public function wdlb_get_all_files_name($files) {
        $files_name = [];
        foreach ($files as $file) {
            $files_name[] = $file->name;
        }
        return implode(', ', $files_name);
    }

    public function wdlb_get_all_categories_name($categories) {
        $categories_name = [];
        foreach ($categories as $category) {
            $categories_name[] = $category->category_name;
        }
        return implode(', ', $categories_name);
    }
}
