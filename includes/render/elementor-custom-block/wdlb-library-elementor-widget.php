<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WDLB_Library_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wdlb-library';
	}

	public function get_title() {
		return __( 'WDLB Library', 'plugin-name' );
	}

	public function get_icon() {
		return 'eicon-archive-posts';
	}

	public function get_categories() {
		return [ 'wdlb-library' ];
	}

	protected function render() {
		echo do_shortcode('[wdlb_library]');
	}
}