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
		'order'           => 20,
	);

	$settings['ignore_featured_image'] = array(
		'option_name'    => 'hd_ssi_ignore_featured_image',
		'label'          => __( 'Ignore featured images', 'simple-social-images' ),
		'description'    => __( 'This will prevent post featured images being used', 'simple-social-images' ),
		'message'        => __( 'Ignore featured images.', 'simple-social-images' ),
		'description'    => __( 'This will prevent the plugin from using a post\'s featured image in the generated social sharing image. Images will be chosen randomly from your uploaded background images below.', 'simple-social-images' ),
		'input_type'     => 'checkbox',
		'default_value'  => 0,
		'order'          => 30,
	);

	$settings['background_color'] = array(
		'option_name'     => 'hd_ssi_background_color',
		'label'           => __( 'Background Color', 'simple-social-images' ),
		'input_type'      => 'color_picker',
		'custom_property' => '--ssi--background-color',
		'data_attributes' => array(
			'custom-property' => '--ssi--background-color',
		),
		'order'           => 40,
	);

	$settings['title_section'] = array(
		'option_name'    => 'hd_ssi_title_section',
		'label'          => __( 'Title settings', 'simple-social-images' ),
		'input_type'     => 'section',
		'order'          => 100,
	);

	$settings['title_position'] = array(
		'option_name'   => 'hd_ssi_title_position',
		'label'         => __( 'Position', 'simple-social-images' ),
		'input_type'    => 'select',
		'default_value' => 'bottom-center',
		'options'       => hd_ssi_get_position_options(),
		'order'         => 105,
		'data_attributes' => array(
			'target-class'   => 'ssi-template__title-wrapper',
			'modifier-class' => 'position--',
		)
	);

	

	$settings['title_width'] = array(
		'option_name'     => 'hd_ssi_title_width',
		'label'           => __( 'Width', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '1',
		'max'             => '100',
		'step'            => '1',
		'default_value'   => 100,
		'custom_property' => '--ssi--title--width',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--width',
		),
		'order'           => 110,
	);

	$settings['title_margin'] = array(
		'option_name'       => 'hd_ssi_title_margin',
		'label'             => __( 'Margin', 'simple-social-images' ),
		'input_type'        => 'range',
		'min'               => '0',
		'max'               => '100',
		'step'              => '1',
		'custom_property'   => '--ssi--title--margin',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--margin',
		),
		'default_value'     => '0',
		'order'             => 115,
	);

	$settings['title_font_size'] = array(
		'option_name'     => 'hd_ssi_title_font_size',
		'label'           => __( 'Font Size', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '2',
		'max'             => '8',
		'step'            => '0.5',
		'default_value'   => 4,
		'custom_property' => '--ssi--title--font-size',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--font-size',
		),
		'order'           => 120,
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
		'default_value'     => 'default',
		'order'             => 125,
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
		'default_value'     => '400',
		'order'             => 130,
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
		'default_value'     => 'normal',
		'order'             => 135,
	);

	$settings['title_text_transform'] = array(
		'option_name'       => 'hd_ssi_title_text_transform',
		'label'             => __( 'Text Transform', 'simple-social-images' ),
		'input_type'        => 'select',
		'options'           => array(
			'default'   => __( 'Default', 'simple-social-images' ),
			'uppercase' => __( 'Uppercase', 'simple-social-images' ),
		),
		'custom_property'   => '--ssi--title--text-transform',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--text-transform',
		),
		'default_value'     => 'default',
		'order'             => 140,
	);

	

	$settings['title_color'] = array(
		'option_name'       => 'hd_ssi_title_color',
		'label'             => __( 'Color', 'simple-social-images' ),
		'input_type'        => 'color_picker',
		'custom_property'   => '--ssi--title--color',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--color',
		),
		'order'             => 145,
	);

	$settings['title_bg_color'] = array(
		'option_name'       => 'hd_ssi_title_bg_color',
		'label'             => __( 'Background Color', 'simple-social-images' ),
		'input_type'        => 'color_picker',
		'custom_property'   => '--ssi--title--background-color',
		'data_attributes' => array(
			'custom-property' => '--ssi--title--background-color',
		),
		'order'             => 150,
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
			'modifier-class' => 'background--',
		)
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
		'data_attributes' => array(
			'target-class'   => 'ssi-template__title',
			'modifier-class' => 'gradient--',
		)
	);

	

	$settings['logo_section'] = array(
		'option_name'       => 'hd_ssi_logo_section',
		'label'             => __( 'Logo Settings', 'simple-social-images' ),
		'input_type'        => 'section',
		'order'             => 200,
	);

	$settings['logo'] = array(
		'option_name' => 'hd_ssi_logo',
		'label'       => __( 'Logo', 'simple-social-images' ),
		'description' => __( 'Upload your logo to display on your template. Each template may place the logo in a slightly different place. The text alignment setting below, sometimes changes the logo position.', 'simple-social-images' ),
		'input_type'  => 'image',
		'order'       => 205,
	);

	$settings['logo_position'] = array(
		'option_name'   => 'hd_ssi_logo_position',
		'label'         => __( 'Position', 'simple-social-images' ),
		'input_type'    => 'select',
		'default_value' => 'top-left',
		'options'       => hd_ssi_get_position_options(),
		'order'         => 210,
		'data_attributes' => array(
			'target-class'   => 'ssi-template__logo',
			'modifier-class' => 'position--',
		)
	);

	$settings['logo_size'] = array(
		'option_name'     => 'hd_ssi_logo_size',
		'label'           => __( 'Size', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '2',
		'max'             => '20',
		'step'            => '0.5',
		'custom_property' => '--ssi--logo--height',
		'data_attributes' => array(
			'custom-property' => '--ssi--logo--height',
		),
		'default_value'   => '4',
		'order'           => 215,
	);

	$settings['logo_margin'] = array(
		'option_name'     => 'hd_ssi_logo_margin',
		'label'           => __( 'Margin', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '0',
		'max'             => '100',
		'step'            => '1',
		'default_value;'  => '0',
		'custom_property' => '--ssi--logo--margin',
		'data_attributes' => array(
			'custom-property' => '--ssi--logo--margin',
		),
		'order'           => 220,
	);

	$settings['image_section'] = array(
		'option_name'       => 'hd_ssi_image_section',
		'label'             => __( 'Image Settings', 'simple-social-images' ),
		'input_type'        => 'section',
		'order'             => 300,
	);

	$settings['background_images'] = array(
		'option_name' => 'hd_ssi_background_images',
		'label'       => __( 'Add Images', 'simple-social-images' ),
		'description' => __( 'Upload background images to use on your template. Each template uses the background image slightly differently. Images are chosen at random from the images uploaded here, assuming your post does not have a featured image.', 'simple-social-images' ),
		'input_type'  => 'gallery',
		'order'       => 305,
	);

	$settings['image_position'] = array(
		'option_name'       => 'hd_ssi_image_position',
		'label'             => __( 'Position', 'simple-social-images' ),
		'input_type'        => 'select',
		'options'           => hd_ssi_get_position_options(),
		'order'             => 310,
		'data_attributes' => array(
			'target-class'   => 'ssi-template__image',
			'modifier-class' => 'position--',
		)
	);

	$settings['image_width'] = array(
		'option_name'     => 'hd_ssi_image_width',
		'label'           => __( 'Width', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '1',
		'max'             => '100',
		'step'            => '1',
		'custom_property' => '--ssi--image--width',
		'default_value;'  => '10',
		'order'           => 315,
	);

	$settings['image_height'] = array(
		'option_name'     => 'hd_ssi_image_height',
		'label'           => __( 'Height', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '1',
		'max'             => '100',
		'step'            => '1',
		'default_value;'  => '100',
		'custom_property' => '--ssi--image--height',
		'data_attributes' => array(
			'custom-property' => '--ssi--image--height',
		),
		'order'           => 320,
	);

	$settings['image_margin'] = array(
		'option_name'     => 'hd_ssi_image_margin',
		'label'           => __( 'Margin', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '0',
		'max'             => '100',
		'step'            => '1',
		'default_value;'  => '0',
		'custom_property' => '--ssi--image--margin',
		'data_attributes' => array(
			'custom-property' => '--ssi--image--margin',
		),
		'order'           => 325,
	);

	$settings['overlay_section'] = array(
		'option_name'       => 'hd_ssi_overlay_section',
		'label'             => __( 'Overlay Settings', 'simple-social-images' ),
		'input_type'        => 'section',
		'order'             => 400,
	);

	$settings['overlay_position'] = array(
		'option_name'     => 'hd_ssi_overlay_position',
		'label'           => __( 'Position', 'simple-social-images' ),
		'input_type'      => 'select',
		'default_value;'  => 'top-left',
		'options'         => hd_ssi_get_position_options(),
		'order'           => 405,
		'data_attributes' => array(
			'target-class'   => 'ssi-template__overlay',
			'modifier-class' => 'position--',
		)
	);

	$settings['overlay_width'] = array(
		'option_name'     => 'hd_ssi_overlay_width',
		'label'           => __( 'Width', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '0',
		'max'             => '100',
		'step'            => '1',
		'default_value;'  => '100',
		'custom_property' => '--ssi--overlay--width',
		'data_attributes' => array(
			'custom-property' => '--ssi--overlay--width',
		),
		'order'           => 410,
	);

	$settings['overlay_height'] = array(
		'option_name'     => 'hd_ssi_overlay_height',
		'label'           => __( 'Height', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '0',
		'max'             => '100',
		'step'            => '1',
		'default_value;'  => '100',
		'custom_property' => '--ssi--overlay--height',
		'data_attributes' => array(
			'custom-property' => '--ssi--overlay--height',
		),
		'order'           => 415,
	);

	$settings['overlay_margin'] = array(
		'option_name'     => 'hd_ssi_overlay_margin',
		'label'           => __( 'Margin', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '0',
		'max'             => '100',
		'step'            => '1',
		'default_value;'  => '0',
		'custom_property' => '--ssi--overlay--margin',
		'data_attributes' => array(
			'custom-property' => '--ssi--overlay--margin',
		),
		'order'           => 420,
	);

	$settings['overlay_color'] = array(
		'option_name'    => 'hd_ssi_overlay_color',
		'label'          => __( 'Color', 'simple-social-images' ),
		'input_type'     => 'color_picker',
		'custom_property' => '--ssi--overlay--background-color',
		'data_attributes' => array(
			'custom-property' => '--ssi--overlay--background-color',
		),
		'order'          => 425,
	);

	$settings['overlay_opacity'] = array(
		'option_name'     => 'hd_ssi_overlay_opacity',
		'label'           => __( 'Opacity', 'simple-social-images' ),
		'input_type'      => 'range',
		'min'             => '0',
		'max'             => '100',
		'step'            => '1',
		'default_value;'  => '0',
		'custom_property' => '--ssi--overlay--opacity',
		'data_attributes' => array(
			'custom-property' => '--ssi--overlay--opacity',
		),
		'order'           => 430,
	);

	$settings['placeholder_title'] = array(
		'option_name' => 'hd_ssi_placeholder_title',
		'label'       => __( 'Placeholder Title', 'simple-social-images' ),
		'input_type'  => 'hidden',
		'order'       => 1000,
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
				<span style="line-height: 1.8;"><?php echo esc_html( $label ); ?></span>
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

function hd_ssi_add_top_tip_icon( $setting, $value ) {

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

add_action( 'hd_si_after_setting_label', 'hd_ssi_add_top_tip_icon', 10, 2 );

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
