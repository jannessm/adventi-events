<?php

add_action( 'ad_ev_settings_tab', 'ad_ev_section_doc_tab', 1 );
function ad_ev_section_doc_tab(){
	global $ad_ev_active_tab; ?>
	<a class="nav-tab <?php echo $ad_ev_active_tab == 'doc' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('edit.php?post_type=event&page=adventi_events&tab=doc'); ?>"><?php echo __('Shortcodes', 'adventi_events'); ?> </a>
	<?php
}



add_action( 'ad_ev_settings_content', 'ad_ev_section_doc_page' );
function ad_ev_section_doc_page() {
	global $ad_ev_active_tab;
    $options = get_option( 'ad_ev_options' );

    if ( 'doc' != $ad_ev_active_tab ) {
		return;
    }

    ?>

	<p>Informationen über verfügbare Shortcodes</p>

	<h3>[ad_ev_header]</h3>
	<p>gibt alle Information in der folgenden Reihenfolge aus:</p>
	<div style="background: white;">
		<p><b>Datum:</b>    01.01.2022</p>
		<p><b>Prediger:</b> T. Wilson</p>
		<p><b>Ort:</b>      Lucy-Lameck-Straße 27, 12049 Berlin</p>
	</div>
	<p>Für weitere Anspassungen, siehe <b>[ad_ev_date]</b>, <b>[ad_ev_preacher]</b>, <b>[ad_ev_location]</b></p><br><br>

	<h3>[ad_ev_map height="300px"]</h3>
	<p>Zeigt eine OpenStreetMap für den angegebenen Ort. Mit dem height Attribut kann die Höhe verändert werden.</p><br><br>

	<h3>[ad_ev_date label=TRUE]</h3>
	<p>gibt alle Information in der folgenden Reihenfolge aus:</p>
	<div style="background: white;">
		<p><b>Datum:</b>    01.01.2022</p>
	</div>
	<p>Mit label=FALSE kann die Beschreibung auch weggelassen werden.</p><br><br>

	<h3>[ad_ev_preacher label=TRUE]</h3>
	<p>gibt alle Information in der folgenden Reihenfolge aus:</p>
	<div style="background: white;">
		<p><b>Prediger:</b> T. Wilson</p>
	</div>
	<p>Mit label=FALSE kann die Beschreibung auch weggelassen werden.</p><br><br>

	<h3>[ad_ev_location label=TRUE]</h3>
	<p>gibt alle Information in der folgenden Reihenfolge aus:</p>
	<div style="background: white;">
		<p><b>Ort:</b>      Lucy-Lameck-Straße 27, 12049 Berlin</p>
	</div>
	<p>Mit label=FALSE kann die Beschreibung auch weggelassen werden.</p><br><br>

	
	<?php
}

