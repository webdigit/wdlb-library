<?php

/**
 * Renders the library content.
 *
 * This function checks if the user has access to the library content. If not, it returns an error message.
 * Otherwise, it generates the HTML markup for the library section, including any filters, search functionality, and the library content itself.
 *
 * @return string The HTML markup for the library section.
 */
function wdlb_render_library () {
    if(!wdlb_get_library_acces()) {
        return __('Sorry you have no access to this content.', 'webdigit-library');
    }

    $render = '<div id="wdlb-library-wrapper">';
    $render .= wdlb_get_library_header();
    $render .= '<div id="wdlb-library-nav-wrapper">';
    $render .= '<section id="wdlb-sidebar">';
    $render .= wdlb_get_search();
    $render .= wdlb_get_filter();
    $render .= '</section>';
    $render .= '<section id="wdlb-content">';
    $render .= wdlb_get_library_content();
    $render .= '</section>';
    $render .= '</div>';
    $render .= wdlb_render_form();
    $render .= '</div>';

    return $render;
}
add_shortcode('wdlb_library', 'wdlb_render_library');

function wdlb_get_library_header() {
    $header = '<div id="wdlb-header">';
	$header .= wdlb_get_request_notification();
    $header .= '<div id="wdlb-header-infos">';
    $header .= wdlb_create_limation_info(wdlb_get_limitation());
    $header .= wdlb_create_request_validation_button();
    $header .= '</div>';
    $header .= '</div>';

    return $header;
}

/**
 * Generates the HTML markup for a notification element.
 *
 * This function creates a notification element that can be used to display messages to the user.
 * The notification element is a div with the ID 'wdlb-notification-wrapper' and the class 'wdlb-notification-request'.
 * Inside this div, there is another div with the class 'wdlb-notification', which contains two span elements:
 * - The first span element has the ID 'wdlb-notification-msg' and will contain the notification message.
 * - The second span element has the ID 'wdlb-notification-close' and the class 'wdlb-notification-close', and can be used to close the notification.
 *
 * @return string The HTML markup for the notification element.
 */
function wdlb_get_request_notification () {
    $notification = '<div id="wdlb-notification-wrapper" class="wdlb-notification-request">';
    $notification .= '<div class="wdlb-notification">';
    $notification .= '<span id="wdlb-notification-msg"></span>';
    $notification .= '<span id="wdlb-notification-close" class="wdlb-notification-close"></span>';
    $notification .= '</div>';
    $notification .= '</div>';

    return $notification;
}


/**
 * Creates the limitation information HTML element.
 *
 * This function takes a limitation value as an argument, which should be a numeric value representing the maximum number of files that can be downloaded per request.
 *
 * If the limitation value is not set or is false, the function returns an empty string.
 * Otherwise, it creates a div element with the ID 'wdlb-limitation-wrapper', which contains two span elements:
 * - The first span element has the ID 'wdlb-limitation-info' and contains a localized string indicating the maximum number of files that can be downloaded per request.
 * - The second span element has the ID 'wdlb-limitation-max-msg' and contains a localized string indicating that the maximum number of files per request has been reached.
 *
 * @param int|false $limitation The limitation value.
 * @return string The HTML string for the limitation information element.
 */
function wdlb_create_limation_info($limitation) {
    if (!$limitation) {
        return '';
    }

    $limitation_info = '<div id="wdlb-limitation-wrapper">';
    $limitation_info .= '<span id="wdlb-limitation-info">' . __('You can download', 'webdigit-library') . ' ' . $limitation . ' ' . __('files per request', 'webdigit-library') . '</span>';
    $limitation_info .= '<span id="wdlb-limitation-max-msg">' . __('You have reached the maximum number of files per request', 'webdigit-library') . '</span>';
    $limitation_info .= '</div>';

    return $limitation_info;
}

/**
 * Retrieves the search form.
 *
 * This function generates the HTML markup for the search form.
 *
 * @return string The HTML markup for the search form.
 */
function wdlb_create_request_validation_button() {
    $button = '<div id="wdlb-request-wrapper">';
    $button .= '<button id="wdlb-confirmation-request">'. __('Request', 'webdigit-library') .' '.'<span id="wdlb-count-item">0</span>'. ' ' . __('items', 'webdigit-library') .'</button>';
    $button .= '';
    $button .= '</div>';

    return $button;
}

/**
 * Retrieves the library content.
 *
 * This function retrieves the content of the library by calling the `get_all_link_files` method of the `WDLB_Linkfiles` class.
 * If no content is found, it returns the string 'No Content Found'.
 * Otherwise, it calls the `create_content` function to create the content and returns it.
 *
 * @return string The library content.
 */
function wdlb_get_library_content () {
    $library_manager = WDLB_Linkfiles::get_instance();
    $contents = $library_manager->get_all_link_files();

    if(!count($contents)) {
        return _e('No content Found', 'webdigit-library');
    }

    return wdlb_create_content($contents);
}

/**
 * Creates the content for rendering the library.
 *
 * @param array $contents The array of content items.
 * @return string The generated content.
 */
function wdlb_create_content($contents) {
    $content = '';

    foreach($contents as $file) {
        $categories = wdlb_get_all_categories_for_content(unserialize($file->category_id));

        $content .= wdlb_add_content_background($file);

        $content .= '<article class="wdlb-content-item wd-item-' . $file->id . '" data-category="' . wdlb_get_all_categories_name($categories) .'">';
        $content .= '<div class="wdlb-content-item-name">' . $file->name . '</div>';

        if ($categories) {
            $content .= wdlb_create_categorie_tag($categories);
        }

        $content .= '<div class="wdlb-content-item-description">' . $file->desc_text . '</div>';

        $content .= wdlb_create_request_button($file);

        $content .= '</article>';
    }

    return $content;
}


/**
 * Creates a request button for a file.
 *
 * This function takes a file object as an argument, which should contain an 'id' property and a 'category_id' property.
 * The 'category_id' property should be a serialized array of category IDs.
 *
 * The function first unserializes the 'category_id' property and retrieves the corresponding categories.
 * It then creates a button element with the following data attributes:
 * - 'data-category': a string of comma-separated category names
 * - 'data-id': the ID of the file
 *
 * Inside the button, it creates a span element with the following data attributes:
 * - 'data-categories_id': a string of comma-separated category IDs
 * - 'data-file_id': the ID of the file
 *
 * The span element also contains the localized string 'Add to request'.
 *
 * @param object $file The file object.
 * @return string The HTML string for the request button.
 */
function wdlb_create_request_button($file) {
    $categories = wdlb_get_all_categories_for_content(unserialize($file->category_id));

    $button = '<button class="wdlb-request-button" data-category="' . wdlb_get_all_categories_name($categories) .'" data-id="' . $file->id . '">';
    $button .='<span class="wdlb-checked-item" data-categories_id="' . wdlb_get_all_categories_id($categories) . '" data-file_id="' . $file->id . '"></span>' . __('Add to request', 'webdigit-library') . '</button>';

    return $button;
}

/**
 * Adds a background style with an image to the content.
 *
 * @param object $file The file object containing the image URL.
 * @return string The generated style content.
 */
function wdlb_add_content_background($file) {
    $content = '<style>';
    if ($file->img_couv) {
        $content .= '.wd-item-' . $file->id . '::before {';
        $content .= 'content: "";';
        $content .= 'position: absolute;';
        $content .= 'top: 0;';
        $content .= 'bottom: 0;';
        $content .= 'right: 0;';
        $content .= 'left: 0;';
        $content .= 'background-size: cover;';
        $content .= 'background-image: url(\'' . $file->img_couv . '\');';
        $content .= 'opacity: 0.10;';
        $content .= 'z-index: -1;';
    } else {
        $content .= '.wd-item-' . $file->id . '{';
        $content .= 'background-color: #fff;';
    }
    $content .= '}';
    $content .= '</style>';

    return $content;
}

/**
 * Retrieves the names of all categories.
 *
 * @param array|false $categories The array of category objects.
 * @return string|false The comma-separated string of category names, or false if $categories is false or not set.
 */
function wdlb_get_all_categories_name($categories) {
    if ($categories === false || !isset($categories)) {
        return false;
    }

    $category_names = array();
    foreach($categories as $category) {
        $category_names[] = $category->category_name;
    }

    return implode(',', $category_names);
}

/**
 * Retrieves all category IDs from the given array of category objects.
 *
 * This function iterates over the given array of category objects and collects their IDs.
 * If the input array is not set or is false, the function returns false.
 * Otherwise, it returns a string of comma-separated category IDs.
 *
 * @param array|false $categories The array of category objects.
 * @return string|false The comma-separated string of category IDs, or false if $categories is false or not set.
 */
function wdlb_get_all_categories_id($categories) {
    if ($categories === false || !isset($categories)) {
        return false;
    }

    $category_ids = array();
    foreach($categories as $category) {
        $category_ids[] = $category->id;
    }

    return implode(',', $category_ids);
}

/**
 * Retrieves all categories for the given category IDs.
 *
 * @param array $categories_id An array of category IDs.
 * @return array|false An array of category objects or false if the input is invalid.
 */
function wdlb_get_all_categories_for_content($categories_id) {
    if ($categories_id === false || !isset($categories_id)) {
        return false;
    }

    $categories_manager = WDLB_Categories::get_instance();
    foreach($categories_id as $category_id) {
        $categories[] = $categories_manager->get_category($category_id);
    }

    return $categories;
}

/**
 * Creates a HTML tag for displaying categories.
 *
 * @param array $categories An array of category objects.
 * @return string The HTML tag containing the categories.
 */
function wdlb_create_categorie_tag($categories) {
    $tag = '<div class="wdlb-content-item-categories">';
    foreach($categories as $category) {
        $tag .= '<span class="category-tags">';
        $tag .= wdlb_create_category_icon($category);
        $tag .= '<span class="wd-cat-name">' . $category->category_name . '</span>';
        $tag .= '</span>';
    }
    $tag .= '</div>';

    return $tag;
}

/**
 * Creates a category icon HTML element.
 *
 * @param object $category The category object.
 * @return string The HTML code for the category icon.
 */
function wdlb_create_category_icon($category) {
    if (!$category->image_url) {
        return '';
    }

    return '<img class="wd-tags-icon" src="' . $category->image_url . '" alt="icon-for-' . $category->category_name . '">';
}

/**
 * Retrieves the library access level.
 *
 * This function calls the `get_acces()` method of the `WDLB_Settings` class
 * to retrieve the library access level.
 *
 * @return string The library access level.
 */
function wdlb_get_library_acces() {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_acces();
}

/**
 * Retrieves the limitation settings for the library.
 *
 * This function calls the `get_settings` method of the `WDLB_Settings` class with the argument 'wd_lib_limit_dl'.
 * The 'wd_lib_limit_dl' setting represents the limitation on the number of files that can be downloaded from the library.
 *
 * @return mixed The value of the 'wd_lib_limit_dl' setting.
 */
function wdlb_get_limitation() {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_settings('wd_lib_limit_dl');
}
