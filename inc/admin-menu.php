<?php
/**
 * Register the new admin menu for the plugin settings.
 */
function hd_ssi_register_settings_page() {

	// add the sub menu page to settings.
	add_submenu_page(
		'options-general.php',
		__( 'Simple Social Images', 'simple-social-images' ),
		__( 'Simple Social Images', 'simple-social-images' ),
		'manage_options',
		'hd-ssi-settings',
		'hd_ssi_settings_page_output'
	);
	
}

add_action( 'admin_menu', 'hd_ssi_register_settings_page' );

/**
 * Outputs the settings page content.
 */
function hd_ssi_settings_page_output() {

	?>

	<div class="wrap">

		<h1><?php echo esc_html_e( 'Simple Social Images Settings', 'simple-social-images' ); ?></h1>

		<?php

		// before the settings form output.
		do_action( 'hd_ssi_before_settings_form_output' );

		?>

		<form method="post" action="options.php" id="ssi-settings-form" class="hd-ssi-settings-form">

			<?php

			// output settings field nonce action fields etc.
			settings_fields( 'hd_ssi_settings' );

			// get the registered settings for this settings page.
			$settings = hd_ssi_get_settings();

			// check we have registered settings.
			if ( ! empty( $settings ) ) {

				// before the settings table output.
				do_action( 'hd_ssi_before_settings_output' );

				?>

				<div class="form-table">

					<?php

					// loop through each registered setting.
					foreach ( $settings as $setting ) {

						// get the current value of this setting.
						$value = get_option( $setting['option_name'], '' );

						/**
						 * Fires an action which runs before the settings wrapper is output.
						 *
						 * @hooked hd_ssi_output_group_start_markup - 10
						 */
						do_action( 'hd_ssi_before_settings_wrapper', $setting, $value );

						// create a section data id.
						$section_id = '';

						// if this setting has a section.
						if ( ! empty( $setting['section'] ) ) {

							// store the section name.
							$section_id = $setting['section'];

						}

						?>

						<div class="hd-ssi-setting hd-ssi-setting-type--<?php echo esc_attr( $setting['input_type'] ); ?>" data-section-id="<?php echo esc_attr( str_replace( '_', '-', $section_id ) ); ?>">

							<div scope="row">

								<?php

								// if this is a checkbox field.
								if ( 'checkbox' === $setting['input_type'] ) {

									// output the label.
									?>
									<span class="label"><?php echo esc_html( $setting['label'] ); ?></span>
									<?php

								} elseif ( 'section' === $setting['input_type'] ) {
									

									// output the section heading.
									?>
									<h2 class="hd-ssi-section-heading" data-section-id="<?php echo str_replace( '_', '-', esc_attr( $setting['option_name'] ) ); ?>"><?php echo esc_html( $setting['label'] ); ?></h2>
									<?php

								} else { // setting type is not a checkbox.

									// output the field label.
									?>
									<label for="<?php echo esc_attr( $setting['option_name'] ); ?>"><?php echo esc_attr( $setting['label'] ); ?></label>
									<?php

								}
								
								/**
								 * Fire and action after the setting label.
								 *
								 * @hooked hd_ssi_add_top_tip_icon() - 10
								 */
								do_action( 'hd_si_after_setting_label', $setting, $value );

								?>

							</div>

							<?php

							// if the setting is not a section.
							if ( 'section' !== $setting['input_type'] ) {

								?>
								<div>

									<?php

									/**
									 * Fire a before setting action.
									 */
									do_action( 'hd_ssi_before_setting', $setting, $value );

									/**
									 * Create an action for this setting type
									 * functions for output can be hooked to it
									 */
									do_action( 'hd_ssi_setting_type_' . $setting['input_type'], $setting, $value );

									/**
									 * Fire a after setting action.
									 *
									 * @hookedhd_ssi_output_setting_descriptions - 10
									 */
									do_action( 'hd_ssi_after_setting', $setting, $value );

									?>

								</div>

								<?php

							}

							?>

						</div>

						<?php

						/**
						 * Fires an action which runs after the settings wrapper is output.
						 *
						 */
						do_action( 'hd_ssi_after_settings_wrapper', $setting, $value );

					}

					?>

					</div>

				<?php

			}

			?>

			<p class="submit">
				<input type="submit" name="wpbb_settings_submit" id="submit" class="button-primary" value="<?php echo esc_attr( 'Save Changes', 'wpbroadbean' ); ?>" />
			</p>

			<?php

			// Fires after the settings output table is closed.
			do_action( 'hd_ssi_after_settings_output' );

			?>

		</form>

		<?php

		// after the settings form output.
		do_action( 'hd_ssi_after_settings_form_output' );

		?>

	</div>

	<?php

}
