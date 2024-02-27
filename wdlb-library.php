<?php
/*
Plugin Name: Webdigit - Library
Description: Permet de partager des documents 
Version: 1.0
Author: Webdigit
Author URI: https://www.webdigit.be
Text Domain: webdigit-library
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/class-wdlb-library-initializer.php';

function wdlb_library() {
    static $instance;
    if ( null === $instance || ($instance instanceof WDLB_Library_Initializer) ) {
        $instance = WDLB_Library_Initializer::instance();
    }

    return $instance;
}

$wdlb_instance = wdlb_library();

register_activation_hook( __FILE__, array( $wdlb_instance, 'activate' ) );
register_deactivation_hook( __FILE__, array( $wdlb_instance, 'deactivate' ) );
