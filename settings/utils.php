<?php

include_once dirname(__FILE__) . '/../constants.php';
$ad_ev_label_style = "display: inline-block; width: 200px; vertical-align: top";



function ad_ev_settings_input($type, $field_name, $label, $placeholder, $descr) {
    global $ad_ev_label_style;
    $options = get_option( 'ad_ev_options' );

    $options[AD_EV_FIELD . 'preacher_plan'] = 'https://predigtplan.adventisten.de';

    $value = isset( $options[ $field_name ] ) ? $options[ $field_name ] : '';
    $input_style = 'min-width:300px;';
    
    if ($type == 'checkbox') {
        $input_style = '';
        $value = $value != '' ? 'checked' : '';
    } else {
        $value = 'value="' . $value . '"';
    }

    if (strcmp($type, 'hidden') != 0) {
        ?>
            <label for="<?php echo esc_attr( $field_name ); ?>" style="<?php echo $ad_ev_label_style; ?>"><?php echo $label; ?></label>
            <div style="display: inline-block;">
        <?php
    }
    ?>
        <input
                id="<?php echo esc_attr( $field_name ); ?>"
                <?php echo $value; ?>
                name="ad_ev_options[<?php echo esc_attr( $field_name ); ?>]"
                placeholder="<?php echo $placeholder; ?>"
                type="<?php echo $type; ?>"
                style="<?php echo $input_style; ?>">
    <?php
    if (strcmp($type, 'hidden') != 0) {
    ?>
            <p class="description">
                <?php esc_html_e( $descr, 'advent-events' ); ?>
            </p>
        </div><br>
    <?php
    }
}


function ad_ev_settings_textarea($field_name, $label, $placeholder, $descr, $hidden) {
    global $ad_ev_label_style;
    $options = get_option( 'ad_ev_options' );

    $options[AD_EV_FIELD . 'preacher_plan'] = 'https://predigtplan.adventisten.de';

    if (!$hidden) {
        ?>
            <label for="<?php echo esc_attr( $field_name ); ?>" style="<?php echo $ad_ev_label_style; ?>"><?php echo $label; ?></label>
            <div style="display: inline-block;">
        <?php
    }

    ?>
        <textarea
                id="<?php echo esc_attr( $field_name ); ?>"
                name="ad_ev_options[<?php echo esc_attr( $field_name ); ?>]"
                placeholder="<?php echo $placeholder; ?>"
                style="min-width:400px; min-height: 200px;<?php echo $hidden ? 'display: none;' : ''; ?>"><?php echo isset( $options[ $field_name ] ) ? $options[ $field_name ] : '' ?></textarea>
    
    <?php
    if (!$hidden) {
    ?>
            <p class="description">
                <?php esc_html_e( $descr, 'advent-events' ); ?>
            </p>
        </div><br>
    <?php
    }
}





function ad_ev_image_selector($image_id, $label, $field_name, $descr, $hidden=FALSE) {
    global $ad_ev_label_style;
    $options = get_option( 'ad_ev_options' );

    wp_localize_script(
        'image-select',
        'args',
        array(
            'image_container' => '.img-preview-container'
        )
    );

    if ($hidden) {
        ?>
            <input id="<?php echo esc_attr( $field_name ); ?>" class="image-id" name="ad_ev_options[<?php echo esc_attr( $field_name ); ?>]" type="hidden" value="<?php echo !!$options[ $field_name ] ? $options[ $field_name ] : '' ?>">
        <?php
        return;
    }

    // Get WordPress' media upload URL
	$upload_link = esc_url( get_upload_iframe_src( 'image' ) );
		
	// Get the image src
	$image_src = wp_get_attachment_image_src( $image_id );

	// For convenience, see if the array is valid
	$is_image = is_array( $image_src );
	?>

    <label for="<?php echo esc_attr( $field_name ); ?>" style="<?php echo $ad_ev_label_style; ?>"><?php echo $label; ?></label>
	<div style="display: inline-block">
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
				<?php _e('Set image') ?>
			</a>
			<a class="delete-custom-img <?php if ( ! $is_image  ) { echo 'hidden'; } ?>" 
			href="#">
				<?php _e('Remove this image') ?>
			</a>
		</p>

		<!-- A hidden input to set and post the chosen image id -->
		<input id="<?php echo esc_attr( $field_name ); ?>" class="image-id" name="ad_ev_options[<?php echo esc_attr( $field_name ); ?>]" type="hidden" value="<?php echo isset( $options[ $field_name ] ) ? $options[ $field_name ] : '' ?>">
		
		<p class="description">
			<?php esc_html_e( $descr, 'advent-events' ); ?>
		</p>
	</div>
	<br>
    
    <?php
}