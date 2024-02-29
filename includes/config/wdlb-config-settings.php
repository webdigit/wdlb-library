<?php
/**
 * General settings for the plugin.
 *
 * @package Webdigit
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Show a success message if the form was submitted
 *
 * @param string $option_name The name of the option.
 */
function wdlb_general_settings_save_option( $option_name ) {
	if ( ! isset( $_POST['wdlb_settings_nonce'] ) ||
	! wp_verify_nonce( sanitize_key( $_POST['wdlb_settings_nonce'] ), 'wdlb_settings' ) ) {
		wp_die( 'Security check failed' );
	}

	$opt_name_sanitize = isset( $_POST[ $option_name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) ) : '';
	update_option( $option_name, $opt_name_sanitize );
}

/**
 * Save options and show a success message if the form was submitted.
 */
function wdlb_general_settings_save_options() {
    wdlb_general_settings_save_option( 'wd_lib_limit_dl' );
    wdlb_general_settings_save_option( 'wd_lib_active_search' );
    wdlb_general_settings_save_option( 'wd_lib_admin_mails' );
    wdlb_general_settings_save_option( 'wd_lib_mail_title' );
    wdlb_general_settings_save_option( 'wd_lib_mail_message' );
    new WDLB_Admin_Notices( 2, __( 'Settings saved successfully !', 'webdigit-library' ) );
}

/**
 * Add the general settings section.
 */
function wdlb_settings_section_callback() {

	if ( isset( $_POST['submit'] ) ) {
		wdlb_general_settings_save_options();
	}
	?>
		<?php wp_nonce_field( 'wdlb_settings', 'wdlb_settings_nonce' ); ?>
		
		<table class="form-table">

        <tr>
			<th scope="row">
			<?php
                esc_html_e(
                    'Enable Search:',
                    'webdigit-library'
                );
			?>
			</th>
			<td>
				<label class="switch">
					<input type="checkbox" id="wd_lib_active_search" name="wd_lib_active_search" <?php echo ( get_option( 'wd_lib_active_search', 'on' ) === 'on' ) ? 'checked' : ''; ?>>
					<span class="slider round"></span>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
                <?php
                    esc_html_e(
                        'Limitations:',
                        'webdigit-library'
                    );
                ?>
			</th>
			<td>
                <input type="text" name="wd_lib_limit_dl" value="<?php echo esc_attr( get_option( 'wd_lib_limit_dl', '0' ) ); ?>" />
            </td>
		</tr>
		</table>
		<td><input type='submit' name='submit' value='
            <?php
                esc_html_e(
                    'Save Changes',
                    'webdigit-library'
                );
            ?>
		' class='button button-primary' /></td>
	<?php
}