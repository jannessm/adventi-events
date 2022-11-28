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
	
    if ( 'text' != $ad_ev_active_tab ) {
		ad_ev_settings_textarea(AD_EV_FIELD . 'default_text', 'Standard Beschreibung', 'z.B. ', '', TRUE);
		ad_ev_settings_textarea(AD_EV_FIELD . 'default_communion_text', '', '', '', TRUE);
		ad_ev_settings_textarea(AD_EV_FIELD . 'default_thanks_giving_text', '', '', '', TRUE);
		ad_ev_settings_textarea(AD_EV_FIELD . 'default_baptism_text', '', '', '', TRUE);
		ad_ev_settings_textarea(AD_EV_FIELD . 'default_youth_service_text', '', '', '', TRUE);
		ad_ev_settings_textarea(AD_EV_FIELD . 'default_community_hour_text', '', '', '', TRUE);
		ad_ev_settings_textarea(AD_EV_FIELD . 'default_forest_service_text', '', '', '', TRUE);
		return;
    }

    ?>
 
	<h3><?php __( 'Standard Texte', 'adventi_events' ); ?></h3>
	
    <?php
	$example_text = '[ad_ev_header]

Wir feiern Gottesdienst mit neuen und alten Liedern, mit einer Gesprächsrunde über den Glauben und mit einer Predigt.
	
[ad_ev_map]';
    ad_ev_settings_textarea(AD_EV_FIELD . 'default_text', 'Standard Beschreibung', 'z.B. ' . $example_text, '', FALSE);
    ad_ev_settings_textarea(AD_EV_FIELD . 'default_communion_text', 'Abendmahl Beschreibung', 'z.B. ' . $example_text, '', FALSE);
    ad_ev_settings_textarea(AD_EV_FIELD . 'default_thanks_giving_text', 'Erntedank Beschreibung', 'z.B. ' . $example_text, '', FALSE);
    ad_ev_settings_textarea(AD_EV_FIELD . 'default_baptism_text', 'Taufgottesdienst Beschreibung', 'z.B. ' . $example_text, '', FALSE);
    ad_ev_settings_textarea(AD_EV_FIELD . 'default_youth_service_text', 'Jugendgottesdienst Beschreibung', 'z.B. ' . $example_text, '', FALSE);
    ad_ev_settings_textarea(AD_EV_FIELD . 'default_community_hour_text', 'Gemeindestunde Beschreibung', 'z.B. ' . $example_text, '', FALSE);
    ad_ev_settings_textarea(AD_EV_FIELD . 'default_forest_service_text', 'Waldgottesdienst Beschreibung', 'z.B. ' . $example_text, '', FALSE);
}

