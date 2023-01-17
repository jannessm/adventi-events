<?php

include_once dirname(__FILE__) . '/utils.php';

add_action( 'ad_ev_settings_tab', 'ad_ev_section_map_tab', 1 );
function ad_ev_section_map_tab(){
	global $ad_ev_active_tab; ?>
	<a class="nav-tab <?php echo $ad_ev_active_tab == 'map' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('edit.php?post_type=event&page=adventi_events&tab=map'); ?>"><?php echo __('Orte', 'adventi_events'); ?> </a>
	<?php
}



add_action( 'ad_ev_settings_content', 'ad_ev_section_map_page' );
function ad_ev_section_map_page() {
	global $ad_ev_active_tab;
    $options = get_option( 'ad_ev_options' );
	
    if ( 'map' != $ad_ev_active_tab ) {
        ad_ev_settings_input('hidden', AD_EV_FIELD . 'church_location', '', '', '');
        ad_ev_settings_input('hidden', AD_EV_FIELD . 'church_lng', '', '', '');
        ad_ev_settings_input('hidden', AD_EV_FIELD . 'church_lat', '', '', '');
        ad_ev_settings_input('hidden', AD_EV_FIELD . 'graphhopper_api_key', '', '', '');
		return;
    }

    wp_localize_script(
        'leaflet-script-settings',
        'leaflet_options',
        array(
            'location' => $options[AD_EV_FIELD . 'church_location'],
            'location_lng' => $options[AD_EV_FIELD . 'church_lng'],
            'location_lat' => $options[AD_EV_FIELD . 'church_lat'],
            'input_id' => AD_EV_FIELD . 'church_location',
            'input_lng' => AD_EV_FIELD . 'church_lng',
            'input_lat' => AD_EV_FIELD . 'church_lat',
            'input_proposals_id' => 'location_proposals',
            'map_id' => 'location_prev',
            'graphhopper_api_key' => $options[AD_EV_FIELD . 'graphhopper_api_key']
        )
    );
    ?>

    <h3>Präsenzort</h3>

    <?php
        ad_ev_settings_input('text', AD_EV_FIELD . 'church_location', 'Adresse', 'z.B. Lucy-Lameck-Straße 27, 12049 Berlin', 'Adresse der Kirche');
    ?>
        <div id="location_proposals"></div><br>
    <?php
        ad_ev_settings_input('text', AD_EV_FIELD . 'church_lng', 'Longitude', 'z.B. 13.4206138', 'Longitude der Adresse (wird für die Kartenansicht genutzt)');
        ad_ev_settings_input('text', AD_EV_FIELD . 'church_lat', 'Latitude', 'z.B. 52.4827523', 'Latitude der Adresse (wird für die Kartenansicht genutzt)');
        ad_ev_settings_input('text', AD_EV_FIELD . 'graphhopper_api_key', 'Graphhopper API Key', 'API Key für Kartendarstellung und Routenplaner', 'Für die Kartenansicht wird die Graphhopper API benötigt');
    ?>

    <h3>Zoom</h4>
    <?php
        ad_ev_settings_input('text', AD_EV_FIELD . 'zoom_id', 'Meeting ID', 'z.B. 444 333 999', 'Standard Zoom Meeting');
        ad_ev_settings_input('text', AD_EV_FIELD . 'zoom_pwd', 'Passwort', 'z.B. 144 000', 'Passwort für das Meeting');
        ad_ev_settings_input('text', AD_EV_FIELD . 'zoom_tel', 'Tel.', 'z.B. +49 444 333 999', 'Nummer um telefonisch beizutreten');
        ad_ev_settings_input('text', AD_EV_FIELD . 'zoom_link', 'Link', 'z.B. https://zoom.us/tolles/meeting', 'Link um direkt beizutreten');
    ?>
	<?php
}

