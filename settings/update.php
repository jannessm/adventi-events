<?php

include_once dirname(__FILE__) . '/utils.php';

add_action( 'ad_ev_settings_tab', 'ad_ev_section_update_tab', 1 );
function ad_ev_section_update_tab(){
	global $ad_ev_active_tab; ?>
	<a class="nav-tab <?php echo $ad_ev_active_tab == 'update' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('edit.php?post_type=event&page=adventi_events&tab=update'); ?>"><?php echo __('Aktualisierung', 'adventi_events'); ?> </a>
	<?php
}



add_action( 'ad_ev_settings_content', 'ad_ev_section_update_page' );
function ad_ev_section_update_page() {
	global $ad_ev_active_tab;
    $options = get_option( 'ad_ev_options' );

    if ( 'update' != $ad_ev_active_tab ) {
		ad_ev_settings_input('hidden', AD_EV_FIELD . 'cron', '', '', '');
		ad_ev_settings_input('hidden', AD_EV_FIELD . 'cron_mail', '', '', '');
		return;
    }

    ?>
 
	<h3><?php __( 'Aktualisierung', 'adventi_events' ); ?></h3>

    <?php
    $update_nonce = wp_create_nonce( 'update_events' );
	
	wp_localize_script(
		'update-script',
		'ajax_obj',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => $update_nonce,
		)
	);

	ad_ev_settings_input('checkbox', AD_EV_FIELD . 'cron', 'Automatisches Update', '', 'wöchentliches Update');
	ad_ev_settings_input('email', AD_EV_FIELD . 'cron_mail', 'Email', '', 'Update Bericht wird an diese Mail gesendet.');
	?>
		<a class="button" onclick="update(event)">Manuelles Update</a>
		<a class="button" onclick="delete_all_services(event)">Alle Gottesdienste löschen</a>
		<div id="adventi-events-dates" style="white-space: pre;"></div>
	<?php
}

