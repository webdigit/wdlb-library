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
	public function __construct() {
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
			name text,
			img_couv text,
			link text,
			document_url text,
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

        $result = $this->wpdb->insert(
            $this->table_name,
            array(
                'post_id'       => $datas['post_id'],
				'name'			=> $datas['name'],
                'desc_text'     => $datas['desc_text'],
                'img_couv'      => $datas['img_couv'],
                'link'          => !empty( $datas['link'] ) ? $datas['link'] : '',
            	'document_url'  => !empty( $datas['document_url'] ) ? $datas['document_url'] : '',
            	'category_id'   => isset($datas['category_id']) ? $this->format_category_ids($datas['category_id']): '',
                'created_at'    => $now,
            )
        );

		if (false === $result) {
            new WDLB_Admin_Notices( 1, __( 'Erreur lors de l\'ajout de la ressource !', 'webdigit-library' ) );
        } else {
            new WDLB_Admin_Notices( 2, __( 'Ressource ajoutée avec succès !', 'webdigit-library' ) );
        }
    }

	private function format_category_ids($ids) {
		$category_ids = is_array($ids) ? $ids : array($ids);
		return serialize($category_ids);
	}

	/**
	 * Retrieves the link files associated with a specific post.
	 *
	 * @param int $post_id The ID of the post.
	 * @return array The link files associated with the post.
	 */
	public function get_link_files( $post_id ) {
		$result = $this->wpdb->get_row( "SELECT * FROM $this->table_name WHERE id = $post_id" );

		if (null === $result) {
			new WDLB_Admin_Notices( 1, __( 'Ressource non trouvée !', 'webdigit-library' ) );
			return null;
		}

		return $result;
	}

	/**
	 * Deletes the link files associated with a given post ID.
	 *
	 * @param int $post_id The ID of the post.
	 * @return void
	 */
	public function delete_link_files( $post_id ) {
		$result = $this->wpdb->delete(
			$this->table_name,
			array( 'id' => $post_id )
		);

		if (false === $result) {
			new WDLB_Admin_Notices( 1, __( 'Erreur lors de la suppression de la ressource !', 'webdigit-library' ) );
		} else {
			new WDLB_Admin_Notices( 2, __( 'Ressource supprimée avec succès !', 'webdigit-library' ) );
		}
	}

	/**
	 * Retrieves all link files from the database.
	 *
	 * @return array The array of link files.
	 */
	public function get_all_link_files() {
		$result = $this->wpdb->get_results("SELECT * FROM $this->table_name");
		return $result;
	}

	/**
	 * Edits the link files with the provided data.
	 *
	 * @param array $datas The data to update the link files.
	 * @return void
	 */
	public function edit_link_files( $datas ) {
		$now = current_time( 'mysql' );
		$result = $this->wpdb->update(
			$this->table_name,
			array(
				'desc_text'     => $datas['desc_text'],
				'name'			=> $datas['name'],
				'img_couv'      => $datas['img_couv'],
				'link'          => !empty( $datas['link'] ) ? $datas['link'] : '',
				'document_url'  => !empty( $datas['document_url'] ) ? $datas['document_url'] : '',
				'category_id'   => isset($datas['category_id']) ? $this->format_category_ids($datas['category_id']): '',
				'created_at'    => $now,
			),
			array( 'id' => $datas['file_id'] )
		);

		if (false === $result) {
            new WDLB_Admin_Notices( 1, __( 'Erreur lors de la modificaiton de la ressource !', 'webdigit-library' ) );
        } else {
            new WDLB_Admin_Notices( 2, __( 'Ressource modifiée avec succès !', 'webdigit-library' ) );
        }
	}
}
