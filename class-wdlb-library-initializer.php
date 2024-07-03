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
		add_action('wp_ajax_wdlb_manage_submited_form', 'wdlb_manage_submited_form');
		add_action('wp_ajax_nopriv_wdlb_manage_submited_form', 'wdlb_manage_submited_form');
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
        include_once WD_LIBRARY_PATH . 'includes/stats/wdlb-config-stats.php';
        include_once WD_LIBRARY_PATH . 'includes/link_files/class-wdlb-link-files.php';
        include_once WD_LIBRARY_PATH . 'includes/link_files/wdlb-config-link-files.php';
        include_once WD_LIBRARY_PATH . 'includes/config/class-wdlb-settings.php';
		include_once WD_LIBRARY_PATH . 'includes/class-wdlb-admin-notices.php';
		include_once WD_LIBRARY_PATH . 'includes/render/wdlb-render-library.php';
		include_once WD_LIBRARY_PATH . 'includes/render/wdlb-render-form.php';
		include_once WD_LIBRARY_PATH . 'includes/render/wdlb-render-search.php';
		include_once WD_LIBRARY_PATH . 'includes/render/wdlb-render-categories-filter.php';
		include_once WD_LIBRARY_PATH . 'includes/render/gutenberg-custom-block/wdlb-add-gutenberg-block.php';

		if (did_action('elementor/loaded' )) {
			include_once WD_LIBRARY_PATH . 'includes/render/elementor-custom-block/wdlb-add-elementor-widget.php';
		}

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
        wp_register_script('wd-admin-script', WD_LIBRARY_URL . '/js/dist/wdlb.admin.bundle.js', array('jquery'), '1.0', true);
		$ajaxurl = admin_url('admin-ajax.php');
		$script = 'const ajax_object = ' . wp_json_encode( array('ajaxurl' => $ajaxurl) ) . ';';
		wp_add_inline_script('wd-admin-script', $script, 'before');
        wp_enqueue_script('wd-admin-script');

        wp_enqueue_style('wd_style_admin_css', WD_LIBRARY_URL . '/css/admin.css', array(), $this->defaults['version']);
	}

    /**
	 * Enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
        wp_enqueue_script('wd-library-script', WD_LIBRARY_URL .  '/js/dist/wdlb.main.bundle.js', array('jquery'), '1.0', true);
        wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/bccc55e953.js', array('jquery'), '1.0', true);

		$limitations = $this->wdlb_get_settings('wd_lib_limit_dl');
		$script = 'const limitations = ' . wp_json_encode( array($limitations) ) . ';';
		wp_add_inline_script('wd-library-script', $script, 'before');

		$libRoles = $this->wdlb_get_settings('wd_lib_auth_roles');
		$script = 'const libRoles = ' . wp_json_encode( array($libRoles) ) . ';';
		wp_add_inline_script('wd-library-script', $script, 'before');

		$ajax_data = array('admin_ajax' => admin_url('admin-ajax.php'));
		$script = 'const ajax_data = ' . wp_json_encode( $ajax_data ) . ';';
		wp_add_inline_script('wd-library-script', $script, 'before');

        wp_enqueue_style('wd_style_css', WD_LIBRARY_URL .  '/css/main.css', array(), $this->defaults['version']);
        wp_enqueue_style('wd_font_awesome_css', WD_LIBRARY_URL .  '/css/all.min.css', array(), $this->defaults['version']);
	}

    /**
	 * Get library limitation from options.
	 *
	 * @return string
	 */
	public function wdlb_get_settings($setting) {
		$settings_manager = WDLB_Settings::get_instance();
		return $settings_manager->get_settings($setting);
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
            array( $this, 'wdlb_link_files_callback' ),
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
	public function wdlb_link_files_callback() {
		wdlb_manage_linked_files();
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
		wdlb_display_stats();
	}

	/**
	 * Callback for admin categories menu.
	 */
	public function wdlb_categories_callback() {
		wdlb_manage_categories();
	}

}
