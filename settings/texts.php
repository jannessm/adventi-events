<?php

include_once dirname(__FILE__) . '/utils.php';

add_action( 'ad_ev_settings_tab', 'ad_ev_section_text_tab', 1 );
function ad_ev_section_text_tab(){
	global $ad_ev_active_tab; ?>
	<a class="nav-tab <?php echo $ad_ev_active_tab == 'text' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('edit.php?post_type=event&page=adventi_events&tab=text'); ?>"><?php echo __('Standard Texte', 'adventi_events'); ?> </a>
	<?php
}



add_action( 'ad_ev_settings_content', 'ad_ev_section_text_page' );
function ad_ev_section_text_page() {
	global $ad_ev_active_tab;
    $options = get_option( 'ad_ev_options' );

	$option_labels = [
		'default_text' => 'Normaler Gottesdienst',
		'default_communion_text' => 'Abendmahl Gottesdienst',
		'default_thanks_giving_text' => 'Erntedank Gottesdienst',
		'default_baptism_text' => 'Taufgottesdienst',
		'default_youth_service_text' => 'Jugendgottesdienst',
		'default_community_hour_text' => 'Gottesdienst mit Gemeindestunde',
		'default_forest_service_text' => 'Waldgottesdienst'];

	$image_ids = [];

	foreach($option_labels as $key => $label) {
		$image_ids[$key] = !!$options[AD_EV_FIELD . $key . '_img'] ? $options[AD_EV_FIELD . $key . '_img'] : '';
	}
	
    if ( 'text' != $ad_ev_active_tab ) {
		foreach($option_labels as $key => $label) {
			ad_ev_image_selector($image_ids[$key], '', AD_EV_FIELD . $key . '_img', '', true);
			ad_ev_settings_textarea(AD_EV_FIELD . $key, '', '', '', TRUE);
		}
		return;
    }

    ?>
 
	<h3><?php __( 'Standard Texte', 'adventi_events' ); ?></h3>
	
    <?php
	$example_text = '[ad_ev_header]

Wir feiern Gottesdienst mit neuen und alten Liedern, mit einer Gesprächsrunde über den Glauben und mit einer Predigt.
	
[ad_ev_map]';

	foreach($option_labels as $key => $label) {
		ad_ev_image_selector($image_ids[$key], $label . ' Bild', AD_EV_FIELD . $key . '_img', '', $hidden=FALSE);
		ad_ev_settings_textarea(AD_EV_FIELD . $key, $label, 'z.B. ' . $example_text, '', FALSE);
	}
}

