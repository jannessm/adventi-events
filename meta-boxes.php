<?php

include_once dirname(__FILE__) . '/constants.php';
include_once dirname(__FILE__) . '/event.php';

abstract class AdventiEventsMetaBox {
	/**
	 * Set up and add the meta box.
	 */
	public static function add() {
		add_meta_box(
			'adventi_events_meta_box',  // Unique ID
			'Adventi Event', 			// Box title
			[ self::class, 'html' ],    // Content callback, must be of type callable
			'event'                     // Post type
		);
	}


	/**
	 * Save the meta box selections.
	 *
	 * @param int $post_id  The post ID.
	 */
	public static function save( int $post_id ) {
		foreach (AdventiEvent::fields as $key) {
			$name = AD_EV_META . $key;
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
		$event = AdventiEvent::from_post($post->ID);

		wp_localize_script(
			'leaflet-script',
			'leaflet_options',
			array(
				'location' => $event->location->address,
				'location_point' => $event->location->point_str(),
				'input_id' => 'location_input',
				'input_point_id' => 'location_point',
				'input_proposals_id' => 'location_proposals',
				'map_id' => 'location_prev',
				'graphhopper_api_key' => $options[AD_EV_FIELD . 'graphhopper_api_key']
			)
		);

		self::get_image_selector( $post, $event->image_id );
		
		?>
		<label for="<?php echo AD_EV_META; ?>preacher" class="adventi_events_meta_box">Prediger</label>
		<input name="<?php echo AD_EV_META; ?>preacher" value="<?php echo $event->preacher; ?>" class="adventi_events_meta_box">
		<br>

		<label for="<?php echo AD_EV_META; ?>date" class="adventi_events_meta_box">Datum</label>
		<input type="datetime-local" name="<?php echo AD_EV_META; ?>date" value="<?php echo $event->date->format('Y-m-d\\TH:i'); ?>" class="adventi_events_meta_box">
		<br>

		<label for="<?php echo AD_EV_META; ?>recurrence" class="adventi_events_meta_box">Wiederkehrende Veranstaltung</label>
		<select type="datetime-local" name="<?php echo AD_EV_META; ?>recurrence" value="<?php echo $event->recurrence; ?>" class="adventi_events_meta_box">
			<?php
				foreach(AdventiEventsIntervals::values() as $value) {
					?>
						<option value="<?php echo $value;?>" <?php echo $value === $event->recurrence ? 'selected' : '' ?>><?php echo $value;?></option>
					<?php
				}
				?>
		</select>
		<br>

		<label for="<?php echo AD_EV_META; ?>location" class="adventi_events_meta_box">Ort</label>
		<input id="location_input" name="<?php echo AD_EV_META; ?>location" value="<?php echo $event->location->address; ?>" class="adventi_events_meta_box">
		<input id="location_point" name="<?php echo AD_EV_META; ?>location_point" value="<?php echo $event->location->point_str(); ?>" type="hidden">
		<div id="location_proposals" class="adventi_events_meta_box"></div>
		<br>

		<label for="<?php echo AD_EV_META; ?>special" class="adventi_events_meta_box">Besondere Veranstaltung</label>
		<input type="checkbox" name="<?php echo AD_EV_META; ?>special" <?php checked($event->is_special()); ?> value="true">

		<div id="location_prev" class="adventi_events_meta_box"></div>
		<?php
	}


	static function get_image_selector( $post, $image_id ) {

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

		<label for="<?php echo AD_EV_META; ?>image" class="adventi_events_meta_box">Titelbild</label>
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
			<input class="image-id" name="<?php echo AD_EV_META; ?>image" type="hidden" value="<?php echo esc_attr( $image_id ); ?>">
		</div>
		<br>
		
		<?php
	}
}