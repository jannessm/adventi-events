<?php

include_once dirname(__FILE__) . '/constants.php';
include_once dirname(__FILE__) . '/event.php';

abstract class AdventiEventsMetaBox {
	/**
	 * Set up and add the meta box.
	 */
	public static function add() {
		add_meta_box(
			'ad_ev_meta_box',  			// Unique ID
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
			} elseif (in_array($key,['is_real', 'is_zoom', 'special'])) {
				update_post_meta(
					$post_id,
					$name,
					"false"
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
		$options = get_option( 'ad_ev_options' );
		$event = AdventiEvent::from_post($post->ID);

		wp_localize_script(
			'leaflet-script',
			'leaflet_options',
			array(
				'location' => $event->location->address,
				'location_lng' => $event->location->lng,
				'location_lat' => $event->location->lat,
				'input_id' => 'location_input',
				'input_lng' => 'location_lng',
				'input_lat' => 'location_lat',
				'input_proposals_id' => 'location_proposals',
				'map_id' => 'location_prev',
				'graphhopper_api_key' => $options[AD_EV_FIELD . 'graphhopper_api_key']
			)
		);

		self::get_image_selector( $post, $event->image_id );

		?>
		<label for="<?php echo AD_EV_META; ?>preacher" class="ad_ev_meta_box">Prediger</label>
		<input name="<?php echo AD_EV_META; ?>preacher" value="<?php echo $event->preacher; ?>" class="ad_ev_meta_box">
		<br>

		<label for="<?php echo AD_EV_META; ?>date" class="ad_ev_meta_box">Datum</label>
		<input type="datetime-local" name="<?php echo AD_EV_META; ?>date" value="<?php echo $event->date->format('Y-m-d\\TH:i'); ?>" class="ad_ev_meta_box">
		<br>

		<label for="<?php echo AD_EV_META; ?>recurrence" class="ad_ev_meta_box">Wiederkehrende Veranstaltung</label>
		<select type="datetime-local" name="<?php echo AD_EV_META; ?>recurrence" value="<?php echo $event->recurrence; ?>" class="ad_ev_meta_box">
			<?php
				foreach(AdventiEventsIntervals::values() as $value) {
					?>
						<option value="<?php echo $value;?>" <?php echo $value === $event->recurrence ? 'selected' : '' ?>><?php echo $value;?></option>
					<?php
				}
				?>
		</select>
		<br>

		<label for="<?php echo AD_EV_META; ?>exclude_dates" class="ad_ev_meta_box">Exkludiere Daten</label>
		<input name="<?php echo AD_EV_META; ?>exclude_dates" value="<?php echo join(', ', $event->exclude_dates); ?>" class="ad_ev_meta_box">
		<p class="hint">Die Daten müssen das Format 01.01.2022 haben. Alle Daten werden per , getrennt.</p><br>
		
		<label for="<?php echo AD_EV_META; ?>is_real" class="ad_ev_meta_box">Präsenzveranstaltung</label>
		<input type="checkbox" id="is_real" name="<?php echo AD_EV_META; ?>is_real" value="true" <?php echo checked($event->location->is_real);?>><br>

		<label for="<?php echo AD_EV_META; ?>location" class="ad_ev_meta_box">Ort</label>
		<input id="location_input" name="<?php echo AD_EV_META; ?>location" value="<?php echo $event->location->address; ?>" class="ad_ev_meta_box">
		<input id="location_lng" name="<?php echo AD_EV_META; ?>location_lng" value="<?php echo $event->location->lng; ?>" type="hidden">
		<input id="location_lat" name="<?php echo AD_EV_META; ?>location_lat" value="<?php echo $event->location->lat; ?>" type="hidden">
		<div id="location_proposals" class="ad_ev_meta_box"></div>
		<br>

		<label for="<?php echo AD_EV_META; ?>is_zoom" class="ad_ev_meta_box">Zoomveranstaltung</label>
		<input type="checkbox" id="is_zoom" name="<?php echo AD_EV_META; ?>is_zoom" value="true" <?php echo checked($event->zoom->is_zoom); ?>><br>
		<label for="<?php echo AD_EV_META; ?>zoom_id" class="ad_ev_meta_box">Zoom ID</label>
		<input id="zoom_id" name="<?php echo AD_EV_META; ?>zoom_id" value="<?php echo $event->zoom->id; ?>" class="ad_ev_meta_box"><br>
		<label for="<?php echo AD_EV_META; ?>zoom_pwd" class="ad_ev_meta_box">Zoom Passwort</label>
		<input id="zoom_pwd" name="<?php echo AD_EV_META; ?>zoom_pwd" value="<?php echo $event->zoom->pwd; ?>" class="ad_ev_meta_box"><br>
		<label for="<?php echo AD_EV_META; ?>zoom_tel" class="ad_ev_meta_box">Zoom Tel</label>
		<input id="zoom_tel" name="<?php echo AD_EV_META; ?>zoom_tel" value="<?php echo $event->zoom->tel; ?>" class="ad_ev_meta_box"><br>
		<label for="<?php echo AD_EV_META; ?>zoom_link" class="ad_ev_meta_box">Zoom Link</label>
		<input id="zoom_link" name="<?php echo AD_EV_META; ?>zoom_link" value="<?php echo $event->zoom->link; ?>" class="ad_ev_meta_box"><br>
		<p class="hint">Passwort, Link und Telefonnummer werden erst nach Captcha angezeigt.</p><br>

		<label for="<?php echo AD_EV_META; ?>special" class="ad_ev_meta_box">Besondere Veranstaltung</label>
		<input type="checkbox" name="<?php echo AD_EV_META; ?>special" value="true" <?php echo checked($event->is_special())?>><br>

		<div id="location_prev" class="ad_ev_meta_box"></div>
		<?php
	}


	static function get_image_selector( $post, $image_id ) {

		wp_localize_script(
			'image-select',
			'args',
			array(
				'image_id' => $image_id,
				'image_container' => ".img-preview-container"
			)
		);
		
		// Get WordPress' media upload URL
		$upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );
		
		// Get the image src
		$image_src = wp_get_attachment_image_src( $image_id );

		// For convenience, see if the array is valid
		$is_image = is_array( $image_src );
		?>

		<label for="<?php echo AD_EV_META; ?>image" class="ad_ev_meta_box">Titelbild</label>
		<div class="ad_ev_meta_box" style="display: inline-block">
			<!-- Your image container, which can be manipulated with js -->
			<div class="img-preview-container" style="max-width:300px !important">
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
			<input class="image-id" name="<?php echo AD_EV_META; ?>image_id" type="hidden" value="<?php echo esc_attr( $image_id ); ?>">
		</div>
		<br>
		
		<?php
	}
}