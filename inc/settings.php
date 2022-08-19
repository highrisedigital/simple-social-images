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

	// get the selected template.
	$selected_template = hd_ssi_get_template();

	$settings['license_key'] = array(
		'option_name'    => 'hd_ssi_license_key',
		'label'          => __( 'License Key', 'simple-social-images' ),
		'description'    => sprintf( __( 'Enter your %1$sSimple Social Images%2$s license key.  This is required in order to generate your social sharing images.', 'simple-social-images' ), '<a href="https://simplesocialimages.com">', '</a>' ),
		'input_type'     => 'text',
		'order'          => 10,
	);

	$settings['post_types'] = array(
		'option_name'    => 'hd_ssi_post_types',
		'label'          => __( 'Post Types', 'simple-social-images' ),
		'description'    => __( 'Select the post types on which Simple Social Images will be activated and available on.' ),
		'input_type'     => 'checkboxes',
		'options'        => hd_ssi_get_site_post_types(),
		'order'          => 20,
	);

	$settings['template_section'] = array(
		'option_name'    => 'hd_ssi_template_section',
		'label'          => __( 'Template Settings', 'simple-social-images' ),
		'input_type'     => 'section',
		'order'          => 30,
	);

	$settings['template'] = array(
		'option_name'    => 'hd_ssi_template',
		'label'          => __( 'Select a Template', 'simple-social-images' ),
		//'description'    => __( 'Choose which template to use. Please save these settings to force the preview to update the template.', 'simple-social-images' ),
		'input_type'     => 'select',
		'options'        => hd_ssi_get_templates(),
		'order'          => 40,
	);

	// if the current template is from the plugin folder.
	if ( str_contains( $selected_template, HD_SSI_LOCATION ) ) {

		$settings['template_reversed'] = array(
			'option_name'    => 'hd_ssi_template_reversed',
			'label'          => __( 'Reverse this template', 'simple-social-images' ),
			//'description'    => __( 'This will reverse the layout of the selected template, should the template support reversal.', 'simple-social-images' ),
			'input_type'     => 'checkbox',
			'order'          => 45,
		);

		$settings['colors_section'] = array(
			'option_name'    => 'hd_ssi_colors_section',
			'label'          => __( 'Color Settings', 'simple-social-images' ),
			'input_type'     => 'section',
			'order'          => 50,
		);
	
		$settings['text_color'] = array(
			'option_name'    => 'hd_ssi_text_color',
			'label'          => __( 'Text Color', 'simple-social-images' ),
			//'description'    => __( 'Enter or choose the text color.', 'simple-social-images' ),
			'input_type'     => 'color_picker',
			'order'          => 60,
		);
	
		$settings['text_bg_color'] = array(
			'option_name'    => 'hd_ssi_text_bg_color',
			'label'          => __( 'Text Background Color', 'simple-social-images' ),
			//'description'    => __( 'Enter or choose the text background color.', 'simple-social-images' ),
			'input_type'     => 'color_picker',
			'order'          => 70,
		);
	
		$settings['bg_color'] = array(
			'option_name'    => 'hd_ssi_bg_color',
			'label'          => __( 'Background Color', 'simple-social-images' ),
			//'description'    => __( 'Enter or choose the background color.', 'simple-social-images' ),
			'input_type'     => 'color_picker',
			'order'          => 80,
		);
		
	}

	$settings['logo_section'] = array(
		'option_name' => 'hd_ssi_logo_section',
		'label'       => __( 'Logo Settings', 'simple-social-images' ),
		'input_type'  => 'section',
		'order'       => 90,
	);

	$settings['logo'] = array(
		'option_name' => 'hd_ssi_logo',
		'label'       => __( 'Image', 'simple-social-images' ),
		//'description' => __( 'Upload your logo to display on your images.', 'simple-social-images' ),
		'input_type'  => 'image',
		'order'       => 100,
	);

	// if the current template is from the plugin folder.
	if ( str_contains( $selected_template, HD_SSI_LOCATION ) ) {

		$settings['logo_size'] = array(
			'option_name' => 'hd_ssi_logo_size',
			'label'       => __( 'Size', 'simple-social-images' ),
			//'description' => __( 'Select a size for the logo.', 'simple-social-images' ),
			'input_type'  => 'range',
			'min'         => '4',
			'max'         => '12',
			'step'        => '0.1', 
			'order'       => 110,
		);

	}

	$settings['background_images_section'] = array(
		'option_name' => 'hd_ssi_background_images_section',
		'label'       => __( 'Background Images Settings', 'simple-social-images' ),
		'input_type'  => 'section',
		'order'       => 120,
	);

	$settings['background_images'] = array(
		'option_name' => 'hd_ssi_background_images',
		'label'       => __( 'Add Images', 'simple-social-images' ),
		//'description' => __( 'Upload background images to use on your template. Each template uses the background image slightly differently. Images are chosen at random from the images uploaded here, assuming your post does not have a featured image.', 'simple-social-images' ),
		'input_type'  => 'gallery',
		'order'       => 130,
	);

	// if the current template is from the plugin folder.
	if ( str_contains( $selected_template, HD_SSI_LOCATION ) ) {

		$settings['fonts_section'] = array(
			'option_name' => 'hd_ssi_font_sizes_section',
			'label'       => __( 'Font Settings', 'simple-social-images' ),
			'input_type'  => 'section',
			'order'       => 140,
		);

		$settings['font_size'] = array(
			'option_name' => 'hd_ssi_font_size',
			'label'       => __( 'Font Size', 'simple-social-images' ),
			//'description' => __( 'Select a size for the font.', 'simple-social-images' ),
			'input_type'  => 'range',
			'min'         => '2',
			'max'         => '8',
			'step'        => '0.5',
			'order'       => 150,
		);

		$settings['font_weight'] = array(
			'option_name'       => 'hd_ssi_font_weight',
			'label'             => __( 'Font Weight', 'simple-social-images' ),
			//'description'       => __( 'Choose the font weight to use on your selected template.', 'simple-social-images' ),
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
			'order'             => 160,
		);

		$settings['font_style'] = array(
			'option_name'       => 'hd_ssi_font_style',
			'label'             => __( 'Font Style', 'simple-social-images' ),
			//'description'       => __( 'Choose the font style to use on your selected template.', 'simple-social-images' ),
			'input_type'        => 'select',
			'options'           => array(
				'normal' => __( 'Normal', 'simple-social-images' ),
				'italic' => __( 'Italic', 'simple-social-images' ),
			),
			'order'             => 170,
		);

		$settings['text_align'] = array(
			'option_name'       => 'hd_ssi_text_align',
			'label'             => __( 'Text Alignment', 'simple-social-images' ),
			//'description'       => __( 'Choose how to align your text in your template.', 'simple-social-images' ),
			'input_type'        => 'select',
			'options'           => array(
				'default' => __( 'Default', 'simple-social-images' ),
				'left'    => __( 'Left', 'simple-social-images' ),
				'right'   => __( 'Right', 'simple-social-images' ),
				'center'  => __( 'Centre', 'simple-social-images' ),
			),
			'order'             => 180,
		);

		$settings['google_font_url'] = array(
			'option_name'       => 'hd_ssi_google_font_url',
			'label'             => __( 'Google Font URL', 'simple-social-images' ),
			'description'  => sprintf( __( '%1$sSee an example of what is required%2$s (the highlighted text).', 'simple-social-images' ), '<a target="_blank" href="' . esc_url( HD_SSI_LOCATION_URL . '/assets/img/google-font-url-example.jpg' ) . '">', '</a>' ),
			'input_type'        => 'text',
			'sanitize_callback' => 'sanitize_url',
			'order'             => 190,
		);

		$settings['google_font_family'] = array(
			'option_name' => 'hd_ssi_google_font_family',
			'label'       => __( 'Google Font Family', 'simple-social-images' ),
			'description'  => sprintf( __( '%1$sSee an example of what is required%2$s (the highlighted text).', 'simple-social-images' ), '<a target="_blank" href="' . esc_url( HD_SSI_LOCATION_URL . '/assets/img/google-font-family-example.jpg' ) . '">', '</a>' ),
			'input_type'  => 'text',
			'order'       => 200,
		);

	}

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

	<input type="text" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-text hd-ssi-input hd-ssi-input--text" value="<?php echo esc_attr( $value ); ?>" />

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

	<textarea name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-text hd-ssi-input hd-ssi-input--textarea" value="<?php echo esc_attr( $value ); ?>"></textarea>

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

	<input type="hidden" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="hidden hd-ssi-input hd-ssi-input--hidden" value="<?php echo esc_attr( $value ); ?>" />

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

	<select name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="hd-ssi-input hd-ssi-input--select">

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
		<input type="checkbox" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="hd-ssi-input hd-ssi-input--checkbox" value="1" <?php checked( $value, 1 ); ?> />
		<span style="line-height: 1.8;"><?php echo wp_kses_post( $setting['description'] ); ?></span>
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

	<input type="text" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-text hd-ssi-input hd-ssi-input--color-picker" value="<?php echo esc_attr( $value ); ?>" />

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
	
	<div class="hd-ssi-image-wrapper" data-input-id="<?php echo esc_attr( $setting['option_name'] ); ?>">
		
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

		<input type="text" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-text hd-ssi-input hd-ssi-input--image" value="<?php echo esc_attr( $value ); ?>" />

		<a href="#" class="button-secondary hd-ssi-image-button"><?php esc_html_e( 'Upload/Choose Image', 'simple-social-images' ); ?></a>

	</div>

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
	
	<div class="hd-ssi-gallery-wrapper" data-placeholder="<?php echo esc_url( HD_SSI_LOCATION_URL . '/assets/img/no-image.jpg' ); ?>">
		
		<?php

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

						// output the image.
						echo wp_get_attachment_image(
							absint( $image ),
							'thumbnail',
							false,
							array(
								'class' => 'hd-ssi-gallery-image',
								//'id'    => $setting['option_name'] . '-image',
							)
						);

						?>

						<span class="dashicons dashicons-no hd-ssi-gallery--remove" data-image-id="<?php echo esc_attr( absint( $image ) ); ?>"></span>

					</figure>

					<?php

				}

			}

		}

		?>

	</div>

	<input type="text" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>-input" class="regular-text hd-ssi-input hd-ssi-input--gallery" value="<?php echo esc_attr( $value ); ?>" />

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
	$min = '1';
	$max = '20';
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

	<input type="range" name="<?php echo esc_attr( $setting['option_name'] ); ?>" id="<?php echo esc_attr( $setting['option_name'] ); ?>" class="regular-text hd-ssi-input hd-ssi-input--range" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" step="<?php echo esc_attr( $step ); ?>" value="<?php echo esc_attr( $value ); ?>" />

	<?php

}

add_action( 'hd_ssi_setting_type_range', 'hd_ssi_setting_input_type_range', 10, 2 );

/**
 * Controls the output of license input setting.
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

add_action( 'wpbb_setting_type_section', 'hd_ssi_setting_input_type_section', 10, 2 );

/**
 * Adds the description beneath each input.
 */
function hd_ssi_output_setting_descriptions( $setting, $value ) {

	// if we have a description.
	if ( empty( $setting['description'] ) || 'checkbox' === $setting['input_type'] ) {
		return;
	}

	// output the description.
	?>
	<p class="hd-ssi-input-description"><?php echo wp_kses_post( $setting['description'] ); ?></p>
	<?php

}

add_action( 'hd_ssi_after_setting', 'hd_ssi_output_setting_descriptions', 10, 2 );
