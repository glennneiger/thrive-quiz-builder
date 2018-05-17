<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class TCB_Button_Element
 */
class TCB_Button_Element extends TCB_Element_Abstract {

	/**
	 * Get element alternate
	 *
	 * @return string
	 */
	public function alternate() {
		return 'button';
	}

	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Button', 'thrive-cb' );
	}

	/**
	 * Return icon class needed for display in menu
	 *
	 * @return string
	 */
	public function icon() {
		return 'button';
	}

	/**
	 * Button element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.thrv-button, .thrv_button_shortcode';
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		return array(
			'button'     => array(
				'config' => array(
					'icon_side'     => array(
						'config' => array(
							'name'    => __( 'Icon Side', 'thrive-cb' ),
							'buttons' => array(
								array( 'value' => 'left', 'text' => __( 'Left', 'thrive-cb' ), 'default' => true ),
								array( 'value' => 'right', 'text' => __( 'Right', 'thrive-cb' ) ),
							),
						),
					),
					'ButtonLink'    => array(
						'config'  => array(
							'label'       => __( 'Button Link', 'thrive-cb' ),
							'extra_attrs' => '',
							'label_col_x' => 12,
							'placeholder' => __( 'Insert linking URL', 'thrive-cb' ),
						),
						'extends' => 'LabelInput',
					),
					'LinkNewTab'    => array(
						'config'  => array(
							'name'    => '',
							'label'   => __( 'Open in new tab', 'thrive-cb' ),
							'default' => false,
						),
						'extends' => 'Checkbox',
					),
					'LinkNoFollow'  => array(
						'config'  => array(
							'name'    => '',
							'label'   => __( 'No Follow', 'thrive-cb' ),
							'default' => false,
						),
						'extends' => 'Checkbox',
					),
					'MasterColor'   => array(
						'show_empty_colors' => true,
						'config'            => array(
							'default'             => '000',
							'label'               => __( 'Master Color', 'thrive-cb' ),
							'important'           => true,
							'affected_components' => array( 'shadow', 'background', 'borders' ),
						),
					),
					'ButtonIcon'    => array(
						'config'  => array(
							'name'    => '',
							'label'   => __( 'Add Icon', 'thrive-cb' ),
							'default' => false,
						),
						'extends' => 'Checkbox',
					),
					'SecondaryText' => array(
						'config'  => array(
							'name'    => '',
							'label'   => __( 'Secondary button text', 'thrive-cb' ),
							'default' => false,
						),
						'to'      => '.tcb-button-texts',
						'extends' => 'Checkbox',
					),
					'ButtonSize'    => array(
						'css_prefix' => '#tve_editor ',
						'config'     => array(
							'name'    => __( 'Size', 'thrive-cb' ),
							'buttons' => array(
								array(
									'value'      => 's',
									'properties' => array(
										'padding'     => '12px 15px',
										'font-size'   => '18px',
										'line-height' => '1.2em'
									),
									'text'       => 'S',
									'default'    => true
								),
								array(
									'value'      => 'm',
									'properties' => array(
										'padding'     => '14px 22px',
										'font-size'   => '24px',
										'line-height' => '1.2em'
									),
									'text'       => 'M'
								),
								array(
									'value'      => 'l',
									'properties' => array(
										'padding'     => '18px 30px',
										'font-size'   => '32px',
										'line-height' => '1.2em'
									),
									'text'       => 'L'
								),
								array(
									'value'      => 'xl',
									'properties' => array(
										'padding'     => '22px 40px',
										'font-size'   => '38px',
										'line-height' => '1.1em'
									),
									'text'       => 'XL'
								),
								array(
									'value'      => 'xxl',
									'properties' => array(
										'padding'     => '32px 50px',
										'font-size'   => '52px',
										'line-height' => '1.1em'
									),
									'text'       => 'XXL'
								),
							),
						),
					),
					'ButtonWidth'   => array(
						'config'  => array(
							'default' => '0',
							'min'     => '10',
							'max'     => '1080',
							'label'   => __( 'Button width', 'thrive-cb' ),
							'um'      => array( '%', 'px' ),
							'css'     => 'max-width',
						),
						'extends' => 'Slider',
					),
					'FullWidth'     => array(
						'config'  => array(
							'name'    => '',
							'label'   => __( 'Full Width', 'thrive-cb' ),
							'default' => false,
						),
						'extends' => 'Checkbox',
					),
					'style'         => array(
						'css_suffix' => ' .tcb-button-link',
						'config'     => array(
							'label'         => __( 'Style', 'thrive-cb' ),
							'items'         => array(
								'default'      => __( 'Default', 'thrive-cb' ),
								'ghost'        => __( 'Ghost', 'thrive-cb' ),
								'rounded'      => __( 'Rounded', 'thrive-cb' ),
								'full_rounded' => __( 'Full Rounded', 'thrive-cb' ),
								'gradient'     => __( 'Gradient', 'thrive-cb' ),
								'elevated'     => __( 'Elevated', 'thrive-cb' ),
								'border_1'     => __( 'Border 1', 'thrive-cb' ),
								'border_2'     => __( 'Border 2', 'thrive-cb' ),
							),
							'default_label' => __( 'Template Button', 'thrive-cb' ),
							'default'       => 'default',
						),
					),
				),
			),
			'animation'  => array(
				'config' => array(
					'to' => '.tcb-button-link',
				),
			),
			'background' => array(
				'config' => array(
					'css_suffix'  => ' .tcb-button-link',
					'ColorPicker' => array(
						'show_empty_colors' => true,
					),
				),
			),
			'borders'    => array(
				'config' => array(
					'css_suffix' => ' .tcb-button-link',
				),
			),
			'typography' => array(
				'config' => array(
					'css_suffix' => ' .tcb-button-link',
					'FontColor'  => array(
						'css_suffix' => ' .tcb-button-link span',
					),
					'FontSize'   => array(
						'css_suffix' => ' .tcb-button-link',
						'important'  => true,
					),
					'TextStyle'  => array(
						'css_suffix' => ' .tcb-button-link span',
					),
					'LineHeight' => array(
						'css_suffix' => ' .tcb-button-link',
					),
					'FontFace'   => array(
						'css_suffix' => ' .tcb-button-link',
					),
				),
			),
			'shadow'     => array(
				'config' => array(
					'css_suffix'     => ' .tcb-button-link',
					'default_shadow' => 'none',
				),
			),
			'layout'     => array(
				'disabled_controls' => array(
					'MaxWidth',
				),
				'config'            => array(
					'to'               => '.tcb-button-link',
					'Alignment'        => array(
						'to' => '',
					),
					'Float'            => array(
						'to' => '',
					),
					'MarginAndPadding' => array(
						'to'             => '',
						'padding_suffix' => ' .tcb-button-link',
					),
					'Position'         => array(
						'to' => '',
					),
					'PositionFrom'     => array(
						'to' => '',
					),
				),
			),
		);
	}

	/**
	 * @return bool
	 */
	public function has_hover_state() {
		return true;
	}

	/**
	 * Element category that will be displayed in the sidebar
	 *
	 * @return string
	 */
	public function category() {
		return $this->get_thrive_basic_label();
	}
}
