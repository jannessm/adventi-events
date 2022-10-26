<?php

function adventi_event_meta_boxes( $meta_boxes ){
	$pre = 'adventi-event-meta-';

    $meta_boxes[] = array(
        'title'      => __( 'Nähere Informationen', 'de' ),
        'post_types' => 'event',
        'fields'     => array(
            array(
                'name'  => esc_html__( 'Datum', 'de' ),
				'id'    => $pre."date",
				'type'  => 'datetime'
            ),
            array(
            	'name' => esc_html__('Adresse', 'de'),
            	'id' => $pre."location",
            	'type' => 'text',
            	'std' => 'Pfarrstr. 132, 12437 Berlin'
            ),
            array(
				'id'            => 'map',
				'name'          => __( 'Ort', 'de' ),
				'type'          => 'map',
				// Default location: 'latitude,longitude[,zoom]' (zoom is optional)
				'std'           => '52.50397,13.473486,17',
				// Name of text field where address is entered. Can be list of text fields, separated by commas (for ex. city, state)
				'address_field' => 'Adresse',
				'api_key'       => ' AIzaSyDzQJ0W3_dNymeZqaSLHMo5qRS5INj66uI', // https://metabox.io/docs/define-fields/#section-map
            ),
			array(
				'name'  => esc_html__('Bild', 'de'),
				'id'    => $pre.'img',
				'type' => 'image_advanced',
                'max_file_uploads' => '1'
			),
			array(
                'name'  => esc_html__( 'Ist dies eine besondere Veranstaltung?', 'de' ),
				'id'    => $pre.'is-special',
				'type'  => 'checkbox'
            )
        ),
    );
    return $meta_boxes;
}