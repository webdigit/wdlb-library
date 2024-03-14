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
    if(!get_library_acces()) {
        return __('Sorry you have no access to this content.', 'webdigit-library');
    }

    $render = '<div id="wdlb-library-wrapper">';
    $render .= get_library_header();
    $render .= '<div id="wdlb-library-nav-wrapper">';
    $render .= '<section id="wdlb-sidebar">';
    $render .= get_search();
    $render .= get_filter();
    $render .= '</section>';
    $render .= '<section id="wdlb-content">';
    $render .= get_library_content();
    $render .= '</section>';
    $render .= '</div>';
    $render .= '</div>';

    return $render;
}
add_shortcode('wdlb_library', 'wdlb_render_library');

function get_library_header() {
    $header = '<div id="wdlb-header">';
    $header .= '<div id="wdlb-header-infos">';
    $header .= create_limation_info(get_limitation());
    $header .= create_request_validation_button();
    $header .= '</div>';
    $header .= '</div>';

    return $header;
}

function create_limation_info($limitation) {
    if (!$limitation) {
        return '';
    }

    $limitation_info = '<div id="wdlb-limitation-wrapper">';
    $limitation_info .= '<span id="wdlb-limitation-info">' . __('You can download', 'webdigit-library') . ' ' . $limitation . ' ' . __('files per request', 'webdigit-library') . '</span>';
    $limitation_info .= '<span id="wdlb-limitation-max-msg">' . __('You have reached the maximum number of files per request', 'webdigit-library') . '</span>';
    $limitation_info .= '</div>';

    return $limitation_info;
}

function create_request_validation_button() {
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
function get_library_content () {
    $library_manager = WDLB_Linkfiles::get_instance();
    $contents = $library_manager->get_all_link_files();

    if(!count($contents)) {
        return 'No Content Found';
    }

    return create_content($contents);
}

/**
 * Creates the content for rendering the library.
 *
 * @param array $contents The array of content items.
 * @return string The generated content.
 */
function create_content($contents) {
    $content = '';

    foreach($contents as $file) {
        $categories = get_all_categories_for_content(unserialize($file->category_id));

        $content .= add_content_background($file);

        $content .= '<article class="wdlb-content-item wd-item-' . $file->id . '" data-category="' . get_all_categories_name($categories) .'">';
        $content .= '<div class="wdlb-content-item-name">' . $file->name . '</div>';

        if ($categories) {
            $content .= create_categorie_tag($categories);
        }

        $content .= '<div class="wdlb-content-item-description">' . $file->desc_text . '</div>';

        $content .= create_request_button($file);

        $content .= '</article>';
    }

    return $content;
}

function create_request_button($file) {
    $categories = get_all_categories_for_content(unserialize($file->category_id));

    $button = '<button class="wdlb-request-button" data-category="' . get_all_categories_name($categories) .'" data-id="' . $file->id . '"><span class="wdlb-checked-item"></span>' . __('Add to request', 'webdigit-library') . '</button>';

    return $button;
}

/**
 * Adds a background style with an image to the content.
 *
 * @param object $file The file object containing the image URL.
 * @return string The generated style content.
 */
function add_content_background($file) {
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
function get_all_categories_name($categories) {
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
 * Retrieves all categories for the given category IDs.
 *
 * @param array $categories_id An array of category IDs.
 * @return array|false An array of category objects or false if the input is invalid.
 */
function get_all_categories_for_content($categories_id) {
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
function create_categorie_tag($categories) {
    $tag = '<div class="wdlb-content-item-categories">';
    foreach($categories as $category) {
        $tag .= '<span class="category-tags">';
        $tag .= create_category_icon($category);
        $tag .= '<span class="wd-cat-name">' . $category->category_name . '</span>';
        $tag .= '</span>';
    }
    $tag .= '</div>';

    return $tag;
}

/**
 * Retrieves the filter for the library.
 *
 * This function retrieves the categories from the WDLB_Categories class
 * and creates a filter based on those categories.
 *
 * @return mixed The filter if categories exist, false otherwise.
 */
function get_filter() {
    $categories_manager = WDLB_Categories::get_instance();
    $categories = $categories_manager->get_categories();

    if(!count($categories)) {
        return false;
    }

    return create_filter($categories);
}

/**
 * Creates a filter section with checkboxes for each category.
 *
 * @param array $categories An array of category objects.
 * @return string The HTML markup for the filter section.
 */
function create_filter($categories) {
    $filter = '<section class="wdlb-filter">';
    foreach($categories as $category) {
        $filter .= '<div class="wdlb-filter-items"><input type="checkbox" name="' . $category->category_name . '" value="' . $category->category_name . '"><label for="' . $category->category_name . '">' . create_category_icon($category) . $category->category_name . '</label></div>';
    }
    $filter .= '</section>';

    return $filter;
}

/**
 * Creates a category icon HTML element.
 *
 * @param object $category The category object.
 * @return string The HTML code for the category icon.
 */
function create_category_icon($category) {
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
function get_library_acces() {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_acces();
}

/**
 * Retrieves the search results based on the active search settings.
 *
 * @return mixed|false The search results if active search is enabled, false otherwise.
 */
function get_search() {
    $settings_manager = WDLB_Settings::get_instance();
    if (!$settings_manager->get_settings('wd_lib_active_search')) {
        return false;
    }
    return create_search();
}

/**
 * Creates a search input field for the library.
 *
 * @return string The HTML markup for the search input field.
 */
function create_search() {
    $search = '<section class="wdlb-search">';
    $search .= '<input type="text" id="wdlb-search" placeholder="' . __('Search...', 'webdigit-library') . '">';
    $search .= '</section>';

    return $search;
}

function get_limitation() {
    $settings_manager = WDLB_Settings::get_instance();
    return $settings_manager->get_settings('wd_lib_limit_dl');
}
