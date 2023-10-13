<?php

include_once dirname(__FILE__) . '/utils.php';

add_action( 'ad_ev_settings_tab', 'ad_ev_section_general_tab', 1 );
function ad_ev_section_general_tab(){
	global $ad_ev_active_tab; ?>
	<a class="nav-tab <?php echo $ad_ev_active_tab == 'general' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('edit.php?post_type=event&page=adventi_events&tab=general'); ?>"><?php echo __('Allgemeine Einstellungen', 'adventi_events'); ?> </a>
	<?php
}



add_action( 'ad_ev_settings_content', 'ad_ev_section_general_page' );
function ad_ev_section_general_page() {
	global $ad_ev_active_tab;

    $options = get_option( 'ad_ev_options' );
	$image_id = isset($options[ AD_EV_FIELD . 'default_image']) ? $options[AD_EV_FIELD . 'default_image'] : '';
	$image_id1 = isset($options[ AD_EV_FIELD . 'default_image_1']) ? $options[AD_EV_FIELD . 'default_image_1'] : '';
	$image_id2 = isset($options[ AD_EV_FIELD . 'default_image_2']) ? $options[AD_EV_FIELD . 'default_image_2'] : '';

    if ( '' || 'general' != $ad_ev_active_tab ) {

        ad_ev_settings_input('hidden', AD_EV_FIELD . 'church_name', 'Kirche', 'z.B. Berlin-an der Hasenheide', 'Kirchenname, wie auf dem Predigtplan angegeben');
        ad_ev_settings_input('hidden', AD_EV_FIELD . 'preacher_plan', 'Predigtplan', 'z.B. https://predigtplan.adventisten.de', 'Predigtplan URL');
        ad_ev_settings_input('hidden', AD_EV_FIELD . 'service_start', 'Gottesdienst Beginn', '10:00', 'Wann beginnt der Gottesdienst i.d.R.');
        ad_ev_image_selector($image_id, 'Standard Bild 1', AD_EV_FIELD . 'default_image', '', TRUE);
        ad_ev_image_selector($image_id1, 'Standard Bild 2', AD_EV_FIELD . 'default_image_1', '', TRUE);
        ad_ev_image_selector($image_id2, 'Standard Bild 3', AD_EV_FIELD . 'default_image_2', '', TRUE);
        
		return;
    }
    ?>
 
 <h3><?php __( 'Allgemeine Einstellungen', 'adventi_events' ); ?></h3>
 
 <?php
    echo '<p>Predigtplan unter <a href="https://predigtplan.adventisten.de">https://predigtplan.adventisten.de</a></p>';

    ad_ev_settings_input('text', AD_EV_FIELD . 'church_name', 'Kirche', 'z.B. Berlin-an der Hasenheide', 'Kirchenname, wie auf dem Predigtplan angegeben');
    ad_ev_settings_input('hidden', AD_EV_FIELD . 'preacher_plan', 'Predigtplan', 'z.B. https://predigtplan.adventisten.de', 'Predigtplan URL');
    ad_ev_settings_input('time', AD_EV_FIELD . 'service_start', 'Gottesdienst Beginn', '10:00', 'Wann beginnt der Gottesdienst i.d.R.');
    ad_ev_image_selector($image_id, 'Standard Bild 1', AD_EV_FIELD . 'default_image', '');
    ad_ev_image_selector($image_id1, 'Standard Bild 2', AD_EV_FIELD . 'default_image_1', '');
    ad_ev_image_selector($image_id2, 'Standard Bild 3', AD_EV_FIELD . 'default_image_2', '');
    ?>


	<?php
}

