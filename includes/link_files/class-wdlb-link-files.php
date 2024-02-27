<?php
/**
 * This file is responsible to manage linked Files
 *
 * @package Webdigit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to manage linked files.
 */
class WDLB_Linkfiles {
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
		$this->table_name          = $wpdb->prefix . 'wdlb_library_link_files';
	}

	/**
	 * Get the instance of the class.
	 *
	 * @return WDLB_Linkfiles
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new WDLB_Linkfiles();
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
			post_id mediumint(9),
			desc_text text,
			img_couv text,
			link text,
			img text,
			category_id text,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
        ) $charset_collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

    /**
     * Inserts link files into the database.
     *
     * @param array $datas The data to be inserted.
     * @return void
     */
    public function insert_link_files( $datas ) {
        $now = current_time( 'mysql' );
        $this->wpdb->insert(
            $this->table_name,
            array(
                'post_id'       => $datas['post_id'],
                'desc_text'     => $datas['desc_text'],
                'img_couv'      => $datas['img_couv'],
                'link'          => $datas['link'],
                'img'           => $datas['img'],
                'category_id'   => $datas['category_id'],
                'created_at'    => $now,
            )
        );
    }
}
