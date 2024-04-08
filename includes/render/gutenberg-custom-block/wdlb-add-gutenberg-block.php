<?php
function wdlb_library_block_init() {
	wp_register_script(
		'wdlb-library-block-script',
		plugins_url( 'blocks/wdlb-gutenberg-block-render.js', __FILE__ ),
		array( 'wp-blocks', 'wp-editor', 'wp-i18n', 'wp-element' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'blocks/wdlb-gutenberg-block-render.js' )
	);

	register_block_type( 'wdlb-library/wdlb-library', array(
		'editor_script' => 'wdlb-library-block-script',
	) );
}

add_action( 'init', 'wdlb_library_block_init' );