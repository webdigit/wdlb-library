<?php

/**
 * Retrieves the search results based on the active search settings.
 *
 * @return mixed|false The search results if active search is enabled, false otherwise.
 */
function wdlb_get_search() {
    $settings_manager = WDLB_Settings::get_instance();
    if (!$settings_manager->get_settings('wd_lib_active_search')) {
        return false;
    }
    return wdlb_create_search();
}

/**
 * Creates a search input field for the library.
 *
 * @return string The HTML markup for the search input field.
 */
function wdlb_create_search() {
    $search = '<section class="wdlb-search">';
    $search .= '<input type="text" id="wdlb-search" placeholder="' . __('Search...', 'webdigit-library') . '">';
    $search .= '</section>';

    return $search;
}