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
        include_once WD_LIBRARY_PATH . 'includes/stats/class-wdlb-stats.php';
        include_once WD_LIBRARY_PATH . 'includes/link_files/class-wdlb-link-files.php';
		// Settings Tabs.
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
		load_plugin_textdomain( 'webdigit-chatbot', false, dirname( WD_LIBRARY_BASENAME ) . '/languages/' );
	}

    /**
	 * Add entry to admin menu.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
        add_menu_page(
            __('Library','webdigit-library'),
            __('Library', 'webdigit-library'),
            'manage_options',
            'webdigit-library',
            'webdigit_library_files',
            WD_LIBRARY_URL . '/assets/img/icon.png',
            20
        );
        add_submenu_page(
            'webdigit-library',
            __('Catégories', 'webdigit-library'),
            __('Catégories', 'webdigit-library'),
            'manage_options',
            'webdigit-library-categories',
            'webdigit_library_categories'
        );
        add_submenu_page(
            'webdigit-library',
            __('Statistiques', 'webdigit-library'),
            __('Statistiques', 'webdigit-library'),
            'manage_options',
            'webdigit-library-stats',
            'webdigit_library_stats'
        );
        add_submenu_page(
            'webdigit-library',
            __('Paramètres', 'webdigit-library'),
            __('Paramètres', 'webdigit-library'),
            'manage_options',
            'webdigit-library-settings',
            'webdigit_library_settings'
        );
	}
    
}
