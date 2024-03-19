<?php

/**
 * Retrieves the filter for the library.
 *
 * This function retrieves the categories from the WDLB_Categories class
 * and creates a filter based on those categories.
 *
 * @return mixed The filter if categories exist, false otherwise.
 */
function wdlb_get_filter() {
    $categories_manager = WDLB_Categories::get_instance();
    $categories = $categories_manager->get_categories();

    if(!count($categories)) {
        return false;
    }

    return wdlb_create_filter($categories);
}

/**
 * Creates a filter section with checkboxes for each category.
 *
 * @param array $categories An array of category objects.
 * @return string The HTML markup for the filter section.
 */
function wdlb_create_filter($categories) {
    $filter = '<section class="wdlb-filter">';
    foreach($categories as $category) {
        $filter .= '<div class="wdlb-filter-items"><input type="checkbox" name="';
        $filter .= $category->category_name;
        $filter .= '" value="';
        $filter .= $category->category_name;
        $filter .= '"><label for="';
        $filter .= $category->category_name;
        $filter .= '">';
        $filter .= wdlb_create_category_icon($category) . $category->category_name;
        $filter .= '</label></div>';
    }
    $filter .= '</section>';

    return $filter;
}