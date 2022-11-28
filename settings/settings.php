<?php

include_once dirname(__FILE__) . '/general.php';
include_once dirname(__FILE__) . '/map.php';
include_once dirname(__FILE__) . '/update.php';
include_once dirname(__FILE__) . '/texts.php';

function ad_ev_settings_init() {
	register_setting( 'adventi_events', 'ad_ev_options' );

	add_settings_section(
		'ad_ev_section_general',
		'',
        'ad_ev_section_wrapper_callback',
		'adventi_events'
	);
}
add_action( 'admin_init', 'ad_ev_settings_init' );

function ad_ev_section_wrapper_callback() {
    global $ad_ev_active_tab;
	$ad_ev_active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

	?>
 
	<h2 class="nav-tab-wrapper">
	<?php
		do_action( 'ad_ev_settings_tab' );
	?>
	</h2>
	<?php
		do_action( 'ad_ev_settings_content' );
}

/**
 * Add the top level menu page.
 */
function ad_ev_options_page() {
	add_submenu_page(
		'edit.php?post_type=event',
		__('Adventi Events', 'adventi-events'),
		__('Einstellungen', 'adventi-events'),
		'manage_options',
		'adventi_events',
		'ad_ev_options_page_html'
	);
}


/**
 * Register our ad_ev_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'ad_ev_options_page' );


/**
 * Top level menu callback function
 */
function ad_ev_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'ad_ev_message', 'ad_ev_message', __( 'Einstellungen gespeichert!', 'adventi-events' ), 'updated' );
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