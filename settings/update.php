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
	?>
		<a class="button" onclick="update(event)">Manuelles Update</a>
		<div id="adventi-events-dates"></div>
	<?php
}

