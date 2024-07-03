<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function wdlb_add_elementor_widget(){

 require_once( plugin_dir_path( __FILE__ ) . 'wdlb-library-elementor-widget.php' );

 \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \WDLB_Library_Elementor_Widget() );
}
add_action( 'init', 'wdlb_add_elementor_widget' );

function wdlb_add_elementor_widget_category( $elements_manager ) {
	$elements_manager->add_category(
		'wdlb-library',
		[
			'title' => __( 'Library', 'plugin-name' ),
			'icon' => 'fa fa-book',
		]
	);
}
add_action( 'elementor/elements/categories_registered', 'wdlb_add_elementor_widget_category' );

function enqueue_elementor_widget_script() {
	if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
		wp_enqueue_script( 'wdlb-library-elementor-widget', WD_LIBRARY_URL . '/js/wdlb-elementor-widget.js', [], '1.0.0', true );
		$nonce = wp_create_nonce( 'wdlb_ajax_nonce' );
		$script = 'const wdlb_ajax_nonce = ' . wp_json_encode( $nonce ) . ';';
		wp_add_inline_script( 'wdlb-library-elementor-widget', $script, 'before' );
	}
}
add_action( 'elementor/editor/before_enqueue_scripts', 'enqueue_elementor_widget_script' );