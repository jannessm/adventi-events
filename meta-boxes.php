<?php

abstract class Adventi_Events_Meta_Box {

	private const meta_keys = ['date', 'location', 'location_point', 'hash', 'is_special', 'image'];

	/**
	 * Set up and add the meta box.
	 */
	public static function add() {
		$screens = [ 'event' ];
		foreach ( $screens as $screen ) {
			add_meta_box(
				'adventi_events_meta_box',  // Unique ID
				'Adventi Event', 			// Box title
				[ self::class, 'html' ],    // Content callback, must be of type callable
				$screen                     // Post type
			);
		}
	}


	/**
	 * Save the meta box selections.
	 *
	 * @param int $post_id  The post ID.
	 */
	public static function save( int $post_id ) {
		foreach (self::meta_keys as $key) {
			$name = '_adventi_events_meta_' . $key;
			if ( array_key_exists( $name, $_POST ) ) {
				update_post_meta(
					$post_id,
					$name,
					$_POST[$name]
				);
			}
		}
	}


	/**
	 * Display the meta box HTML to the user.
	 *
	 * @param \WP_Post $post   Post object.
	 */
	public static function html( $post ) {
		$options = get_option( 'adventi_events_options' );

		$date = get_post_meta( $post->ID, '_adventi_events_meta_date', true );
		$location = get_post_meta( $post->ID, '_adventi_events_meta_location', true );
		$location_point = get_post_meta( $post->ID, '_adventi_events_meta_location_point', true );
		$is_special = get_post_meta( $post->ID, '_adventi_events_meta_is_special', true ) === "true";

		$default_point = '';
		if (isset($options['adventi_events_field_church_long']) && isset($options['adventi_events_field_church_lat'])) {
			$default_point = '['.$options['adventi_events_field_church_long'].','.$options['adventi_events_field_church_lat'].']';
		}

		$location = $location !== '' ? $location : $options['adventi_events_field_church_location'];
		$location_point = $location_point !== '' ? $location_point : $default_point;

		wp_localize_script(
			'leaflet-script',
			'leaflet_options',
			array(
				'location' => $location,
				'location_point' => $location_point,
				'input_id' => 'location_input',
				'input_point_id' => 'location_point',
				'input_proposals_id' => 'location_proposals',
				'map_id' => 'location_prev',
				'graphhopper_api_key' => $options['adventi_events_field_graphhopper_api_key']
			)
		);

		Adventi_Events_Meta_Box::get_image_selector( $post );
		
		?>

		<label for="_adventi_events_meta_date" class="adventi_events_meta_box">Datum</label>
		<input type="datetime-local" name="_adventi_events_meta_date" value="<?php echo !$date; ?>" class="adventi_events_meta_box">
		<br>

		<label for="_adventi_events_meta_location" class="adventi_events_meta_box">Ort</label>
		<input id="location_input" name="_adventi_events_meta_location" value="<?php echo $location; ?>" class="adventi_events_meta_box">
		<input id="location_point" name="_adventi_events_meta_location_point" value="<?php echo $location_point; ?>" type="hidden">
		<div id="location_proposals" class="adventi_events_meta_box"></div>
		<br>

		<label for="_adventi_events_meta_is_special" class="adventi_events_meta_box">Besondere Veranstaltung</label>
		<input type="checkbox" name="_adventi_events_meta_is_special" <?php checked($is_special); ?> value="true">

		<div id="location_prev" class="adventi_events_meta_box"></div>
		<?php
	}


	static function get_image_selector( $post ) {
		
		$image_id = get_post_meta( $post->ID, '_adventi_events_meta_image', true );

		wp_localize_script(
			'image-select',
			'args',
			array(
				'image_id' => $image_id,
				'image_container' => "#img-preview-container"
			)
		);
		
		// Get WordPress' media upload URL
		$upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );
		
		// Get the image src
		$image_src = wp_get_attachment_image_src( $image_id );

		// For convenience, see if the array is valid
		$is_image = is_array( $image_src );
		?>

		<label for="_adventi_events_meta_image" class="adventi_events_meta_box">Titelbild</label>
		<div class="adventi_events_meta_box" style="display: inline-block">
			<!-- Your image container, which can be manipulated with js -->
			<div id="img-preview-container" style="max-width:300px !important">
				<?php if ( $is_image ) : ?>
					<img src="<?php echo $image_src[0] ?>" alt="" style="" />
				<?php endif; ?>
			</div>

			<!-- Your add & remove image links -->
			<p class="hide-if-no-js">
				<a class="upload-custom-img <?php if ( $is_image  ) { echo 'hidden'; } ?>" 
				href="<?php echo $upload_link ?>">
					<?php _e('Set custom image') ?>
				</a>
				<a class="delete-custom-img <?php if ( ! $is_image  ) { echo 'hidden'; } ?>" 
				href="#">
					<?php _e('Remove this image') ?>
				</a>
			</p>

			<!-- A hidden input to set and post the chosen image id -->
			<input class="image-id" name="_adventi_events_meta_image" type="hidden" value="<?php echo esc_attr( $image_id ); ?>">
		</div>
		<br>
		
		<?php
	}
}