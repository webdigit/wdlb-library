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

	protected function _register_controls() {

		$this->start_controls_section(
			'wdlb_library_settings',
			[
				'label' => __( 'WDLB Library Settings', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'wd_lib_limit_dl',
			[
				'label' => __( 'Limitations', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => get_option( 'wd_lib_limit_dl', '0' ),
			]
		);

		$this->add_control(
			'wd_lib_active_search',
			[
				'label' => __( 'Enable Search', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => get_option( 'wd_lib_active_search', 'on' ) === 'on' ? 'yes' : '',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if (isset($settings['wd_lib_limit_dl'])) {
			update_option('wd_lib_limit_dl', $settings['wd_lib_limit_dl']);
		}

		if (isset($settings['wd_lib_active_search'])) {
			update_option('wd_lib_active_search', $settings['wd_lib_active_search'] === 'yes' ? 'on' : '');
		}

		echo do_shortcode('[wdlb_library]');
	}
}