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
	public function __construct() {
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
			parent_id mediumint(9),
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

		$result = $this->wpdb->insert(
			$this->table_name,
			array(
				'category_name' => $datas['category_name'],
				'image_url'     => $datas['image_url'],
				'email_link'    => $datas['email_link'],
				'parent_id'     => $datas['parent_id'],
				'created_at'    => $now,
			)
		);

		if (false === $result) {
            new WDLB_Admin_Notices( 1, __( 'Erreur lors de l\'ajout de la catégorie !', 'webdigit-library' ) );
        } else {
            new WDLB_Admin_Notices( 2, __( 'Catégorie ajoutée avec succès !', 'webdigit-library' ) );
        }
	}

	/**
	 * Retrieves all categories from the database.
	 *
	 * @return array The array of categories.
	 */
	public function get_categories() {
		$categories = $this->wpdb->get_results( "SELECT * FROM $this->table_name" );
		return $categories;
	}

	/**
	 * Deletes a category by its ID.
	 *
	 * @param int $id The ID of the category to delete.
	 * @return void
	 */
	public function delete_category( $id ) {
		$result = $this->wpdb->delete(
			$this->table_name,
			array( 'id' => $id )
		);

		if (false === $result) {
			new WDLB_Admin_Notices( 1, __( 'Erreur lors de la suppression de la catégorie !', 'webdigit-library' ) );
		} else {
			new WDLB_Admin_Notices( 2, __( 'Catégorie supprimée avec succès !', 'webdigit-library' ) );
		}
	}

	/**
	 * Retrieves a category by its ID.
	 *
	 * @param int $id The ID of the category to retrieve.
	 * @return object|null The category object if found, null otherwise.
	 */
	public function get_category( $id ) {
		$category = $this->wpdb->get_row( "SELECT * FROM $this->table_name WHERE id = $id" );

		if (null === $category) {
			new WDLB_Admin_Notices( 1, __( 'Catégorie non trouvée !', 'webdigit-library' ) );
			return null;
		}

		return $category;
	}

	/**
	 * Update a category with the provided data.
	 *
	 * @param array $datas The data to update the category with.
	 *                    - category_name (string): The new name of the category.
	 *                    - image_url (string): The new image URL of the category.
	 *                    - email_link (string): The new email link of the category.
	 *                    - id (int): The ID of the category to update.
	 * @return void
	 */
	public function update_category( $datas ) {
		$result = $this->wpdb->update(
			$this->table_name,
			array(
				'category_name' => $datas['category_name'],
				'image_url'     => $datas['image_url'],
				'email_link'    => $datas['email_link'],
				'parent_id'     => $datas['parent_id'],
			),
			array( 'id' => $datas['id'] )
		);

		if (false === $result) {
			new WDLB_Admin_Notices( 1, __( 'Erreur lors de la mise à jour de la catégorie !', 'webdigit-library' ) );
		} else {
			new WDLB_Admin_Notices( 2, __( 'Catégorie mise à jour avec succès !', 'webdigit-library' ) );
		}
	}
}
