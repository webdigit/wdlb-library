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

		<form method="post" enctype="multipart/form-data">
		<?php
				settings_fields( 'wdlb_settings' );
				do_settings_sections( 'wdlb_settings' );
		?>
		</form>
	</div>
	<?php
}