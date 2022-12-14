<?php
/**
 * Registers the associted plugin settings with WordPress.
 */

/**
 * Registers the plugin settings with WordPress.
 */
function hd_ssi_register_settings() {

	// get the registered settings.
	$settings = hd_ssi_get_settings();

	// if we have no settings.
	if ( empty( $settings ) ) {
		return;
	}

	// set up default option args.
	$defaults = array(
		'label'             => '',
		'option_name'       => '',
		'input_type'        => 'text',
		'type'              => 'string',
		'description'       => '',
		'sanitize_callback' => null,
		'show_in_rest'      => false,
		'order'             => 10,
		'default_value'     => '',
		'custom_property'   => '',
	);

	// loop through each setting.
	foreach ( $settings as $setting ) {

		// merge the args with defaults.
		$args = wp_parse_args( $setting, $defaults );

		// if no setting key is set.
		if ( '' === $args['option_name'] ) {

			// don't register the setting.
			continue;

		}

		// if the setting type is a section.
		if ( 'section' === $args['input_type'] ) {

			// don't register the setting as it is not a setting!
			continue;

		}

		// register this setting.
		register_setting(
			'hd_ssi_settings', // setting group name.
			$args['option_name'], // setting name - the option key.
			array(
				'type'              => $args['type'],
				'group'             => 'hd_ssi_settings',
				'description'       => $args['description'],
				'sanitize_callback' => $args['sanitize_callback'],
				'show_in_rest'      => $args['show_in_rest'],
			)
		);

	}

}

add_action( 'admin_init', 'hd_ssi_register_settings' );

/**
 * Registers the plugins default settings.
 *
 * @param  array $settings The current array of settings.
 * @return array $settings The modified array of settings.
 */
function hd_ssi_register_default_settings( $settings ) {

	$settings['license_key'] = array(
		'option_name'    => 'hd_ssi_license_key',
		'label'          => __( 'License Key', 'simple-social-images' ),
		'description'    => sprintf( __( 'Enter your %1$sSimple Social Images%2$s license key.  This is required in order to generate your social sharing images. Once you have saved this settings page, you will be able to activate you license with a button above this text.', 'simple-social-images' ), '<a href="https://simplesocialimages.com">', '</a>' ),
		'input_type'     => 'license',
		'order'          => 10,
	);

	$settings['post_types'] = array(
		'option_name'     => 'hd_ssi_post_types',
		'label'           => __( 'Post Types', 'simple-social-images' ),
		'description'     => __( 'Select the post types on which Simple Social Images will be activated and available on.' ),
		'input_type'      => 'checkboxes',
		'options'         => hd_ssi_get_site_post_types(),
		'order'           => 15,
		'group'           => 'start',
	);

	$settings['template_section'] = array(
		'option_name'    => 'hd_ssi_template_section',
		'label'          => __( 'Template settings', 'simple-social-images' ),
		'input_type'     => 'section',
		'order'          => 50,
	);

	$settings['use_title'] = array(
		'option_name'    => 'hd_ssi_use_title',
		'label'          => __( 'Use title', 'simple-social-images' ),
		'message'        => __( 'Use title', 'simple-social-images' ),
		'input_type'     => 'checkbox',
		'default_value'  => 1,
		'hide_label'     => true,
		'order'          => 52,
		'data_attributes' => array(
			'section-toggle'   => 'hd_ssi_title_section',
			'toggle-target'    => 'ssi-template__title-wrapper',
		),
		'section'        => 'hd_ssi_title_section',
	);

	$settings['use_logo'] = array(
		'option_name'    => 'hd_ssi_use_logo',
		'label'          => __( 'Use a logo', 'simple-social-images' ),
		'message'        => __( 'Use a logo', 'simple-social-images' ),
		'input_type'     => 'checkbox',
		'default_value'  => 1,
		'hide_label'     => true,
		'order'          => 53,
		'section'        => 'hd_ssi_logo_section',
		'data_attributes' => array(
			'section-toggle'   => 'hd_ssi_logo_section',
			'toggle-target'    => 'ssi-template__logo',
		),
	);

	$settings['use_image'] = array(
		'option_name'    => 'hd_ssi_use_image',
		'label'          => __( 'Use an image', 'simple-social-images' ),
		'message'        => __( 'Use an image', 'simple-social-images' ),
		'input_type'     => 'checkbox',
		'default_value'  => 1,
		'hide_label'     => true,
		'order'          => 54,
		'section'     => 'hd_ssi_image_section',
		'data_attributes' => array(
			'section-toggle'   => 'hd_ssi_image_section',
			'toggle-target'    => 'ssi-template__image',
		),
	);

	$settings['background_color'] = array(
		'option_name'     => 'hd_ssi_background_color',
		'label'           => __( 'Background Color', 'simple-social-images' ),
		'input_type'      => 'color_picker',
		'custom_property' => '--ssi--background-color',
		'data_attributes' => array(
			'custom-property' => '--ssi--background-color',
		),
		'order'           => 55,
		'group'           => 'start',
	);

	$settings['font_family'] = array(
		'option_name'     => 'hd_ssi_font_family',
		'label'           => __( 'Font Family', 'simple-social-images' ),
		'description'     => sprintf( __( '%1$sSee an example of what is required%2$s (the highlighted text).', 'simple-social-images' ), '<a target="_blank" href="' . esc_url( HD_SSI_LOCATION_URL . '/assets/img/google-font-family-example.jpg' ) . '">', '</a>' ),
		'input_type'      => 'text',
		'custom_property' => '--ssi--font-family',
		'data_attributes' => array(
			'custom-property' => '--ssi--font-family',
		),
		'default_value'   => 'sans-serif',
		'order'           => 56,
		'group'           => 'start',
		'section'       => 'hd_ssi_title_section',
	);

	$settings['google_font_url'] = array(
		'option_name'       => 'hd_ssi_google_font_url',
		'label'             => __( 'Google Font URL', 'simple-social-images' ),
		'description'  => sprintf( __( '%1$sSee an example of what is required%2$s (the highlighted text).', 'simple-social-images' ), '<a target="_blank" href="' . esc_url( HD_SSI_LOCATION_URL . '/assets/img/google-font-url-example.jpg' ) . '">', '</a>' ),
		'input_type'        => 'text',
		'sanitize_callback' => 'sanitize_url',
		'order'             => 57,
		'group'             => 'end',
		'section'       => 'hd_ssi_title_section',
	);

	$settings['title_section'] = array(
		'option_name'    => 'hd_ssi_title_section',
		'label'          => __( 'Title settings', 'simple-social-images' ),
		'input_type'     => 'section',
		'order'          => 100,
		'data_attributes' => array(
			'section-toggle'   => 'hd_ssi_title_section',
		),
		'section'        => 'hd_ssi_title_section',
	);

	$settings['title_position'] = array(
		'option_name'   => 'hd_ssi_title_position',
		'label'         => __( 'Position', 'simple-social-images' ),
		'input_type'    => 'select',
		'default_value' => 'bottom-left',
		'options'       => hd_ssi_get_position_options(),
		'order'         => 105,
		'data_attributes' => array(
			'target-class'   => 'ssi-template__title-wrapper',
			'modifier-class' => 'ssi--position--',
		),
		'section'       => 'hd_ssi_title_section',
	);

	$settings['title_offset_x'] = array(
		'option_name'       => 'hd_ssi_title_offset_x',
		'label'             => __( 'X-axis', 'simple-social-images' ),
		'input_type'        => 'number',
		'min'               => '-100',
		'max'               => '100',
		'step'              => '1',
		'custom_property'   => '--ssi--title--offset--x',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--offset--x',
		),
		'default_value'     => '5',
		'order'             => 106,
		'section'           => 'hd_ssi_title_section',
	);

	$settings['title_offset_y'] = array(
		'option_name'       => 'hd_ssi_title_offset_y',
		'label'             => __( 'Y-axis', 'simple-social-images' ),
		'input_type'        => 'number',
		'min'               => '-100',
		'max'               => '100',
		'step'              => '1',
		'custom_property'   => '--ssi--title--offset--y',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--offset--y',
		),
		'default_value'     => '-5',
		'order'             => 107,
		'section'           => 'hd_ssi_title_section',
	);

	$settings['title_width'] = array(
		'option_name'     => 'hd_ssi_title_width',
		'label'           => __( 'Width', 'simple-social-images' ),
		'input_type'      => 'number',
		'min'             => '1',
		'max'             => '100',
		'step'            => '1',
		'default_value'   => 80,
		'custom_property' => '--ssi--title--width',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--width',
		),
		'order'           => 110,
		'section'         => 'hd_ssi_title_section',
	);

	$settings['title_font_size'] = array(
		'option_name'     => 'hd_ssi_title_font_size',
		'label'           => __( 'Font Size', 'simple-social-images' ),
		'input_type'      => 'number',
		'min'             => '2',
		'max'             => '8',
		'step'            => '0.5',
		'default_value'   => 5,
		'custom_property' => '--ssi--title--font-size',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--font-size',
		),
		'order'           => 120,
		'section'         => 'hd_ssi_title_section',
		'group'             => 'start',
	);

	$settings['title_align'] = array(
		'option_name'       => 'hd_ssi_title_align',
		'label'             => __( 'Alignment', 'simple-social-images' ),
		'input_type'        => 'select',
		'options'           => array(
			'default' => __( 'Default', 'simple-social-images' ),
			'left'    => __( 'Left', 'simple-social-images' ),
			'right'   => __( 'Right', 'simple-social-images' ),
			'center'  => __( 'Center', 'simple-social-images' ),
		),
		'custom_property'   => '--ssi--title--text-align',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--text-align',
		),
		'default_value'     => 'left',
		'order'             => 125,
		'section'           => 'hd_ssi_title_section',
	);

	$settings['title_weight'] = array(
		'option_name'       => 'hd_ssi_title_weight',
		'label'             => __( 'Weight', 'simple-social-images' ),
		'input_type'        => 'select',
		'options'           => array(
			'100' => __( '100 - Thin', 'simple-social-images' ),
			'200' => '200',
			'300' => '300',
			'400' => __( '400 - Regular', 'simple-social-images' ),
			'500' => '500',
			'600' => '600',
			'700' => __( '700 - Bold', 'simple-social-images' ),
			'800' => '800',
			'900' => __( '900 - Heavy', 'simple-social-images' ),
		),
		'custom_property'   => '--ssi--title--font-weight',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--font-weight',
		),
		'default_value'     => '700',
		'order'             => 130,
		'section'           => 'hd_ssi_title_section',
	);

	$settings['title_style'] = array(
		'option_name'       => 'hd_ssi_title_style',
		'label'             => __( 'Style', 'simple-social-images' ),
		'input_type'        => 'select',
		'options'           => array(
			'normal' => __( 'Normal', 'simple-social-images' ),
			'italic' => __( 'Italic', 'simple-social-images' ),
		),
		'custom_property'   => '--ssi--title--font-style',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--font-style',
		),
		'default_value'     => 'normal',
		'order'             => 135,
		'section'           => 'hd_ssi_title_section',
	);

	$settings['title_text_transform'] = array(
		'option_name'       => 'hd_ssi_title_text_transform',
		'label'             => __( 'Text Transform', 'simple-social-images' ),
		'input_type'        => 'select',
		'options'           => array(
			'none'   => __( 'None', 'simple-social-images' ),
			'uppercase' => __( 'Uppercase', 'simple-social-images' ),
			'lowercase' => __( 'Lowercase', 'simple-social-images' ),
			'capitalize' => __( 'Capitalize', 'simple-social-images' ),
		),
		'custom_property'   => '--ssi--title--text-transform',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--text-transform',
		),
		'default_value'     => 'none',
		'order'             => 140,
		'section'           => 'hd_ssi_title_section',
	);

	$settings['title_color'] = array(
		'option_name'       => 'hd_ssi_title_color',
		'label'             => __( 'Color', 'simple-social-images' ),
		'input_type'        => 'color_picker',
		'custom_property'   => '--ssi--title--color',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--color',
		),
		'default_value'     => 'white',
		'order'             => 145,
		'group'             => 'start',
		'section'           => 'hd_ssi_title_section',
	);

	$settings['title_bg_color'] = array(
		'option_name'       => 'hd_ssi_title_bg_color',
		'label'             => __( 'Background Color', 'simple-social-images' ),
		'input_type'        => 'color_picker',
		'custom_property'   => '--ssi--title--background-color',
		'data_attributes'   => array(
			'custom-property' => '--ssi--title--background-color',
		),
		'default_value'     => '#5f0ddb',
		'order'             => 150,
		'section'           => 'hd_ssi_title_section',
	);

	$settings['title_background_type'] = array(
		'option_name'       => 'hd_ssi_title_background_type',
		'label'             => __( 'Background Type', 'simple-social-images' ),
		'input_type'        => 'select',
		'options'           => array(
			'block'    => __( 'Block', 'simple-social-images' ),
			'inline'   => __( 'Inline', 'simple-social-images' ),
			'gradient' => __( 'Gradient', 'simple-social-images' ),
		),
		'default_value'     => 'block',
		'order'             => 155,
		'data_attributes' => array(
			'target-class'   => 'ssi-template__title',
			'modifier-class' => 'ssi-background--',
		),
		'section'           => 'hd_ssi_title_section',
	);

	$settings['title_background_gradient'] = array(
		'option_name'       => 'hd_ssi_title_background_gradient',
		'label'             => __( 'Background Gradient', 'simple-social-images' ),
		'input_type'        => 'select',
		'options'           => array(
			'top'    => __( 'Top', 'simple-social-images' ),
			'left'   => __( 'Left', 'simple-social-images' ),
			'bottom' => __( 'Bottom', 'simple-social-images' ),
			'right'  => __( 'Right', 'simple-social-images' ),
		),
		'default_value'     => 'bottom',
		'order'             => 160,
		'data_attributes'   => array(
			'target-class'   => 'ssi-template__title',
			'modifier-class' => 'gradient--',
		),
		'section'           => 'hd_ssi_title_section',
	);

	$settings['logo_section'] = array(
		'option_name'       => 'hd_ssi_logo_section',
		'label'             => __( 'Logo Settings', 'simple-social-images' ),
		'input_type'        => 'section',
		'order'             => 200,
		'section'           => 'hd_ssi_logo_section',
	);

	$settings['logo'] = array(
		'option_name' => 'hd_ssi_logo',
		'label'       => __( 'Logo', 'simple-social-images' ),
		'description' => __( 'Upload your logo to display on your template. Each template may place the logo in a slightly different place. The text alignment setting below, sometimes changes the logo position.', 'simple-social-images' ),
		'input_type'  => 'image',
		'order'       => 205,
		'data_attributes' => array(
			'target-class'   => 'ssi-template__logo',
		),
		'section'     => 'hd_ssi_logo_section',
	);

	$settings['logo_position'] = array(
		'option_name'   => 'hd_ssi_logo_position',
		'label'         => __( 'Position', 'simple-social-images' ),
		'input_type'    => 'select',
		'default_value' => 'top-right',
		'options'       => hd_ssi_get_position_options(),
		'order'         => 210,
		'data_attributes' => array(
			'target-class'   => 'ssi-template__logo',
			'modifier-class' => 'ssi--position--',
		),
		'group'         => 'start',
		'section'       => 'hd_ssi_logo_section',
	);

	$settings['logo_offset_x'] = array(
		'option_name'       => 'hd_ssi_logo_offset_x',
		'label'             => __( 'X-axis', 'simple-social-images' ),
		'input_type'        => 'number',
		'min'               => '-100',
		'max'               => '100',
		'step'              => '1',
		'custom_property'   => '--ssi--logo--offset--x',
		'data_attributes'   => array(
			'custom-property' => '--ssi--logo--offset--x',
		),
		'default_value'     => '-5',
		'order'             => 211,
		'section'           => 'hd_ssi_logo_section',
	);

	$settings['logo_offset_y'] = array(
		'option_name'       => 'hd_ssi_logo_offset_y',
		'label'             => __( 'Y-axis', 'simple-social-images' ),
		'input_type'        => 'number',
		'min'               => '-100',
		'max'               => '100',
		'step'              => '1',
		'custom_property'   => '--ssi--logo--offset--y',
		'data_attributes'   => array(
			'custom-property' => '--ssi--logo--offset--y',
		),
		'default_value'     => '5',
		'order'             => 212,
		'section'           => 'hd_ssi_logo_section',
	);

	$settings['logo_size'] = array(
		'option_name'     => 'hd_ssi_logo_size',
		'label'           => __( 'Size', 'simple-social-images' ),
		'input_type'      => 'number',
		'min'             => '2',
		'max'             => '20',
		'step'            => '0.5',
		'custom_property' => '--ssi--logo--height',
		'data_attributes' => array(
			'custom-property' => '--ssi--logo--height',
		),
		'default_value'   => '5',
		'order'           => 215,
		'section'         => 'hd_ssi_logo_section',
	);

	$settings['image_section'] = array(
		'option_name'       => 'hd_ssi_image_section',
		'label'             => __( 'Image Settings', 'simple-social-images' ),
		'input_type'        => 'section',
		'order'             => 300,
		'section'           => 'hd_ssi_image_section',
	);

	$settings['use_featured_image'] = array(
		'option_name'    => 'hd_ssi_use_featured_image',
		'label'          => __( 'Use featured images', 'simple-social-images' ),
		'description'    => __( 'This will prevent post featured images being used', 'simple-social-images' ),
		'message'        => __( 'Use featured images', 'simple-social-images' ),
		'description'    => __( 'Instead of using an image from the images added below, if a post has a featured image assigned, the feature image will be used instead.', 'simple-social-images' ),
		'input_type'     => 'checkbox',
		'default_value'  => 0,
		'order'          => 302,
		'section'     => 'hd_ssi_image_section',
	);

	$settings['background_images'] = array(
		'option_name' => 'hd_ssi_background_images',
		'label'       => __( 'Add Images', 'simple-social-images' ),
		'description' => __( 'Upload background images to use on your template. Each template uses the background image slightly differently. Images are chosen at random from the images uploaded here, assuming your post does not have a featured image.', 'simple-social-images' ),
		'input_type'  => 'gallery',
		'order'       => 305,
		'data_attributes' => array(
			'target-class'   => 'ssi-template__image',
		),
		'group'           => 'start',
		'section'     => 'hd_ssi_image_section',
	);

	$settings['image_position'] = array(
		'option_name'       => 'hd_ssi_image_position',
		'label'             => __( 'Position', 'simple-social-images' ),
		'input_type'        => 'select',
		'options'           => hd_ssi_get_position_options(),
		'order'             => 310,
		'data_attributes'   => array(
			'target-class'   => 'ssi-template__image',
			'modifier-class' => 'ssi--position--',
		),
		'default_value'     => 'middle-center',
		'group'             => 'start',
		'section'     => 'hd_ssi_image_section',
	);

	$settings['image_offset_x'] = array(
		'option_name'       => 'hd_ssi_image_offset_x',
		'label'             => __( 'X-axis', 'simple-social-images' ),
		'input_type'        => 'number',
		'min'               => '-100',
		'max'               => '100',
		'step'              => '1',
		'custom_property'   => '--ssi--image--offset--x',
		'data_attributes' => array(
			'custom-property' => '--ssi--image--offset--x',
		),
		'default_value'     => '0',
		'order'             => 311,
		'section'     => 'hd_ssi_image_section',
	);

	$settings['image_offset_y'] = array(
		'option_name'       => 'hd_ssi_image_offset_y',
		'label'             => __( 'Y-axis', 'simple-social-images' ),
		'input_type'        => 'number',
		'min'               => '-100',
		'max'               => '100',
		'step'              => '1',
		'custom_property'   => '--ssi--image--offset--y',
		'data_attributes' => array(
			'custom-property' => '--ssi--image--offset--y',
		),
		'default_value'     => '0',
		'order'             => 312,
		'section'     => 'hd_ssi_image_section',
	);

	$settings['image_width'] = array(
		'option_name'     => 'hd_ssi_image_width',
		'label'           => __( 'Width', 'simple-social-images' ),
		'input_type'      => 'number',
		'min'             => '1',
		'max'             => '100',
		'step'            => '1',
		'custom_property' => '--ssi--image--width',
		'data_attributes' => array(
			'custom-property' => '--ssi--image--width',
		),
		'default_value'  => '95',
		'order'           => 315,
		'section'     => 'hd_ssi_image_section',
	);

	$settings['image_height'] = array(
		'option_name'     => 'hd_ssi_image_height',
		'label'           => __( 'Height', 'simple-social-images' ),
		'input_type'      => 'number',
		'min'             => '1',
		'max'             => '100',
		'step'            => '1',
		'default_value'  => '92',
		'custom_property' => '--ssi--image--height',
		'data_attributes' => array(
			'custom-property' => '--ssi--image--height',
		),
		'order'           => 320,
		'section'     => 'hd_ssi_image_section',
	);

	$settings['image_blend_mode'] = array(
		'option_name'       => 'hd_ssi_image_blend_mode',
		'label'             => __( 'Blend mode', 'simple-social-images' ),
		'input_type'        => 'select',
		'options'           => array(
			'default' => __( 'None', 'simple-social-images' ),
			'darken' => __( 'Darken', 'simple-social-images' ),
			'multiply' => __( 'Multiply', 'simple-social-images' ),
			'color-burn' => __( 'Color burn', 'simple-social-images' ),
			'lighten' => __( 'Lighten', 'simple-social-images' ),
			'screen' => __( 'Screen', 'simple-social-images' ),
			'color-dodge' => __( 'Color dodge', 'simple-social-images' ),
			'overlay' => __( 'Overlay', 'simple-social-images' ),
			'soft-light' => __( 'Soft light', 'simple-social-images' ),
			'hard-light' => __( 'Hard light', 'simple-social-images' ),
			'difference' => __( 'Difference', 'simple-social-images' ),
			'exclusion' => __( 'Exclusion', 'simple-social-images' ),
			'hue' => __( 'Hue', 'simple-social-images' ),
			'saturation' => __( 'Saturation', 'simple-social-images' ),
			'color' => __( 'Color', 'simple-social-images' ),
			'luminosity' => __( 'Luminosity', 'simple-social-images' ),
		),
		'order'             => 321,
		'custom_property'   => '--ssi--image--blend-mode',
		'data_attributes' => array(
			'custom-property'   => '--ssi--image--blend-mode',
		),
		'default_value'     => 'none',
		'group'             => 'start',
		'section'     => 'hd_ssi_image_section',
	);

	$settings['placeholder_title'] = array(
		'option_name'   => 'hd_ssi_placeholder_title',
		'label'         => __( 'Placeholder Title', 'simple-social-images' ),
		'input_type'    => 'hidden',
		'default_value' => 'Placeholder title - click to edit',
		'order'         => 1000,
	);

	// return the registered settings array.
	return $settings;

}

add_filter( 'hd_ssi_settings', 'hd_ssi_register_default_settings' );

/**
 * Controls the output of text input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_text( $setting, $value ) {

	// handle output for a text input.
	?>

	<input type="text" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-text hd-ssi-input hd-ssi-input--text" value="<?php echo esc_attr( $value ); ?>"<?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?> />

	<?php

}

add_action( 'hd_ssi_setting_type_text', 'hd_ssi_setting_input_type_text', 10, 2 );

/**
 * Controls the output of number input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_number( $setting, $value ) {

	// defaults for min, max and step.
	$min = '0';
	$max = '100';
	$step = '1';

	// if we have a max.
	if ( ! empty( $setting['max'] ) ) {
		$max = $setting['max'];
	}

	// if we have a min.
	if ( ! empty( $setting['min'] ) ) {
		$min = $setting['min'];
	}

	// if we have a step.
	if ( ! empty( $setting['step'] ) ) {
		$step = $setting['step'];
	}

	// handle output for a number input.
	?>

	<input type="number" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-number hd-ssi-input hd-ssi-input--number" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" step="<?php echo esc_attr( $step ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?> />

	<?php

}

add_action( 'hd_ssi_setting_type_number', 'hd_ssi_setting_input_type_number', 10, 2 );

/**
 * Controls the output of textarea input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_textarea( $setting, $value ) {

	// handle output for a text input.
	?>

	<textarea name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-text hd-ssi-input hd-ssi-input--textarea" value="<?php echo esc_attr( $value ); ?><?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?>"></textarea>

	<?php

}

add_action( 'hd_ssi_setting_type_textarea', 'hd_ssi_setting_input_type_textarea', 10, 2 );

/**
 * Controls the output of hidden input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_hidden( $setting, $value ) {

	// handle output for a hidden input.
	?>

	<input type="hidden" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="hidden hd-ssi-input hd-ssi-input--hidden" value="<?php echo esc_attr( $value ); ?>"<?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?> />

	<?php

}

add_action( 'hd_ssi_setting_type_hidden', 'hd_ssi_setting_input_type_hidden', 10, 2 );

/**
 * Controls the output of select input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_select( $setting, $value ) {

	// handle the output for a select input type setting.
	?>

	<select name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="hd-ssi-input hd-ssi-input--select"<?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?>>

		<?php

		// check we have some options.
		if ( isset( $setting['options'] ) ) {

			// loop through each select option.
			foreach ( $setting['options'] as $option_value => $option_label ) {

				?>

				<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?>><?php echo esc_attr( $option_label ); ?></option>

				<?php

			} // End foreach().
		} // End if().

		?>

	</select>

	<?php

}

add_action( 'hd_ssi_setting_type_select', 'hd_ssi_setting_input_type_select', 10, 2 );

/**
 * Controls the output of checkbox input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_checkbox( $setting, $value ) {

	// handle output for a text input.
	?>

	<label for="<?php echo esc_attr( $setting['option_name'] ); ?>">
		<input type="checkbox" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="hd-ssi-input hd-ssi-input--checkbox" value="1" <?php checked( $value, 1 ); ?><?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?> />
		<span class="hd-ssi-input-message"><?php echo wp_kses_post( $setting['message'] ); ?></span>
	</label>

	<?php

}

add_action( 'hd_ssi_setting_type_checkbox', 'hd_ssi_setting_input_type_checkbox', 10, 2 );

/**
 * Controls the output of checkboxes input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_checkboxes( $setting, $value ) {

	// if we have options.
	if ( ! empty( $setting['options'] ) ) {

		// if the current value is not empty.
		if ( empty( $value ) ) {
			$value = array();
		}

		// loop through each option.
		foreach ( $setting['options'] as $post_type => $label ) {

			// if this post type is in the value array.
			if ( in_array( $post_type, $value, true ) ) {
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}

			?>
			<label for="<?php echo esc_attr( $setting['option_name'] ); ?>-<?php echo esc_attr( $post_type ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $setting['option_name'] ); ?>[]" id="<?php echo esc_attr( $setting['option_name'] ); ?>-<?php echo esc_attr( $post_type ); ?>" class="hd-ssi-input hd-ssi-input--checkbox" value="<?php echo esc_attr( $post_type ); ?>"<?php echo esc_attr( $checked ); ?> />
				<span class="hd-ssi-input-message" style="line-height: 1.8;"><?php echo esc_html( $label ); ?></span>
			</label>

			<?php

		}

	}

}

add_action( 'hd_ssi_setting_type_checkboxes', 'hd_ssi_setting_input_type_checkboxes', 10, 2 );

/**
 * Controls the output of color picker input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_color_picker( $setting, $value ) {

	// handle output for a text input.
	?>

	<input type="text" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-text hd-ssi-input hd-ssi-input--color-picker" value="<?php echo esc_attr( $value ); ?>"<?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?> />

	<?php

}

add_action( 'hd_ssi_setting_type_color_picker', 'hd_ssi_setting_input_type_color_picker', 10, 2 );

/**
 * Controls the output of image input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_image( $setting, $value ) {

	// handle output for a text input.
	?>
	
	<div id="hd-ssi-image-wrapper" class="hd-ssi-image-wrapper" data-input-id="<?php echo esc_attr( $setting['option_name'] ); ?>">
		
		<?php

		// if we have an image already.
		if ( ! empty( $value ) ) {

			// get the url of the image.
			echo wp_get_attachment_image(
				absint( $value ),
				'full',
				false,
				array(
					'class' => 'hd-ssi-image',
					'id'    => $setting['option_name'] . '-image',
				)
			);

			?>
			<span class="dashicons dashicons-no hd-ssi-image--remove" data-input-id="<?php echo esc_attr( $setting['option_name'] ); ?>"></span>
			<?php

		}

		?>

	</div>

	<input type="text" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-text hd-ssi-input hd-ssi-input--image" value="<?php echo esc_attr( $value ); ?>"<?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?> />

	<a href="#" class="button-secondary hd-ssi-image-button"><?php esc_html_e( 'Upload/Choose Image', 'simple-social-images' ); ?></a>

	<?php

}

add_action( 'hd_ssi_setting_type_image', 'hd_ssi_setting_input_type_image', 10, 2 );

/**
 * Controls the output of gallery input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_gallery( $setting, $value ) {

	// handle output for a gallery input.
	?>
	
	<div id="hd-ssi-gallery-wrapper" class="hd-ssi-gallery-wrapper" data-placeholder="<?php echo esc_url( HD_SSI_LOCATION_URL . '/assets/img/no-image.jpg' ); ?>">
		
		<?php

		// create an array of gallery image ids that we can populate with known, valid images.
		$gallery_images = array();

		// if we have an image already.
		if ( ! empty( $value ) ) {

			// turn the value into an array.
			$values = explode( ',', $value );
			
			// if value is an array.
			if ( is_array( $values ) && ! empty( $values ) ) {

				// loop through each image.
				foreach ( $values as $image ) {

					?>

					<figure class="hd-ssi-gallery-item">

						<?php

						// get the image.
						$gallery_image = wp_get_attachment_image(
							absint( $image ),
							'thumbnail',
							false,
							array(
								'class' => 'hd-ssi-gallery-image',
								//'id'    => $setting['option_name'] . '-image',
							)
						);

						// if we have an image.
						if ( ! empty( $gallery_image ) ) {

							// add to the gallery image array.
							$gallery_images[] = absint( $image );

							// output the image.
							echo wp_kses_post( $gallery_image );

							// output the remove span.
							?>
							<span class="dashicons dashicons-no hd-ssi-gallery--remove" data-image-id="<?php echo esc_attr( absint( $image ) ); ?>"></span>
							<?php

						}

						?>

					</figure>

					<?php

				}

			}

		}

		?>

	</div>

	<input type="text" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>-input" class="regular-text hd-ssi-input hd-ssi-input--gallery" value="<?php echo esc_attr( implode( ',', $gallery_images ) ); ?>"<?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?> />

	<a href="#" class="button-secondary hd-ssi-gallery-button"><?php esc_html_e( 'Upload/Choose Images', 'simple-social-images' ); ?></a>

	<?php

}

add_action( 'hd_ssi_setting_type_gallery', 'hd_ssi_setting_input_type_gallery', 10, 2 );

/**
 * Controls the output of range input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_range( $setting, $value ) {

	// defaults for min, max and step.
	$min = '0';
	$max = '100';
	$step = '1';

	// if we have a max.
	if ( ! empty( $setting['max'] ) ) {
		$max = $setting['max'];
	}

	// if we have a min.
	if ( ! empty( $setting['min'] ) ) {
		$min = $setting['min'];
	}

	// if we have a step.
	if ( ! empty( $setting['step'] ) ) {
		$step = $setting['step'];
	}

	// handle output for a range input.
	?>

	<input type="range" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-text hd-ssi-input hd-ssi-input--range" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" step="<?php echo esc_attr( $step ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?> />

	<?php

}

add_action( 'hd_ssi_setting_type_range', 'hd_ssi_setting_input_type_range', 10, 2 );

/**
 * Controls the output of section input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_section( $setting, $value ) {

	// if we have any section text.
	if ( ! empty( $setting['description'] ) ) {

		// output the text.
		?>
		<p class="section-text"><?php echo esc_html( $setting['description'] ); ?></p>
		<?php

	}

}

add_action( 'hd_ssi_setting_type_section', 'hd_ssi_setting_input_type_section', 10, 2 );

/**
 * Controls the output of license input setting.
 *
 * @param  array $setting an array of the current setting.
 * @param  mixed $value   the current value of this setting saved in the database.
 */
function hd_ssi_setting_input_type_license( $setting, $value ) {

	// set a classes array for this field.
	$classes = array(
		'regular-text',
		'hd-ssi-input',
		'hd-ssi-input--license',
		'hd-ssi-input--license-' . hd_ssi_get_license_key_status()
	);

	?>

	<input type="text" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo wp_kses_post( hd_ssi_output_setting_data_attributes( $setting ) ); ?> />

	<?php

	// add a nonce field to check for on activation of the license.
	wp_nonce_field( 'hd_ssi_license_nonce', 'hd_ssi_license_nonce' );

	// get the license key and status.
	$license		= hd_ssi_get_license_key();
	$license_status = hd_ssi_get_license_key_status();

	// if we have a license key and the status in no valid.
	if( $license != false && $license_status != 'valid' ) {
		
		// output the activate license button.
		?>
		<input type="submit" class="button-secondary" name="hd_ssi_license_activate" value="<?php _e( 'Activate License' ); ?>"/>
		<?php
		
	}
	
	// we have an active valid license.
	elseif( $license != false && $license_status != 'invalid' ) {
		
		// output the deactivate license button.
		?>
		<input type="submit" class="button-secondary" name="hd_ssi_license_deactivate" value="<?php _e( 'Deactivate License' ); ?>"/>
		<?php
		
	}

}

add_action( 'hd_ssi_setting_type_license', 'hd_ssi_setting_input_type_license', 10, 2 );

/**
 * Adds the description beneath each input.
 */
function hd_ssi_output_setting_descriptions( $setting, $value ) {

	// if we have a description.
	if ( empty( $setting['description'] ) ) {
		return;
	}

	// output the description.
	?>
	<p class="hd-ssi-input-description"><?php echo wp_kses_post( $setting['description'] ); ?></p>
	<?php

}

add_action( 'hd_ssi_after_setting', 'hd_ssi_output_setting_descriptions', 10, 2 );

/**
 * Adds the group divider above the input.
 */
function hd_ssi_output_group_start_markup( $setting, $value ) {

	// if we have a group.
	if ( empty( $setting['group'] ) ) {
		return;
	}

	// if this is not a group start.
	if ( 'start' !== $setting['group'] ) {
		return;
	}

	// create a section data id.
	$section_id = '';

	// if this setting has a section.
	if ( ! empty( $setting['section'] ) ) {

		// store the section name.
		$section_id = $setting['section'];

	}

	// output the description.
	?>
	<hr class="group-divider" data-section-id="<?php echo esc_attr( str_replace( '_', '-', $section_id ) ); ?>" >
	<?php

}

add_action( 'hd_ssi_before_settings_wrapper', 'hd_ssi_output_group_start_markup', 10, 2 );

/**
 * Adds the group divider below the input.
 */
function hd_ssi_output_group_end_markup( $setting, $value ) {

	// if we have a group.
	if ( empty( $setting['group'] ) ) {
		return;
	}

	// if this is not a group end.
	if ( 'end' !== $setting['group'] ) {
		return;
	}

	// create a section data id.
	$section_id = '';

	// if this setting has a section.
	if ( ! empty( $setting['section'] ) ) {

		// store the section name.
		$section_id = $setting['section'];

	}

	// output the divider.
	?>
	<hr class="group-divider" data-section-id="<?php echo esc_attr( str_replace( '_', '-', $section_id ) ); ?>" >
	<?php

}

add_action( 'hd_ssi_after_settings_wrapper', 'hd_ssi_output_group_end_markup', 10, 2 );

/**
 * Adds a tooltip items to settings that have a description.
 */
function hd_ssi_add_tool_tip_icon( $setting, $value ) {

	// if the setting has no description.
	if ( empty( $setting['description'] ) ) {
		return;
	}

	// create an array of section to ignore.
	$ignored_sections = array(
		'section',
	);

	// if this setting type is in the ignore list.
	if ( in_array( $setting['input_type'], $ignored_sections, true ) ) {
		return; // do nothing.
	}

	?>
	<span class="dashicons dashicons-editor-help hd-ssi-tooltip hd-ssi-tooltip--<?php echo esc_attr( $setting['input_type'] ); ?>"></span>
	<?php

}

add_action( 'hd_si_after_setting_label', 'hd_ssi_add_tool_tip_icon', 10, 2 );

/**
 * Outputs any data attributes add to settings.
 *
 * @param array $settings The array of args for this setting.
 */
function hd_ssi_output_setting_data_attributes( $setting ) {

	// if the setting has no data attributes.
	if ( empty( $setting['data_attributes'] ) ) {
		return '';
	}

	// create an output var.
	$output = '';

	// loop through each attribute.
	foreach ( $setting['data_attributes'] as $attr_name => $attr_value ) {
		
		$output .= 'data-' . $attr_name . '="' . $attr_value . '" ';

	}

	// trim any trailing space off the output.
	$output = trim( $output );

	// return the output.
	return $output;

}
