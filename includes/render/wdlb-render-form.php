<?php

/**
 * Renders the form.
 *
 * This function constructs the HTML content for the form. It creates a wrapper div with the ID 'wdlb-form-wrapper',
 * and within this wrapper, it calls the `wdlb_create_form` function to generate the form content.
 * It also adds a div with the ID 'wdlb-form-fade-screen' within the wrapper.
 * The function returns the constructed HTML content.
 *
 * @return string The HTML content for the form.
 */
function wdlb_render_form () {
    $content_form  = '<div id="wdlb-form-wrapper">';
    $content_form .= wdlb_create_form();
    $content_form .= '<div id="wdlb-form-fade-screen"></div>';
    $content_form .= '</div>';

    return $content_form;
}

/**
 * Creates the form.
 *
 * This function constructs the HTML content for the form. It first checks if the form has been submitted,
 * and if so, it calls the `wdlb_manage_submited_form` function to handle the submitted form data.
 * The form includes fields for the user's email, name, surname, and phone number, as well as a hidden field for the file IDs.
 * It also includes a checkbox for the user to accept the GDPR terms.
 * The function returns the constructed HTML content.
 *
 * @return string The HTML content for the form.
 */
function wdlb_create_form () {
    $form = '<div id="wdlb-form-popup">';
    $form .= '<form id="wdlb-requested-form" class="form" method="post">';
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

/**
 * Handles the submitted form data.
 *
 * This function retrieves the submitted form data from the $_POST superglobal and the file IDs from the form data.
 * It then decodes the file IDs from a JSON string to an array and retrieves information about the files.
 *
 * The function then iterates over the array of file data, sending an email for each file and storing the status of each email in the $userStatus array.
 *
 * If any of the emails failed to send (indicated by a false value in the $userStatus array), the function echoes a JSON-encoded error message and exits the script.
 * If all of the emails were sent successfully, the function echoes a JSON-encoded success message and exits the script.
 */
function wdlb_manage_submited_form () {
	$form_data = $_POST;
    $files_id = $form_data['wdlb_files_id'];

    $files_data = json_decode(stripslashes($files_id), true);
    $files_data = get_all_infos($files_data);

	$userStatus = [];

    foreach ($files_data as $data) {
        $userStatus[] = wdlb_send_email($form_data, $data);
    }

	if (in_array(false, $userStatus)) {
		echo json_encode(['status' => 'error', 'message' => 'Une erreur est survenue lors de l\'envoi de l\'email. Veuillez réessayer.'] );
	} else {
		echo json_encode(['status' => 'success', 'message' => 'Votre demande a bien été prise en compte. Vous allez recevoir un email avec les ressources demandées.'] );
	}

	exit;
}

/**
 * Sends an email based on the provided form data and file data.
 *
 * This function takes two parameters: an associative array of form data and an associative array of file data.
 * It first calls the `wdlb_prepare_email` function to prepare and send the email based on the form data and file data.
 * It then inserts statistics based on the form data and file data using the `wdlb_insert_stats` function.
 * The function returns the status of the email sent to the user.
 *
 * @param array $form_data An associative array of form data.
 * @param array $files_data An associative array of file data.
 * @return bool The status of the email sent to the user.
 */
function wdlb_send_email ($form_data, $files_data) {
    $userMailStatus = wdlb_prepare_email($form_data, $files_data);
    wdlb_insert_stats($form_data, $files_data);

	return $userMailStatus;
}

/**
 * Inserts statistics based on the provided form data and file data.
 *
 * This function takes two parameters: an associative array of form data and an associative array of file data.
 * It first gets an instance of the `WDLB_Stats` class, which is used to manage statistics.
 * It then calls the `insert_stats()` method on the `WDLB_Stats` instance, passing the form data and file data as arguments.
 * The `insert_stats()` method inserts statistics based on the form data and file data.
 *
 * @param array $form_data An associative array of form data.
 * @param array $files_data An associative array of file data.
 */
function wdlb_insert_stats ($form_data, $files_data) {
    $stats_manager = WDLB_Stats::get_instance();
    $stats_manager->insert_stats($form_data, $files_data);
}

/**
 * Prepares and sends an email based on the provided form data and file data.
 *
 * This function takes two parameters: an associative array of form data and an associative array of file data.
 * It first retrieves the email message using the `wdlb_get_mail_message` function.
 * It then constructs the email content for the user using the `wdlb_construct_mail_content_user` function.
 * It constructs the email content for the requested files using the `wdlb_construct_mail_content_files` function.
 * It constructs the email content for the requested categories using the `wdlb_construct_mail_content_categories` function.
 * The function then sends an email to the admin based on the message, user data, file content, and category content using the `wdlb_send_admin_email` function.
 * It sends an email to the user based on the message, user data, and file content using the `wdlb_send_user_email` function.
 * The function returns the status of the email sent to the user.
 *
 * @param array $form_data An associative array of form data.
 * @param array $files_data An associative array of file data.
 * @return bool The status of the email sent to the user.
 */
function wdlb_prepare_email ($form_data, $files_data) {
	$message = wdlb_get_mail_message();

	$mail_user_data = wdlb_construct_mail_content_user($form_data);
	$requested_files_content = wdlb_construct_mail_content_files($files_data);

	$requested_categories_content = wdlb_construct_mail_content_categories($files_data);

	wdlb_send_admin_email($message, $mail_user_data, $requested_files_content, $requested_categories_content);
	$status = wdlb_send_user_email($message, $mail_user_data, $requested_files_content);

	return $status;
}

/**
 * Sends an email to the user based on the provided message, user data, and file content.
 *
 * This function takes three parameters: a string message, an associative array of user data, and an associative array of file content.
 * It constructs the email header using the `wdlb_construct_mail_header` function.
 * It replaces the '[wdlb_content_mail_user]' placeholder in the message with the user data content and file content.
 * It then sends an email to the user's email address using the `wp_mail()` function. The email includes the mail title, the modified message, the email header, and the document URL as an attachment.
 * The function returns the status of the email sent to the user.
 *
 * @param string $message The email message.
 * @param array $mail_user_data An associative array of user data. It should include a 'content' key containing the user data content.
 * @param array $requested_files_content An associative array of file content. It should include a 'content' key containing the file content and a 'document_url' key containing the document URL.
 * @return bool The status of the email sent to the user.
 */
function wdlb_send_user_email ($message, $mail_user_data, $requested_files_content) {
	$headers = wdlb_construct_mail_header();
	$message = str_replace('[wdlb_content_mail_user]', $mail_user_data['content'] . $requested_files_content['content'], $message);
	$attachments = $requested_files_content['document_url'];

	$mailstatus = wp_mail($mail_user_data['email'], wdlb_get_mail_title(), $message, $headers, $attachments);

	return $mailstatus;
}

/**
 * Sends an email to the admin based on the provided message, user data, file content, and category content.
 *
 * This function takes four parameters: a string message, an associative array of user data, an associative array of file content, and an associative array of category content.
 * It first retrieves the email links from the category content and assigns them to the `$emails_to` variable.
 * It then calls the `wdlb_construct_mail_header()` function to construct the email header, passing the `$emails_to` variable as an argument.
 * It replaces the '[wdlb_content_mail_user]' placeholder in the message with the user data content, file content, and category content.
 * It then sends an email to the admin's email address using the `wp_mail()` function. The email includes the mail title, the modified message, and the email header.
 *
 * @param string $message The email message.
 * @param array $mail_user_data An associative array of user data. It should include a 'content' key containing the user data content.
 * @param array $requested_files_content An associative array of file content. It should include a 'content' key containing the file content.
 * @param array $requested_categories_content An associative array of category content. It should include a 'content' key containing the category content and an 'emails' key containing the email links.
 */
function wdlb_send_admin_email ($message, $mail_user_data, $requested_files_content, $requested_categories_content) {
	$emails_to = $requested_categories_content['emails'];
	$headers = wdlb_construct_mail_header($emails_to);
	$message = str_replace('[wdlb_content_mail_user]', $mail_user_data['content'] . $requested_files_content['content'] . $requested_categories_content['content'], $message);

	wp_mail(wdlb_get_admin_email(), wdlb_get_mail_title(), $message, $headers);
}

/**
 * Constructs the email header based on the provided email links.
 *
 * This function takes an array of email links as a parameter. If the array is not empty, it adds a 'CC' header with the email links to the email header.
 * The email header also includes a 'From' header with the sender's email and a 'Content-Type' header with 'text/html; charset=UTF-8'.
 * The function returns the constructed email header.
 *
 * @param array $emails_to An array of email links. Default is false.
 * @return string The constructed email header.
 */
function wdlb_construct_mail_header ($emails_to = false) {
	$mail_header = 'From: ' . wdlb_get_sender_email() . "\r\n";
	if ($emails_to) {
		$mail_header .= 'CC: ' . implode(',', $emails_to) . "\r\n";
	}
	$mail_header .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";

	return $mail_header;
}

/**
 * Constructs the content of the email related to the user.
 *
 * This function takes an array of form data, extracts the user's email, name, surname, and phone number, and constructs a string of HTML content
 * that lists the user's details. It also collects the user's email.
 *
 * The function returns an associative array with two keys:
 * - 'content': A string of HTML content that lists the user's details.
 * - 'email': The user's email.
 *
 * @param array $form_data An associative array containing form data, including 'wdlb_email', 'wdlb_name', 'wdlb_surname', and 'wdlb_phone' keys.
 * @return array An associative array with 'content' and 'email' keys.
 */
function wdlb_construct_mail_content_user ($form_data) {
	$email = $form_data['wdlb_email'];
	$name = $form_data['wdlb_name'];
	$surname = $form_data['wdlb_surname'];
	$phone = $form_data['wdlb_phone'];

	$result = [];

	$content = '<br /><b>Nom : </b>' . $name . "<br />";
	$content .= '<b>Prénom : </b>' . $surname . "<br />";
	$content .= '<b>Email : </b>' . $email . "<br />";
	$content .= '<b>Téléphone : </b>' . $phone . "<br /><br />";

	$result['content'] = $content;
	$result['email'] = $email;

	return $result;
}

/**
 * Constructs the content of the email related to the files.
 *
 * This function takes an array of file data, extracts the files, and constructs a string of HTML content
 * that lists the files. It also collects the document URLs and links associated with each file.
 *
 * The function returns an associative array with three keys:
 * - 'content': A string of HTML content that lists the files.
 * - 'document_url': An array of document URLs associated with each file.
 * - 'link': An array of links associated with each file.
 *
 * @param array $files_data An associative array containing file data, including 'file' key which is an array of files.
 * @return array An associative array with 'content', 'document_url' and 'link' keys.
 */
function wdlb_construct_mail_content_files ($files_data) {
	$files = $files_data['file'];
	$result = [];

	$content = '<b>Ressources : </b><br />';
	$content .= '<ul>';
	foreach ($files as $file) {
		$content .= "<li>";
		if ($file->link) {
			$content .= "<a href='" . $file->link . "'>" . $file->name . "</a>";
		} else {
			$content .= $file->name;
		}
		$content .= "</li>";
		$result['document_url'][] = wdlb_get_file_path($file->document_url);
		$result['link'][] = $file->link;
	}
	$content .= '</ul>';
	$result['content'] = $content;

	return $result;
}

/**
 * Retrieves the file path of a document based on its URL.
 *
 * This function uses the `attachment_url_to_postid()` function to get the ID of the attachment (document) based on its URL.
 * It then uses the `get_attached_file()` function to get the file path of the attachment based on its ID.
 * The function returns the file path of the attachment.
 *
 * @param string $document_url The URL of the document.
 * @return string The file path of the document.
 */
function wdlb_get_file_path ($document_url) {
	$attachment_id = attachment_url_to_postid($document_url);

	return get_attached_file($attachment_id);
}

/**
 * Constructs the content of the email related to the categories.
 *
 * This function takes an array of file data, extracts the categories, and constructs a string of HTML content
 * that lists the categories. It also collects the email links associated with each category.
 *
 * The function returns an associative array with two keys:
 * - 'content': A string of HTML content that lists the categories.
 * - 'emails': An array of email links associated with each category.
 *
 * @param array $files_data An associative array containing file data, including 'categories' key which is an array of categories.
 * @return array An associative array with 'content' and 'emails' keys.
 */
function wdlb_construct_mail_content_categories ($files_data) {
	$categories = $files_data['categories'];
	$result = [];

	$content = '<b>Catégories : </b><br />';
	$content .= '<ul>';
	foreach ($categories as $category) {
		$content .= "<li>" . $category->category_name . "</li>";
		$result['emails'][] = $category->email_link;
	}
	$content .= '</ul>';
	$result['content'] = $content;

	return $result;
}

/**
 * Retrieves the admin email from the settings.
 *
 * This function gets an instance of the `WDLB_Settings` class and uses it to retrieve the admin email from the settings.
 * The admin email is identified by the 'wd_lib_admin_mails' key.
 * The function returns the admin email.
 *
 * @return string The admin email.
 */
function wdlb_get_admin_email() {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_settings('wd_lib_admin_mails');
}

/**
 * Retrieves the mail title from the settings.
 *
 * This function gets an instance of the `WDLB_Settings` class and uses it to retrieve the mail title from the settings.
 * The mail title is identified by the 'wd_lib_mail_title' key.
 * The function returns the mail title.
 *
 * @return string The mail title.
 */
function wdlb_get_mail_title() {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_settings('wd_lib_mail_title');
}

/**
 * Retrieves the mail message from the settings.
 *
 * This function gets an instance of the `WDLB_Settings` class and uses it to retrieve the mail message from the settings.
 * The mail message is identified by the 'wd_lib_mail_message' key.
 * The function returns the mail message.
 *
 * @return string The mail message.
 */
function wdlb_get_mail_message () {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_settings('wd_lib_mail_message');
}

/**
 * Retrieves the sender email from the settings.
 *
 * This function gets an instance of the `WDLB_Settings` class and uses it to retrieve the sender email from the settings.
 * The sender email is identified by the 'wd_lib_sender_mails' key.
 * The function returns the sender email.
 *
 * @return string The sender email.
 */
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