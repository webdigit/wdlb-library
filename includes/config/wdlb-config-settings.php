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

	if (is_array($_POST[ $option_name ])) {
	   $opt_name_sanitize = isset( $_POST[ $option_name ] ) ? json_encode($_POST[ $option_name ]) : '';
	} else {
	   $opt_name_sanitize = isset( $_POST[ $option_name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) ) : '';
	}

	update_option( $option_name, $opt_name_sanitize );
}

/**
 * Save options and show a success message if the form was submitted.
 */
function wdlb_general_settings_save_options() {
    wdlb_general_settings_save_option( 'wd_lib_limit_dl' );
    wdlb_general_settings_save_option( 'wd_lib_auth_roles' );
    wdlb_general_settings_save_option( 'wd_lib_active_search' );
    wdlb_general_settings_save_option( 'wd_lib_admin_mails' );
    wdlb_general_settings_save_option( 'wd_lib_sender_mails' );
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
                'Authorised Roles:',
                'webdigit-library'
                );
            ?>
            </th>
            <td>
                <?php
                    $roles = get_editable_roles();
                    $auth_roles = json_decode(get_option( 'wd_lib_auth_roles' ));
                ?>
                <?php foreach ($roles as $role) : ?>
                    <?php
                        $checked = false;
                        if ( isset($auth_roles) && is_array($auth_roles) && count($auth_roles) > 0) {
                            foreach ($auth_roles as $auth_role) {
                                if (strtolower($role['name']) === $auth_role) {
                                    $checked = true;
                                    break;
                                }
                            }
                        }
                    ?>
                    <label>
                        <input type="checkbox" name="wd_lib_auth_roles[]" value="<?php echo strtolower($role['name']); ?>" <?php echo $checked ? 'checked' : ''; ?>>
                        <?php echo $role['name']; ?>
                    </label><br>
                <?php endforeach; ?>
            </td>
        </tr>
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
            <tr valign="top">
                <th scope="row">
                    <?php
                    esc_html_e(
                        'Sender mail:',
                        'webdigit-library'
                    );
                    ?>
                </th>
                <td>
                    <input required type="mail" name="wd_lib_sender_mails" value="<?php echo esc_attr( get_option( 'wd_lib_sender_mails', '' ) ); ?>" />
                </td>
            </tr>
		<tr valign="top">
			<th scope="row">
                <?php
                    esc_html_e(
                        'Recipients mails:',
                        'webdigit-library'
                    );
                ?>
			</th>
			<td>
                <input type="text" name="wd_lib_admin_mails" value="<?php echo esc_attr( get_option( 'wd_lib_admin_mails', '' ) ); ?>" />
            </td>
		</tr>
		<tr valign="top">
			<th scope="row">
                <?php
                    esc_html_e(
                        'Email title (customer):',
                        'webdigit-library'
                    );
                ?>
			</th>
			<td>
                <input type="text" name="wd_lib_mail_title" value="<?php echo esc_attr( get_option( 'wd_lib_mail_title', '' ) ); ?>" />
            </td>
		</tr>
		<tr valign="top">
			<th scope="row">
                <?php
                    esc_html_e(
                        'Email content (customer):',
                        'webdigit-library'
                    );
                ?>
			</th>
			<td>
			    <?php
					wp_editor(stripslashes(get_option('wd_lib_mail_message')), 'wd_lib_mail_message', ['textarea_name' => 'wd_lib_mail_message']);
				?>
            </td>
		</tr>
		</table>
		<input type='submit' name='submit' value='
            <?php
                esc_html_e(
                    'Save Changes',
                    'webdigit-library'
                );
            ?>
		' class='button button-primary' />
	<?php
}
