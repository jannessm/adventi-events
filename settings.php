<?php

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

function adventi_events_settings_init() {
	// Register a new setting for "wporg" page.
	register_setting( 'adventi_events', 'adventi_events_options' );

	// Register a new section in the "wporg" page.
	add_settings_section(
		'adventi_events_section_general',
		__( 'Generelle Einstellungen', 'adventi-events' ),
        'adventi_events_section_general_callback',
		'adventi_events'
	);

	// Register a new section in the "wporg" page.
	add_settings_section(
		'adventi_events_section_graphhopper',
		__( 'Karten', 'adventi-events' ),
        'adventi_events_section_graphhopper_callback',
		'adventi_events'
	);

	// Register a new section in the "wporg" page.
	add_settings_section(
		'adventi_events_section_reload_data',
		__( 'Aktualisierung der Daten', 'adventi-events' ),
        'adventi_events_section_reload_data_callback',
		'adventi_events'
	);

	// Register a new field in the "adventi_events_section_general" section, inside the "wporg" page.
	add_settings_field(
		'adventi_events_field_church_name', // As of WP 4.6 this value is used only internally.
		                        // Use $args' label_for to populate the id inside the callback.
		__( 'Kirche', 'advent-events' ),
		'adventi_events_field_church_name_cb',
		'adventi_events',
		'adventi_events_section_general',
		array(
			'label_for'         => 'adventi_events_field_church_name',
			'class'             => 'adventi_events_row',
		)
	);

	// Register a new field in the "adventi_events_section_general" section, inside the "wporg" page.
	add_settings_field(
		'adventi_events_field_service_start', // As of WP 4.6 this value is used only internally.
		                        // Use $args' label_for to populate the id inside the callback.
		__( 'Kirche', 'advent-events' ),
		'adventi_events_field_service_start_cb',
		'adventi_events',
		'adventi_events_section_general',
		array(
			'label_for'         => 'adventi_events_field_service_start',
			'class'             => 'adventi_events_row',
		)
	);

	// Register a new field in the "adventi_events_section_general" section, inside the "wporg" page.
	add_settings_field(
		'adventi_events_field_default_image', // As of WP 4.6 this value is used only internally.
		                        // Use $args' label_for to populate the id inside the callback.
		__( 'Kirche', 'advent-events' ),
		'adventi_events_field_default_image_cb',
		'adventi_events',
		'adventi_events_section_general',
		array(
			'label_for'         => 'adventi_events_field_default_image',
			'class'             => 'adventi_events_row',
		)
	);
	
	// Register a new field in the "adventi_events_section_general" section, inside the "wporg" page.
	add_settings_field(
		'adventi_events_field_church_location', // As of WP 4.6 this value is used only internally.
		                        // Use $args' label_for to populate the id inside the callback.
		__( 'Adresse', 'advent-events' ),
		'adventi_events_field_church_location_cb',
		'adventi_events',
		'adventi_events_section_graphhopper',
		array(
			'label_for'         => 'adventi_events_field_church_location',
			'class'             => 'adventi_events_row',
		)
	);
	// Register a new field in the "adventi_events_section_general" section, inside the "wporg" page.
	add_settings_field(
		'adventi_events_field_church_long', // As of WP 4.6 this value is used only internally.
		                        // Use $args' label_for to populate the id inside the callback.
		__( 'Longitude', 'advent-events' ),
		'adventi_events_field_church_long_cb',
		'adventi_events',
		'adventi_events_section_graphhopper',
		array(
			'label_for'         => 'adventi_events_field_church_long',
			'class'             => 'adventi_events_row',
		)
	);
	// Register a new field in the "adventi_events_section_general" section, inside the "wporg" page.
	add_settings_field(
		'adventi_events_field_church_lat', // As of WP 4.6 this value is used only internally.
		                        // Use $args' label_for to populate the id inside the callback.
		__( 'Latitude', 'advent-events' ),
		'adventi_events_field_church_lat_cb',
		'adventi_events',
		'adventi_events_section_graphhopper',
		array(
			'label_for'         => 'adventi_events_field_church_lat',
			'class'             => 'adventi_events_row',
		)
	);
	
	// Register a new field in the "adventi_events_section_general" section, inside the "wporg" page.
	add_settings_field(
		'adventi_events_field_graphhopper_api_key', // As of WP 4.6 this value is used only internally.
		                        // Use $args' label_for to populate the id inside the callback.
		__( 'Graphhopper API Key', 'advent-events' ),
		'adventi_events_field_graphhopper_api_key_cb',
		'adventi_events',
		'adventi_events_section_graphhopper',
		array(
			'label_for'         => 'adventi_events_field_graphhopper_api_key',
			'class'             => 'adventi_events_row',
		)
	);

	// Register a new field in the "adventi_events_section_general" section, inside the "wporg" page.
	add_settings_field(
		'adventi_events_field_preacher_plan', // As of WP 4.6 this value is used only internally.
		                        // Use $args' label_for to populate the id inside the callback.
		__( 'Predigtplan-Adresse', 'advent-events' ),
		'adventi_events_field_preacher_plan_cb',
		'adventi_events',
		'adventi_events_section_general',
		array(
			'label_for'         => 'adventi_events_field_preacher_plan',
			'class'             => 'adventi_events_row',
		)
	);
}

/**
 * Register our wporg_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'adventi_events_settings_init' );


/**
 * Custom option and settings:
 *  - callback functions
 */


/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function adventi_events_section_general_callback( $args ) { }
function adventi_events_section_graphhopper_callback( $args ) { }

function adventi_events_section_reload_data_callback( $args ) {

	$update_nonce = wp_create_nonce( 'update_events' );
	
	wp_localize_script(
		'update-script',
		'ajax_obj',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => $update_nonce,
		)
	);
	?>
		<a class="button" onclick="update(event)">Test</a>
		<div id="adventi-events-dates"></div>
	<?php
}


function adventi_events_field_church_name_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'adventi_events_options' );
	?>
	<input
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ?>"
			name="adventi_events_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			placeholder="z.B. Berlin-an der Hasenheide"
			style="min-width:300px;">
	<p class="description">
		<?php esc_html_e( 'Kirchenname, wie auf dem Predigtplan angegeben', 'advent-events' ); ?>
	</p>
	<?php
}

/**
 * Pill field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function adventi_events_field_service_start_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'adventi_events_options' );
	?>
	<input
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ?>"
			name="adventi_events_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			type="time"
			placeholder="10:30"
			style="min-width:300px;">
	<p class="description">
		<?php esc_html_e( 'Wann beginnt der Gottesdienst normalerweise', 'advent-events' ); ?>
	</p>
	<?php
}

function adventi_events_field_default_image_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'adventi_events_options' );

	$image_id = isset($options['adventi_events_field_default_image']) ? $options['adventi_events_field_default_image'] : '';

	wp_localize_script(
		'image-select',
		'args',
		array(
			'image_id' => $image_id,
			'image_container' => "#img-preview-container"
		)
	);

	// Get WordPress' media upload URL
	$upload_link = esc_url( get_upload_iframe_src( 'image' ) );
		
	// Get the image src
	$image_src = wp_get_attachment_image_src( $image_id );

	// For convenience, see if the array is valid
	$is_image = is_array( $image_src );
	?>

	<div style="display: inline-block">
		<!-- Your image container, which can be manipulated with js -->
		<div id="img-preview-container" style="max-width:300px !important">
			<?php if ( $is_image ) : ?>
				<img src="<?php echo $image_src[0] ?>" alt="" style="" />
			<?php endif; ?>
		</div>

		<!-- Your add & remove image links -->
		<p class="hide-if-no-js">
			<a class="upload-custom-img <?php if ( $is_image  ) { echo 'hidden'; } ?>" 
			href="<?php echo $upload_link ?>">
				<?php _e('Set image') ?>
			</a>
			<a class="delete-custom-img <?php if ( ! $is_image  ) { echo 'hidden'; } ?>" 
			href="#">
				<?php _e('Remove this image') ?>
			</a>
		</p>

		<!-- A hidden input to set and post the chosen image id -->
		<input id="<?php echo esc_attr( $args['label_for'] ); ?>" class="image-id" name="adventi_events_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="hidden" value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ?>">
		
		<p class="description">
			<?php esc_html_e( 'Standard Bild', 'advent-events' ); ?>
		</p>
	</div>
	<br>
	
	<?php
}

/**
 * Pill field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function adventi_events_field_church_location_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'adventi_events_options' );
	?>
	<input
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ?>"
			name="adventi_events_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			placeholder="z.B. Lucy-Lameck-Straße 27, 12049 Berlin"
			style="min-width:300px;">
	<p class="description">
		<?php esc_html_e( 'Adresse der Kirche', 'advent-events' ); ?>
	</p>
	<?php
}

/**
 * Pill field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function adventi_events_field_church_long_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'adventi_events_options' );
	?>
	<input
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ?>"
			name="adventi_events_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			placeholder="z.B. 13.4206138"
			style="min-width:300px;">
	<p class="description">
		<?php esc_html_e( 'Longitude der Adresse (wird für die Kartenansicht genutzt)', 'advent-events' ); ?>
	</p>
	<?php
}
/**
 * Pill field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function adventi_events_field_church_lat_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'adventi_events_options' );
	?>
	<input
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ?>"
			name="adventi_events_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			placeholder="z.B. 52.4827523"
			style="min-width:300px;">
	<p class="description">
		<?php esc_html_e( 'Latitude der Adresse (wird für die Kartenansicht genutzt)', 'advent-events' ); ?>
	</p>
	<?php
}
/**
 * Pill field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function adventi_events_field_graphhopper_api_key_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'adventi_events_options' );
	?>
	<input
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ?>"
			name="adventi_events_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			placeholder="API Key für Kartendarstellung und Routenplaner"
			style="min-width:300px;">
	<p class="description">
		<a href="graphhopper.com"><?php esc_html_e( 'Für die Kartenansicht wird die Graphhopper API benötigt', 'advent-events' ); ?></a>
	</p>
	<?php
}
/**
 * Pill field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function adventi_events_field_preacher_plan_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'adventi_events_options' );
	?>
	<input
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : 'https://predigtplan.adventisten.de' ?>"
			placeholder="z.B. https://predigtplan.adventisten.de"
			style="min-width:300px;" disabled>
	<input
			type="hidden"
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			value="https://predigtplan.adventisten.de"
			name="adventi_events_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			placeholder="z.B. https://predigtplan.adventisten.de">
	<p class="description">
		<?php esc_html_e( 'Adresse des Predigtplans', 'advent-events' ); ?>
	</p>
	<?php
}

/**
 * Add the top level menu page.
 */
function adventi_events_options_page() {
	add_submenu_page(
		'edit.php?post_type=event',
		__('Adventi Events', 'adventi-events'),
		__('Einstellungen', 'adventi-events'),
		'manage_options',
		'adventi_events',
		'adventi_events_options_page_html'
	);
}


/**
 * Register our adventi_events_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'adventi_events_options_page' );


/**
 * Top level menu callback function
 */
function adventi_events_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'adventi_events_message', 'adventi_events_message', __( 'Einstellungen gespeichert!', 'adventi-events' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'adventi_event_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "adventi_events"
			settings_fields( 'adventi_events' );
			// output setting sections and their fields
			// (sections are registered for "wporg", each field is registered to a specific section)
			do_settings_sections( 'adventi_events' );
			// output save settings button
			submit_button( 'Speichern' );
			?>
		</form>
	</div>
	<?php
}