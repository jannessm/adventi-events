<?php
// function adventi_events_settings_init() {
//     // add_submenu_page(

//     // )

//     // Register a new setting for "adventi_events" page
//     register_setting('adventi_events', 'adventi_events_options');

//     // new section
//     add_settings_section(
//         'adventi_events_section_general',
//         __('Generelle Einstellungen', 'adventi-events'),
//         'adventi_events_section_developers_callback',
// 		'adventi_events'
//     );

//     // Register a new field in the "adventi_events_general" section, inside the "adventi_events" page.
// 	add_settings_field(
// 		'adventi_events_church_name', // As of WP 4.6 this value is used only internally.
// 		                    // Use $args' label_for to populate the id inside the callback.
//         __( 'Name der Kirche', 'adventi_events' ),
// 		'adventi_events_church_name_cb',
// 		'adventi_events',
// 		'adventi_events_section_general',
// 		array(
// 			'label_for'         => 'adventi_events_church_name',
// 			'class'             => 'adventi_events_row'
// 		)
// 	);
// }
// add_action('admin_init', 'adventi_events_settings_init');

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
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
			'adventi_events_church_name' => 'church_name',
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
			'adventi_events_preacher_plan' => 'preacher_plan',
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
function adventi_events_field_preacher_plan_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'adventi_events_options' );
	?>
	<input
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ?>"
			name="adventi_events_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			placeholder="z.B. https://predigtplan.adventisten.de"
			style="min-width:300px;">
	<p class="description">
		<?php esc_html_e( 'Adresse des Predigtplans', 'advent-events' ); ?>
	</p>
	<?php
}

/**
 * Add the top level menu page.
 */
function adventi_events_options_page() {
	add_menu_page(
		'Adventi Events',
		'Adventi Events',
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