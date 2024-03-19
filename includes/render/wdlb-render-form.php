<?php

function wdlb_render_form () {
    $content_form  = '<div id="wdlb-form-wrapper">';
    $content_form .= wdlb_create_form();
    $content_form .= '<div id="wdlb-form-fade-screen"></div>';
    $content_form .= '</div>';

    return $content_form;
}

function wdlb_create_form () {
    if (isset($_POST['wdlb_form_submit'])) {
        wdlb_manage_submited_form($_POST);
    }

    $form = '<div id="wdlb-form-popup">';
    $form .= '<form class="form" method="post">';
    $form .= '<div id="cross-popup"></div>';
    $form .= '<div class="wdlb-text-form">';
    $form .= '<p>Vous y êtes presque ! Remplissez-les champs ci-dessous et les ressources sélectionnées vous seront automatiquement transmises par mail.</p>';
    $form .= '</div>';
    $form .= '<input type="email" name="wdlb_email" placeholder="Adresse email" required />';
    $form .= '<input type="text" name="wdlb_name" placeholder="Nom" required />';
    $form .= '<input type="text" name="wdlb_surname" placeholder="Prénom" required />';
    $form .= '<input type="text" name="wdlb_phone" placeholder="Numéro de téléphone" />';
    $form .= '<input type="hidden" value="" id="wdlb_hidden_data_field" name="wdlb_files_id" />';
    $form .= '<div class="wd-rgpd-check-form">';
    $form .= '<input type="checkbox" onchange="wdlb_accept_gdpr(this)" required /><p>En nous soumettant vos données, vous acceptez que celles-ci soient traitées conformément à notre <a href="' . esc_attr( esc_url( get_privacy_policy_url() ) ) . '">Politique de confidentialité</a>*</p>';
    $form .= '</div>';
    $form .= '<input type="submit" id="wdlb_requestFormBtn" class="submit-btn" disabled name="wdlb_form_submit" value="Envoyer" />';
    $form .= '</form>';
    $form .= '</div>';

    return $form;
}

function wdlb_manage_submited_form ($form_data) {
    $files_id = $form_data['wdlb_files_id'];

    $files_data = json_decode(stripslashes($files_id), true);
    $files_data = get_all_infos($files_data);

    foreach ($files_data as $data) {
        wdlb_send_email($form_data, $data);
    }
}

function wdlb_send_email ($form_data, $files_data) {
    wdlb_send_admin_email($form_data, $files_data);
    // wdlb_send_customer_email($email, $name, $surname, $phone, $files);
    wdlb_insert_stats($form_data, $files_data);
}

function wdlb_insert_stats ($form_data, $files_data) {
    $stats_manager = WDLB_Stats::get_instance();
    $stats_manager->insert_stats($form_data, $files_data);
}

function wdlb_send_admin_email ($form_data, $files_data) {
    $email = $form_data['wdlb_email'];
    $name = $form_data['wdlb_name'];
    $surname = $form_data['wdlb_surname'];
    $phone = $form_data['wdlb_phone'];
    $files = $files_data['file'];
    $categories = $files_data['categories'];
    $emails_to = [];

    $message = wdlb_get_mail_message() . "<br /><br />";

    $message .= '<b>Nom : </b>' . $name . "<br />";
    $message .= '<b>Prénom : </b>' . $surname . "<br />";
    $message .= '<b>Email : </b>' . $email . "<br />";
    $message .= '<b>Téléphone : </b>' . $phone . "<br /><br />";

    $message .= 'Fichiers : ';
    $message .= '<ul>';
    foreach ($files as $file) {
        $message .= "<li>" . $file->name . "</li>";
    }
    $message .= '</ul>';

    $message .= 'Catégories : ';
    $message .= '<ul>';
    foreach ($categories as $category) {
        $message .= "<li>" . $category->category_name . "</li>";
        $emails_to[] = $category->email_link;
    }
    $message .= '</ul>';

    $headers = 'From: ' . wdlb_get_sender_email() . "\r\n" .
        'CC: ' . implode(',', $emails_to) . "\r\n" .
        'Reply-To: ' . $email . "\r\n".
        'Content-Type: text/html; charset=UTF-8' . "\r\n";
    wp_mail(wdlb_get_admin_email(), wdlb_get_mail_title(), $message, $headers);
}

function wdlb_get_admin_email() {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_settings('wd_lib_admin_mails');
}

function wdlb_get_mail_title() {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_settings('wd_lib_mail_title');
}

function wdlb_get_mail_message () {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_settings('wd_lib_mail_message');
}

function wdlb_get_sender_email () {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_settings('wd_lib_sender_mails');
}

/**
 * Retrieves files by their IDs.
 *
 * This function gets an instance of the `WDLB_Linkfiles` class and uses it to retrieve files by their IDs.
 * The IDs are provided as a comma-separated string, which is then exploded into an array.
 * The function iterates over the array of IDs, retrieving the corresponding file for each ID and adding it to the `$files` array.
 * The function returns the `$files` array, which contains the retrieved files.
 *
 * @param string $files_id A comma-separated string of file IDs.
 * @return array An array of files corresponding to the provided IDs.
 */
function wdlb_get_files_by_id ($files_id) {
    $files_manager = WDLB_Linkfiles::get_instance();
    $files_id = explode(',', $files_id);
    $files = [];

    foreach ($files_id as $file_id) {
        $files[] = $files_manager->get_link_files($file_id);
    }

    return $files;
}

/**
 * Retrieves categories by their IDs.
 *
 * This function gets an instance of the `WDLB_Categories` class and uses it to retrieve categories by their IDs.
 * The IDs are provided as a comma-separated string, which is then exploded into an array.
 * The function iterates over the array of IDs, retrieving the corresponding category for each ID and adding it to the `$categories` array.
 * The function returns the `$categories` array, which contains the retrieved categories.
 *
 * @param string $categories_id A comma-separated string of category IDs.
 * @return array An array of categories corresponding to the provided IDs.
 */
function wdlb_get_categories_by_id($categories_id) {
    $categories_manager = WDLB_Categories::get_instance();
    $categories_id = explode(',', $categories_id);
    $categories = [];

    foreach ($categories_id as $category_id) {
        $categories[] = $categories_manager->get_category($category_id);
    }

    return $categories;
}

/**
 * Retrieves information about files and categories based on the provided data.
 *
 * This function uses the provided data to retrieve information about files and categories.
 * The data should be an associative array with 'files' and 'categories' keys, each containing a comma-separated string of IDs.
 * The function uses the `wdlb_get_files_by_id` and `wdlb_get_categories_by_id` functions to retrieve the files and categories, respectively.
 * The retrieved files and categories are returned in an associative array with 'file' and 'categories' keys.
 *
 * @param array $data An associative array with 'files' and 'categories' keys, each containing a comma-separated string of IDs.
 * @return array An array with 'file' and 'categories' keys, each containing the retrieved files or categories, respectively.
 */
function get_infos($data) {
    return [
        'file' => wdlb_get_files_by_id($data['files']),
        'categories' => wdlb_get_categories_by_id($data['categories'])
    ];
}

/**
 * Retrieves information about multiple sets of files and categories based on the provided data.
 *
 * This function iterates over the provided array of data sets, each of which should be an associative array with 'files' and 'categories' keys, each containing a comma-separated string of IDs.
 * For each data set, the function uses the `get_infos` function to retrieve information about the files and categories.
 * The retrieved information for each data set is added to the `$infos` array.
 * The function returns the `$infos` array, which contains the retrieved information for each data set.
 *
 * @param array $datas An array of associative arrays, each with 'files' and 'categories' keys, each containing a comma-separated string of IDs.
 * @return array An array of arrays, each with 'file' and 'categories' keys, each containing the retrieved files or categories for a data set, respectively.
 */
function get_all_infos($datas) {
    foreach ($datas as $data) {
        $infos[] = get_infos($data);
    }

    return $infos;
}