<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Menu Anchor Widget
 */
class Widget_Menu_Anchor extends Widget_Base {

	/**
	 * Retrieve menu anchor widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'menu-anchor';
	}

	/**
	 * Retrieve menu anchor widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Menu Anchor', 'elementor' );
	}

	/**
	 * Retrieve menu anchor widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-anchor';
	}

	/**
	 * Retrieve the list of categories the menu anchor widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general-elements' ];
	}

	/**
	 * Register menu anchor widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_anchor',
			[
				'label' => __( 'Anchor', 'elementor' ),
			]
		);

		$this->add_control(
			'anchor_description',
			[
				'raw' => __( 'This ID will be the CSS ID you will have to use in your own page, Without #.', 'elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'anchor',
			[
				'label' => __( 'The ID of Menu Anchor.', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'For Example: About', 'elementor' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render menu anchor widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$anchor = $this->get_settings( 'anchor' );

		if ( ! empty( $anchor ) ) {
			$this->add_render_attribute( 'inner', 'id', $anchor );
		}

		$this->add_render_attribute( 'inner', 'class', 'elementor-menu-anchor' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'inner' ); ?>></div>
		<?php
	}

	/**
	 * Render menu anchor widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<div class="elementor-menu-anchor"{{{ settings.anchor ? ' id="' + settings.anchor + '"' : '' }}}></div>
		<?php
	}
}
