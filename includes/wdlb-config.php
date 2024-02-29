<?php
/**
 * This file is reponsible for the admin dashboard.
 *
 * @package Webdigit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Displays the configuration form for the plugin.
 */
function wdlb_config_form() {
	$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'wdlb_settings';
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<h2 class="nav-tab-wrapper">
			<a href="?page=wdlb&tab=wdlb_settings" class="nav-tab <?php echo $active_tab == 'wdlb_settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General Settings', 'webdigit-library' ); ?></a>
			<a href="?page=wdlb&tab=wdlb_categories" class="nav-tab <?php echo $active_tab == 'wdlb_categories' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Categories', 'webdigit-library' ); ?></a>
			<a href="?page=wdlb&tab=wdlb_stats" class="nav-tab <?php echo $active_tab == 'wdlb_stats' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Stats', 'webdigit-library' ); ?></a>	
		</h2>

		<form method="post" enctype="multipart/form-data">
		<?php
		switch ( $active_tab ) {
            case 'wdlb_settings':
				settings_fields( 'wdlb_settings' );
				do_settings_sections( 'wdlb_settings' );
				break;
			case 'wdlb_categories':
				settings_fields( 'wdlb_categories' );
				do_settings_sections( 'wdlb_categories' );
				break;
			case 'wdlb_stats':
				settings_fields( 'wdlb_stats' );
				do_settings_sections( 'wdlb_stats' );
				break;
			default:
				settings_fields( 'wdlb_settings' );
				do_settings_sections( 'wdlb_settings' );
				break;
		}
		?>
		</form>
	</div>
	<?php
}