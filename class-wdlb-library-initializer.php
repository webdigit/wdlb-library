<?php
/**
 * This file is responsible for initializing the plugin.
 * It also contains the main class for the plugin.
 *
 * @package Webdigit
 */

 if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WDLB_Library_Initializer
 *
 * This class is responsible for initializing the library plugin.
 */
class WDLB_Library_Initializer {
    private static $instance;

    public $options = array(
        'wd_lib_auth_roles' => '',
        'wd_lib_limit_dl' => 0,
        'wd_lib_admin_mails' => '', 
        'wd_lib_mail_title' => '', 
        'wd_lib_mail_message' => ''
    );

    public $defaults = array(
        'general' => array(
            'wd_lib_auth_roles' => '',
            'wd_lib_limit_dl' => 0,
            'wd_lib_admin_mails' => '', 
            'wd_lib_mail_title' => '', 
            'wd_lib_mail_message' => ''
        ),
        'version' => '1.0'
    );

	/**
	 * Class WDLB_Library_Initializer
	 * 
	 * This class initializes the library plugin by defining constants, retrieving options, and adding necessary actions.
	 */
	public function __construct() {
		$this->define_constants();

		foreach ( $this->options as $key => $value ) {
			$this->options[ $key ] = get_option( $key );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

    /**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->defaults['version'];
	}

    /**
	 * Setup plugin constants.
	 *
	 * @return void
	 */
	private function define_constants() {
		define( 'WD_LIBRARY_PATH', plugin_dir_path( __FILE__ ) );
		define( 'WD_LIBRARY_URL', plugins_url( '', __FILE__ ) );
		define( 'WD_LIBRARY_BASENAME', plugin_basename( __FILE__ ) );
	}

    /**
	 * Activate plugin.
	 *
	 * @return void
	 */
	public function activate() {
		$library_categories = WDLB_Categories::get_instance();
		$library_categories->create_table();

		$library_stats = WDLB_Stats::get_instance();
		$library_stats->create_table();

        $linked_files = WDLB_Linkfiles::get_instance();
        $linked_files->create_table();

		update_option( 'wdlb_library_version', $this->get_version() );
	}

    /**
	 * Deactivate plugin.
	 *
	 * @return void
	 */
	public function deactivate() {
	}

    /**
	 * Main plugin instance.
	 *
	 * @return object
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			add_action( 'init', array( self::$instance, 'load_textdomain' ) );

			self::$instance->includes();
		}

		return self::$instance;
	}

    /**
	 * Include required files.
	 *
	 * @return void
	 */
	private function includes() {
        include_once WD_LIBRARY_PATH . 'includes/categories/class-wdlb-categories.php';
		include_once WD_LIBRARY_PATH . 'includes/categories/wdlb-config-categories.php';
        include_once WD_LIBRARY_PATH . 'includes/stats/class-wdlb-stats.php';
        include_once WD_LIBRARY_PATH . 'includes/link_files/class-wdlb-link-files.php';
		include_once WD_LIBRARY_PATH . 'includes/class-wdlb-admin-notices.php';

		// Settings Tabs.
		include_once WD_LIBRARY_PATH . 'includes/wdlb-config.php';
		include_once WD_LIBRARY_PATH . 'includes/config/wdlb-config-settings-init.php';
		include_once WD_LIBRARY_PATH . 'includes/config/wdlb-config-settings.php';
	}
    
	/**
	 * Add admin menu.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
        wp_enqueue_media();
        wp_register_script('wd-admin-script', WD_LIBRARY_URL . '/js/admin.js', array('jquery'), '1.0', true);
        wp_localize_script('wd-admin-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
        wp_enqueue_script('wd-admin-script');

        wp_enqueue_style('wd_style_css', WD_LIBRARY_URL . '/css/style.css', array(), $this->defaults['version']);
	}

    /**
	 * Enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
        wp_enqueue_script('wd-library-script', WD_LIBRARY_URL .  '/js/library.js', array('jquery'), '1.0', true);
        wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/bccc55e953.js', array('jquery'), '1.0', true);
        
        
        wp_localize_script('wd-library-script', 'limitations', $this->get_limitation());

        wp_enqueue_style('wd_style_css', WD_LIBRARY_URL .  '/css/main.css', array(), $this->defaults['version']);
	}

    /**
	 * Get library limitation from options.
	 *
	 * @return string
	 */
	public function get_limitation() {
		return $this->options['wd_lib_limit_dl'];
	}

    /**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'webdigit-library', false, dirname( WD_LIBRARY_BASENAME ) . '/languages/' );
	}

    /**
	 * Add entry to admin menu.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
        add_menu_page(
            'Library',
            'Library',
            'manage_options',
            'wdlb',
            array( $this, 'wdlb_callback' ),
            WD_LIBRARY_URL . '/assets/img/icon.png',
            20
        );
        add_submenu_page(
            'wdlb',
            'Catégories',
            'Catégories',
            'manage_options',
            'wdlb_categories',
            array( $this, 'wdlb_categories_callback' ),
        );
        add_submenu_page(
            'wdlb',
            'Stats',
            'Stats',
            'manage_options',
            'wdlb_stats',
            array( $this, 'wdlb_stats_callback' )
        );
        add_submenu_page(
            'wdlb',
            'Settings',
            'Settings',
            'manage_options',
            'wdlb_settings',
			array( $this, 'wdlb_settings_callback' )
        );
	}

	/**
	 * Callback for admin main menu.
	 */
	public function wdlb_callback() {
		wdlb_config_form();
	}

	/**
	 * Callback for admin settings menu.
	 */
	public function wdlb_settings_callback() {
		wdlb_config_form();
	}

	/**
	 * Callback for admin stats menu.
	 */
	public function wdlb_stats_callback() {
		// wdlb_config_form();
	}

	/**
	 * Callback for admin categories menu.
	 */
	public function wdlb_categories_callback() {
		wdlb_manage_categories();
	}
    
}
