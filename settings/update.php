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

    if (!isset($options[AD_EV_FIELD . 'api_secret']) || $options[AD_EV_FIELD . 'api_secret'] === '') {
        $options[AD_EV_FIELD . 'api_secret'] = wp_generate_password(40, false, false);
        update_option('ad_ev_options', $options);
    }

    if ( 'update' != $ad_ev_active_tab ) {
		ad_ev_settings_input('hidden', AD_EV_FIELD . 'cron', '', '', '');
		ad_ev_settings_input('hidden', AD_EV_FIELD . 'cron_mail', '', '', '');
        ad_ev_settings_input('hidden', AD_EV_FIELD . 'api_secret', '', '', '');
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
            'events_api_url' => esc_url_raw(rest_url('adventi-events/v1/imported-events')),
            'api_secret_input_id' => AD_EV_FIELD . 'api_secret',
		)
	);

	ad_ev_settings_input('checkbox', AD_EV_FIELD . 'cron', 'Automatisches Update', '', 'wöchentliches Update');
	ad_ev_settings_input('email', AD_EV_FIELD . 'cron_mail', 'Email', '', 'Update Bericht wird an diese Mail gesendet.');
    ad_ev_settings_input('hidden', AD_EV_FIELD . 'api_secret', '', '', '');
	?>
		<a class="button" onclick="update(event)">Manuelles Update</a>
		<a class="button" onclick="import_csv_events(event)">CSV importieren</a>
        <input id="ad-ev-csv-file" type="file" accept=".csv,text/csv" style="margin-left: 10px;">
		<a class="button" onclick="delete_all_services(event)">Alle Gottesdienste löschen</a>
		<div id="adventi-events-dates" style="white-space: pre;"></div>
        <hr>
        <h3>API Zugriff</h3>
        <label for="<?php echo esc_attr(AD_EV_FIELD . 'api_secret'); ?>" style="display: inline-block; width: 200px; vertical-align: top;">API Secret</label>
        <div style="display: inline-block;">
            <input id="<?php echo esc_attr(AD_EV_FIELD . 'api_secret'); ?>" type="text" value="<?php echo esc_attr($options[AD_EV_FIELD . 'api_secret']); ?>" readonly style="min-width:300px;">
            <a class="button" onclick="copy_api_secret(event)">Secret kopieren</a>
            <p class="description">Endpoint: <?php echo esc_html(rest_url('adventi-events/v1/imported-events')); ?> (?ad_ev_secr, Query: page, per_page)</p>
        </div><br>
	<?php
}
