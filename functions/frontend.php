<?php

/**
* If comment form is publish then only show the custom fields
*/

function cfb_get_comment_form_status( $comment_form_id ){

	$post = get_post( $comment_form_id );

	if( is_object( $post ) && $post->post_status == 'publish' ){
		return $post->ID;
	}

	return;

}

/**
* Get comment form id for post / pages
*/

function cfb_get_comment_form_id( $post ){

	if( !is_object( $post ) ){
		return;
	}

	$comment_display = get_option( 'cfb_comment_display' );

	if( empty( $comment_display ) || !is_array( $comment_display ) ){
		return;
	}

	if( $post->post_type != 'page' ){

		foreach ( $comment_display as $post_type => $comment_form_id ) {
			
			if( $post_type == $post->post_type ){

				return cfb_get_comment_form_status( $comment_form_id );

			}

		}

	} else {

		foreach ( $comment_display as $post_type => $comment_form_ids_array ) {

			if( $post_type == 'page' && array_key_exists( $post->ID , $comment_form_ids_array ) ){

				$comment_form_id = $comment_form_ids_array[$post->ID];
				return cfb_get_comment_form_status( $comment_form_id );

			}

		}
		
	}
	
	return;

}

/**
* Get section break HTML
*/

function cfb_get_extra_field_section_break( $value ){

	$title = !empty( $value['title'] ) ? esc_html( $value['title'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';

	$data = '<div class="extra_field_section_break cfb_extra_field"><h3>' . $title . '</h3>';

	if( !empty( $help ) ){
		$data .= '<p><i>' . $help . '</i></p>';	
	}
	
	$data .= '</div>';

	return $data;

}

/**
* Get Google Maps field HTML
*/

function cfb_get_extra_field_google_maps( $value , $comment_id ){

	//echo '<pre>'; print_r( $value ); echo '</pre>'; 
	$required = (!empty( $value['required'] ) && $value['required'] == 'yes') ? 'required' : '';
	$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : '';
	$name = !empty( $value['name'] ) ? esc_html( $value['name'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';
	$css = !empty( $value['css'] ) ? esc_html( $value['css'] ) : '';
	$placeholder = !empty( $value['placeholder'] ) ? esc_html( $value['placeholder'] ) : '';

	// For google map
	$google_id = 'wpad_map_' . $value['name']; 
	$input_id = 'wpad_id_' . $value['name'];
	$country_restrict = $value['country_restriction'];
	$place_type = $value['place_types'];

	$wpad_lat_lng = !empty( $value['default_coordinates']) ? $value['default_coordinates'] : '27.7172453,85.3239605'; 
	$lat_lng_arr = explode( ',' , $wpad_lat_lng );

	if( $comment_id != null ){
		$default = get_comment_meta( $comment_id, $name, true );

		$db_lat = !empty( $default['lat'] ) ? $default['lat'] : '';
		$db_lng = !empty( $default['lng'] ) ? $default['lng'] : '';

		if( !empty($db_lat) && !empty( $db_lng ) ){
			$wpad_lat_lng = $default['lat'] . ',' . $default['lng']; // Take the lat and lng form the database	
		}
		
	}
	
	$size = !empty( $value['size'] ) ? (int) $value['size'] : '40';

	$data = '<p class="extra_field_google_map cfb_extra_field"><label for="' . $name . '">' . $label;

	if( $required == 'required' ){
		$data .= '<span class="required"> *</span>';
	}

	$data .= '</label>' . '<input size="' . $size . '" id="' . $input_id . '" name="' . $name . '" type="text" class="cfb_google_place_input ' . $css . '" placeholder="' . $placeholder . '" value="' . (empty( $default['long_address'] ) ? '' : esc_html( $default['long_address'] )) . '" />';

	$data .= '<span class="cfb_google_map_details">
		<input name="lat_' . $google_id . '" type="hidden" id="lat_' . $google_id . '" class="google_map_lat" value="' . $lat_lng_arr[0] . '">
		<input name="lng_' . $google_id . '" type="hidden" id="lng_' .  $google_id . '" class="google_map_lng" value="' . $lat_lng_arr[1] . '">
	</span>

	<span id="' . $google_id . '" class="cfb_google_map_display"></span>';

	if( !empty( $help ) ){
		$data .= '<span class="help_text">' . $help . '</span>';	
	}
	
	$data .= '</p>';
	$data .= cfb_get_google_map_script( $value , $input_id , $google_id , $wpad_lat_lng , $country_restrict , $place_type );

	return $data;
}

function cfb_get_google_map_script( $value , $input_id , $google_id , $wpad_lat_lng , $country_restrict , $place_type ){ 
	
	ob_start(); ?>

	<script>

		window[ 'draggable_' + "<?php echo $input_id; ?>" ] = <?php echo !empty($value['marker_draggable']) ? 'true' : 'false'; ?>;
			
	    jQuery(document).ready(function(){
	    	
	    	var zoom = <?php echo !empty($value['zoom_level']) ? $value['zoom_level'] : '12'; ?>;
	    	var input_name = "<?php echo $input_id; ?>";

	    	cfb_initialize( "<?php echo $google_id; ?>" , zoom , input_name , "<?php echo $wpad_lat_lng; ?>" , window[ 'draggable_' + "<?php echo $input_id; ?>" ] , "<?php echo $country_restrict; ?>" , "<?php echo $place_type; ?>");

	    });

	</script>

	<?php
	return ob_get_clean();

}

/**
* Get text field HTML
*/

function cfb_get_extra_field_text( $value , $comment_id ){

	$required = (!empty( $value['required'] ) && $value['required'] == 'yes') ? 'required' : '';
	$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : '';
	$name = !empty( $value['name'] ) ? esc_html( $value['name'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';
	$css = !empty( $value['css'] ) ? esc_html( $value['css'] ) : '';
	$placeholder = !empty( $value['placeholder'] ) ? esc_html( $value['placeholder'] ) : '';

	if( $comment_id == null ){
		$default = !empty( $value['default'] ) ? esc_html( $value['default'] ) : '';	
	} else {
		$default = get_comment_meta( $comment_id, $name, true );
	}
	
	$size = !empty( $value['size'] ) ? (int) $value['size'] : '40';

	$data = '<p class="extra_field_text cfb_extra_field"><label for="' . $name . '">' . $label;

	if( $required == 'required' ){
		$data .= '<span class="required"> *</span>';
	}

	$data .= '</label>' . '<input size="' . $size . '" id="' . $name . '" name="' . $name . '" type="text" class="' . $css . '" placeholder="' . $placeholder . '" value="' . $default . '" />';

	if( !empty( $help ) ){
		$data .= '<span class="help_text">' . $help . '</span>';	
	}
	
	$data .= '</p>';

	return $data;
}

/**
* Get Really Simple Captcha field HTML
*/

function cfb_get_extra_field_really_simple_captcha( $value , $comment_id ){

	if( !class_exists( 'ReallySimpleCaptcha' ) ){
		return;
	}

	$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : '';
	$name = !empty( $value['name'] ) ? esc_html( $value['name'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';
	
	$data = '<p class="extra_field_really_simple_captcha cfb_extra_field"><label for="' . $name . '">' . $label;

	$data .= '<span class="required"> *</span></label>';

	$data .= cfb_get_really_simple_captcha( $name );

	$data .= '<input size="30" id="' . $name . '" name="' . $name . '" type="text" />';

	if( !empty( $help ) ){
		$data .= '<span class="help_text">' . $help . '</span>';	
	}
	
	$data .= '</p>';

	return $data;
}

function cfb_get_really_simple_captcha( $name ){

	$captcha_instance = new ReallySimpleCaptcha();
	$captcha_instance->bg = array( 255, 255, 255 );
	$captcha_instance->img_size = array( 250 , 40 );
	$captcha_instance->char_length = 7;
	$captcha_instance->font_size = 25;
	$captcha_instance->font_char_width = 35;
	$captcha_instance->base = array( 6, 30 );

	$word = $captcha_instance->generate_random_word();
	$prefix = mt_rand();
	$filename = $captcha_instance->generate_image( $prefix, $word );

	$image = plugins_url() . '/really-simple-captcha/tmp/' . $filename;

	$data = '<span class="captcha_wrapper"><img id="' . $prefix . '" class="cfb_really_simple_captcha" src="' . $image . '" /></span>';
	//echo '<input name="user_input_captcha" type="text" class="form-control mb-5 mt-5" placeholder="' . esc_html__( 'Type above letters' , 'extretion' ) . '">';
	$data .= '<input type="hidden" name="' . $name . '_captcha_prefix" value="' . $prefix . '">';
	return $data;

}

/**
* Get email field HTML
*/

function cfb_get_extra_field_email( $value , $comment_id ){

	$required = (!empty( $value['required'] ) && $value['required'] == 'yes') ? 'required' : '';
	$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : '';
	$name = !empty( $value['name'] ) ? esc_html( $value['name'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';
	$css = !empty( $value['css'] ) ? esc_html( $value['css'] ) : '';
	$placeholder = !empty( $value['placeholder'] ) ? esc_html( $value['placeholder'] ) : '';

	if( empty( $comment_id ) ){
		$default = !empty( $value['default'] ) ? esc_html( $value['default'] ) : '';	
	} else {
		$default = get_comment_meta( $comment_id, $name, true );
	}

	$size = !empty( $value['size'] ) ? (int) $value['size'] : '40';

	$data = '<p class="extra_field_email cfb_extra_field"><label for="' . $name . '">' . $label;

	if( $required == 'required' ){
		$data .= '<span class="required"> *</span>';
	}

	$data .= '</label>' . '<input size="' . $size . '" id="' . $name . '" name="' . $name . '" type="email" class="' . $css . '" placeholder="' . $placeholder . '" value="' . $default . '" />';

	if( !empty( $help ) ){
		$data .= '<span class="help_text">' . $help . '</span>';	
	}
	
	$data .= '</p>';

	return $data;
}

/**
* Get URL field HTML
*/

function cfb_get_extra_field_url( $value , $comment_id ){

	$required = (!empty( $value['required'] ) && $value['required'] == 'yes') ? 'required' : '';
	$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : '';
	$name = !empty( $value['name'] ) ? esc_html( $value['name'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';
	$css = !empty( $value['css'] ) ? esc_html( $value['css'] ) : '';
	$placeholder = !empty( $value['placeholder'] ) ? esc_html( $value['placeholder'] ) : '';

	if( empty( $comment_id ) ){
		$default = !empty( $value['default'] ) ? esc_html( $value['default'] ) : '';	
	} else {
		$default = get_comment_meta( $comment_id, $name, true );
	}

	$size = !empty( $value['size'] ) ? (int) $value['size'] : '40';

	$data = '<p class="extra_field_url cfb_extra_field"><label for="' . $name . '">' . $label;

	if( $required == 'required' ){
		$data .= '<span class="required"> *</span>';
	}

	$data .= '</label>' . '<input size="' . $size . '" id="' . $name . '" name="' . $name . '" type="text" class="' . $css . '" placeholder="' . $placeholder . '" value="' . $default . '" />';

	if( !empty( $help ) ){
		$data .= '<span class="help_text">' . $help . '</span>';	
	}
	
	$data .= '</p>';

	return $data;
}

/**
* Get radio field HTML
*/

function cfb_get_extra_field_radio( $value , $comment_id ){

	$required = (!empty( $value['required'] ) && $value['required'] == 'yes') ? 'required' : '';
	$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : '';
	$name = !empty( $value['name'] ) ? esc_html( $value['name'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';
	$css = !empty( $value['css'] ) ? esc_html( $value['css'] ) : '';

	if( empty( $comment_id ) ){
		$default = !empty( $value['default'] ) ? esc_html( $value['default'] ) : '';	
	} else {
		$default = get_comment_meta( $comment_id, $name, true );
	}

	/* Filter Choices */
	$choices = !empty( $value['choices'] ) ? esc_html( $value['choices'] ) : '';

	$choicesArray = explode( "\n", $choices );

	if( !empty( $choicesArray ) && is_array( $choicesArray ) ){

		$explode_key_value = array();		
		$radio_choices = array();

		foreach( $choicesArray as $value ){

			$explode_key_value = explode( ':' , $value , 2 );

			if( !empty( $explode_key_value[0] ) && !empty( $explode_key_value[1] ) ){
				
				$kirki_key =  sanitize_text_field( $explode_key_value[0] );
				$kirki_value =  sanitize_text_field( $explode_key_value[1] );

				$radio_choices[ $kirki_key ] = $kirki_value;

			}			

		}

	}

	$data = '<p class="extra_field_radio cfb_extra_field"><label>' . $label;

	if( $required == 'required' ){
		$data .= '<span class="required"> *</span>';
	}

	$data .= '</label><span class="radio_value_wrapper">';

	if( !empty( $radio_choices ) ){

		foreach ( $radio_choices as $key1 => $value1 ) {
			
			$data .= '<label><input ';
			$data .= checked( $default, $key1 , false );
			$data .= ' id="' . $name . '" name="' . $name . '" type="radio" class="' . $css . '" value="' . $key1 . '" />' . $value1 . '</label>';

		}

	}

	$data .= '</span>';	

	if( !empty( $help ) ){
		$data .= '<span class="help_text">' . $help . '</span>';	
	}
	
	$data .= '</p>';

	return $data;
}

/**
* Get select field HTML
*/

function cfb_get_extra_field_select( $value , $comment_id ){

	$required = (!empty( $value['required'] ) && $value['required'] == 'yes') ? 'required' : '';
	$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : '';
	$name = !empty( $value['name'] ) ? esc_html( $value['name'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';
	$css = !empty( $value['css'] ) ? esc_html( $value['css'] ) : '';
	$select_text = !empty( $value['select_text'] ) ? esc_html( $value['select_text'] ) : '';

	if( !empty( $comment_id ) ){
		$selected = get_comment_meta( $comment_id, $name, true );	
	} else {	
		$selected = '';
	}

	/* Filter Choices */
	$choices = !empty( $value['choices'] ) ? esc_html( $value['choices'] ) : '';

	$choicesArray = explode( "\n", $choices );

	if( !empty( $choicesArray ) && is_array( $choicesArray ) ){

		$explode_key_value = array();		
		$radio_choices = array();

		foreach( $choicesArray as $value ){

			$explode_key_value = explode( ':' , $value , 2 );

			if( !empty( $explode_key_value[0] ) && !empty( $explode_key_value[1] ) ){
				
				$kirki_key =  sanitize_text_field( $explode_key_value[0] );
				$kirki_value =  sanitize_text_field( $explode_key_value[1] );

				$radio_choices[ $kirki_key ] = $kirki_value;

			}			

		}

	}

	$data = '<p class="extra_field_select cfb_extra_field"><label>' . $label;

	if( $required == 'required' ){
		$data .= '<span class="required"> *</span>';
	}

	$data .= '</label><select class="select_value_wrapper" name="' . $name . '" id="' . $name . '" class="' . $css . '">';
	$data .= !empty( $select_text ) ? '<option value="">' . $select_text . '</option>' : '';

	if( !empty( $radio_choices ) ){

		foreach ( $radio_choices as $key1 => $value1 ) {
			
			$data .= '<option ';
			$data .= selected( $selected, $key1 , false );
			$data .= ' value="' . $key1 . '">' . $value1 . '</option>';

		}

	}

	$data .= '</select>';	

	if( !empty( $help ) ){
		$data .= '<span class="help_text">' . $help . '</span>';	
	}
	
	$data .= '</p>';

	return $data;
}

/**
* Get multi select field HTML
*/

function cfb_get_extra_field_multi_select( $value , $comment_id ){

	$required = (!empty( $value['required'] ) && $value['required'] == 'yes') ? 'required' : '';
	$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : '';
	$name = !empty( $value['name'] ) ? esc_html( $value['name'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';
	$css = !empty( $value['css'] ) ? esc_html( $value['css'] ) : '';
	$select_text = !empty( $value['select_text'] ) ? esc_html( $value['select_text'] ) : '';

	if( !empty( $comment_id ) ){
		$defaults = get_comment_meta( $comment_id, $name, true );
		$defaults = !empty( $defaults ) ? $defaults : array();
	} else {
		$defaults = array();
	}

	/* Filter Choices */
	$choices = !empty( $value['choices'] ) ? esc_html( $value['choices'] ) : '';

	$choicesArray = explode( "\n", $choices );

	if( !empty( $choicesArray ) && is_array( $choicesArray ) ){

		$explode_key_value = array();		
		$radio_choices = array();

		foreach( $choicesArray as $value ){

			$explode_key_value = explode( ':' , $value , 2 );

			if( !empty( $explode_key_value[0] ) && !empty( $explode_key_value[1] ) ){
				
				$kirki_key =  sanitize_text_field( $explode_key_value[0] );
				$kirki_value =  sanitize_text_field( $explode_key_value[1] );

				$radio_choices[ $kirki_key ] = $kirki_value;

			}			

		}

	}

	$data = '<p class="extra_field_multi_select cfb_extra_field"><label>' . $label;

	if( $required == 'required' ){
		$data .= '<span class="required"> *</span>';
	}

	$data .= '</label><select multiple class="select_value_wrapper" name="' . $name . '[]" id="' . $name . '" class="' . $css . '">';
	$data .= !empty( $select_text ) ? '<option value="">' . $select_text . '</option>' : '';

	if( !empty( $radio_choices ) ){

		foreach ( $radio_choices as $key1 => $value1 ) {
			
			$data .= '<option ';
			$data .= in_array( $key1 , $defaults ) ? ' selected ' : '';
			$data .= ' value="' . $key1 . '">' . $value1 . '</option>';

		}

	}

	$data .= '</select>';	

	if( !empty( $help ) ){
		$data .= '<span class="help_text">' . $help . '</span>';	
	}
	
	$data .= '</p>';

	return $data;
}

/**
* Get checkbox field HTML
*/

function cfb_get_extra_field_checkbox( $value , $comment_id ){

	$required = (!empty( $value['required'] ) && $value['required'] == 'yes') ? 'required' : '';
	$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : '';
	$name = !empty( $value['name'] ) ? esc_html( $value['name'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';
	$css = !empty( $value['css'] ) ? esc_html( $value['css'] ) : '';
	$defaults = !empty( $value['default'] ) ? explode( ',' , $value['default'] ) : array();

	if( empty( $comment_id ) ){
		
		if( !empty( $defaults ) ){
			$defaults = array_map( 'sanitize_text_field' , $defaults );
		}

	} else {
		$defaults = get_comment_meta( $comment_id, $name, true );
		$defaults = !empty( $defaults ) ? $defaults : array();
	}

	/* Filter Choices */
	$choices = !empty( $value['choices'] ) ? esc_html( $value['choices'] ) : '';

	$choicesArray = explode( "\n", $choices );

	if( !empty( $choicesArray ) && is_array( $choicesArray ) ){

		$explode_key_value = array();		
		$radio_choices = array();

		foreach( $choicesArray as $value ){

			$explode_key_value = explode( ':' , $value , 2 );

			if( !empty( $explode_key_value[0] ) && !empty( $explode_key_value[1] ) ){
				
				$kirki_key =  sanitize_text_field( $explode_key_value[0] );
				$kirki_value =  sanitize_text_field( $explode_key_value[1] );

				$radio_choices[ $kirki_key ] = $kirki_value;

			}			

		}

	}

	//echo '<pre>'; print_r( $radio_choices ); echo '</pre>';

	$data = '<p class="extra_field_checkbox cfb_extra_field"><label>' . $label;

	if( $required == 'required' ){
		$data .= '<span class="required"> *</span>';
	}

	$data .= '</label><span class="checkbox_value_wrapper">';

	if( !empty( $radio_choices ) ){

		foreach ( $radio_choices as $key1 => $value1 ) {
			
			$data .= '<label><input ';
			$data .= in_array( $key1, $defaults ) ? 'checked' : '';
			$data .= ' id="' . $name . '" name="' . $name . '[]" type="checkbox" class="' . $css . '" value="' . $key1 . '" />' . $value1 . '</label>';

		}

	}

	$data .= '</span>';	

	if( !empty( $help ) ){
		$data .= '<span class="help_text">' . $help . '</span>';	
	}
	
	$data .= '</p>';

	return $data;
}

/**
* Get textarea field HTML
*/

function cfb_get_extra_field_textarea( $value , $comment_id ){

	//echo '<pre>'; print_r( $value ); echo '</pre>';

	$required = (!empty( $value['required'] ) && $value['required'] == 'yes') ? 'required' : '';
	$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : '';
	$name = !empty( $value['name'] ) ? esc_html( $value['name'] ) : '';
	$help = !empty( $value['help'] ) ? esc_html( $value['help'] ) : '';
	$css = !empty( $value['css'] ) ? esc_html( $value['css'] ) : '';
	$placeholder = !empty( $value['placeholder'] ) ? esc_html( $value['placeholder'] ) : '';
	
	if( empty( $comment_id ) ){
		$default = !empty( $value['default'] ) ? esc_html( $value['default'] ) : '';	
	} else {
		$default = get_comment_meta( $comment_id, $name, true );
	}
	
	$rows = !empty( $value['rows'] ) ? esc_html( $value['rows'] ) : '5';
	$columns = !empty( $value['columns'] ) ? esc_html( $value['columns'] ) : '45';

	$data = '<p class="extra_field_textarea cfb_extra_field"><label for="' . $name . '">' . $label;

	if( $required == 'required' ){
		$data .= '<span class="required"> *</span>';
	}

	$data .= '</label>' . '<textarea cols="' . $columns . '" rows="' . $rows . '" id="' . $name . '" name="' . $name . '" class="' . $css . '" placeholder="' . $placeholder . '"/>' . $default . '</textarea>';

	if( !empty( $help ) ){
		$data .= '<span class="help_text">' . $help . '</span>';	
	}
	
	$data .= '</p>';

	return $data;
}

/**
* Get extra fields to show on comment fields
*/

function cfb_get_extra_fields( $post = null , $comment_id = null ) {

	if( $post == null ){
		global $post;
	}
	
	$comment_form_id = cfb_get_comment_form_id( $post );
	$extra_fields = get_post_meta( $comment_form_id , 'comment_custom_fields' , true );
	$html_extra_fields = array();

	if( !empty( $extra_fields ) && is_array( $extra_fields ) ){

		foreach ( $extra_fields as $key => $value ) {
			
			switch ( $value['type'] ) {

				case 'section_break':

					$html_extra_fields["section_break_{$key}"] = cfb_get_extra_field_section_break( $value );
					break;

				case 'text':

					$html_extra_fields["text_{$key}"] = cfb_get_extra_field_text( $value , $comment_id );
					break;

				case 'google_maps':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					$html_extra_fields["google_maps_{$key}"] = cfb_get_extra_field_google_maps( $value , $comment_id );
					break;

				case 'comment':
					$html_extra_fields["comment_field"] = '';
					break;

				case 'website':
					$html_extra_fields["website_field"] = '';					
					break;

				case 'textarea':
					$html_extra_fields["textarea_{$key}"] = cfb_get_extra_field_textarea( $value , $comment_id );
					break;

				case 'radio':
					$html_extra_fields["radio_{$key}"] = cfb_get_extra_field_radio( $value , $comment_id );
					break;

				case 'checkbox':
					$html_extra_fields["checkbox_{$key}"] = cfb_get_extra_field_checkbox( $value , $comment_id );
					break;

				case 'dropdown':
					$html_extra_fields["select_{$key}"] = cfb_get_extra_field_select( $value , $comment_id );
					break;

				case 'multi_select':
					$html_extra_fields["multi_select_{$key}"] = cfb_get_extra_field_multi_select( $value , $comment_id );
					break;

				case 'url':

					$html_extra_fields["url_{$key}"] = cfb_get_extra_field_url( $value , $comment_id );
					break;

				case 'email':
					$html_extra_fields["email_{$key}"] = cfb_get_extra_field_email( $value , $comment_id );
					break;

				case 'date':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					$html_extra_fields["date_{$key}"] = cfb_get_extra_field_date( $value , $comment_id );	
					break;

				case 'taxonomy':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					$value['choices'] = cfb_get_taxonomy_choices( $value );

					if( empty( $value['display_option'] ) || $value['display_option'] == 'select' ){
						
						$html_extra_fields["select_{$key}"] = cfb_get_extra_field_select( $value , $comment_id );

					} elseif( $value['display_option'] == 'multi_select' ){

						$html_extra_fields["multi_select_{$key}"] = cfb_get_extra_field_multi_select( $value , $comment_id );

					} elseif( $value['display_option'] == 'radio' ){

						$html_extra_fields["radio_{$key}"] = cfb_get_extra_field_radio( $value , $comment_id );

					} else {

						$html_extra_fields["checkbox_{$key}"] = cfb_get_extra_field_checkbox( $value , $comment_id );

					}

					break;

				case 'file_upload':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					$html_extra_fields["file_upload_{$key}"] = cfb_get_extra_field_file_upload( $value , $comment_id );

					break;

				case 'user_image':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					$html_extra_fields["user_image_{$key}"] = cfb_get_extra_field_user_image( $value , $comment_id );

					break;

				case 'reCaptcha':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) || is_admin() ){
						continue;
					}

					$html_extra_fields["reCaptcha_{$key}"] = cfb_get_extra_field_reCaptcha( $value , $comment_id );

					break;

				case 'really_simple_captcha':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) || is_admin() ){
						continue;
					}

					$html_extra_fields["really_simple_captcha_{$key}"] =  cfb_get_extra_field_really_simple_captcha( $value , $comment_id );

					break;

				case 'HTML':

					$content = $value['html'];
					if( !empty( $content ) ){
						$html_extra_fields["HTML_{$key}"] =  do_shortcode( $content );	
					}
					
					break;
				
				default:
					# code...
					break;
			}

		}

	}

	$style='<style>
		#commentform p.comment-form-author { float: inherit; }
		#commentform p.comment-form-email { float: inherit; }
	</style>';
	$html_extra_fields['custom_css'] = $style;

    return $html_extra_fields;
}

/**
 * @param array      $array
 * @param int|string $position
 * @param mixed      $insert
 */

function cfb_array_insert(&$array, $position, $insert) {
    if (is_int($position)) {
        array_splice($array, $position, 0, $insert);
    } else {
        $pos   = array_search($position, array_keys($array));
        $array = array_merge(
            array_slice($array, 0, $pos),
            $insert,
            array_slice($array, $pos)
        );
    }
}

/**
* Move predefine comment field ( Name, email and website )
*/

function cfb_insert_predefined_comment_fields( $fields , $extra_fields , $temp_name , $temp_email , $temp_url , $temp_comment ){

	if( !empty( $extra_fields ) && is_array( $extra_fields ) ){

		foreach ( $extra_fields as $key => $value ) {

			//echo $value['type'] . ' ';
			
			if( $value['type'] == 'name' ){
				cfb_array_insert( $fields, $key , $temp_name );
			} elseif( $value['type'] == 'predefined_email' ){
				cfb_array_insert( $fields, $key , $temp_email );
			} elseif( $value['type'] == 'website' ){
				cfb_array_insert( $fields, $key , $temp_url );
			} elseif( $value['type'] == 'comment' ){
				cfb_array_insert( $fields, $key , $temp_comment );
			}

		}

	}
	//$fields['comment_field']
	return $fields;

}


/**
* Move comment field
*/

function cfb_move_comment_textarea( $fields , $extra_fields , $comment_field ){

	if( !empty( $extra_fields ) && is_array( $extra_fields ) ){

		foreach ( $extra_fields as $key => $value ) {
			
			if( $value['type'] == 'comment' ){
				cfb_array_insert( $fields, $key , $comment_field );
			}

		}

	}

	return $fields;

}

/**
* Move comment field
*/

add_filter( 'comment_form_fields', 'cfb_move_comment_field' );
function cfb_move_comment_field( $fields ) {

	// if ( is_user_logged_in() ){
 //        return $fields;
	// }

	global $post;
	
	$comment_form_id = cfb_get_comment_form_id( $post );

	if( $comment_form_id == false ){
		return $fields;
	}
	
	$extra_fields = get_post_meta( $comment_form_id , 'comment_custom_fields' , true );
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );

	$fields = cfb_move_comment_textarea( $fields , $extra_fields , $comment_field );
	//print_r($fields);
	return $fields;
}

function cfb_remove_comment_key( $extra_fields ){

	if( !empty( $extra_fields ) && is_array( $extra_fields ) ){

		foreach ( $extra_fields as $key => $value) {
			
			if( $value['type'] == 'comment' ){
				unset( $extra_fields[$key] );
			}

		}

	}

	return array_values( $extra_fields );

}





/**
* Get all comment fields
*/

add_filter( 'comment_form_fields' , 'cfb_add_comment_fields' );
//add_filter( 'comment_form_default_fields','cfb_add_comment_fields' );

function cfb_add_comment_fields( $custom_fields ) {

	global $post;

	$comment_form_id = cfb_get_comment_form_id( $post );

	if( $comment_form_id == false ){
		return $custom_fields;
	}

	$temp_name = $custom_fields['author'];
	$temp_email = $custom_fields['email'];
	$temp_url = $custom_fields['url'];
	$temp_comment = $custom_fields[0];

	$extra_fields = get_post_meta( $comment_form_id , 'comment_custom_fields' , true );
	//$extra_fields = is_array( $extra_fields ) ? cfb_remove_comment_key( $extra_fields ) : cfb_remove_comment_key( array() );

    $fields = cfb_get_extra_fields();
    unset( $fields['website_field'] );
    $fields = cfb_insert_predefined_comment_fields( $fields , $extra_fields , $temp_name , $temp_email , $temp_url , $temp_comment );

    return $fields;

}

add_filter( 'comment_form_logged_in_after', 'cfb_add_comment_fields_logged_in');

function cfb_add_comment_fields_logged_in() {

    if (!is_user_logged_in())
        return;

    $fields = cfb_get_extra_fields();

    //echo '<pre>'; print_r( $fields ); echo '</pre>';

    foreach ( $fields as $name => $field ) {

    	switch ( $name ) {

    		case 'comment_field':
    			$comment_field = '<p class="comment-form-comment"><label for="comment">' .esc_html__( 'Comment' , 'cfb' ). '<span class="required"> *</span></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required="required"></textarea></p>';
    			echo apply_filters( "comment_form_field_comment", $comment_field );
    			break;
    		
    		default:
    			echo apply_filters( "comment_form_field_{$name}", $field );
    			break;

    	}
        
    }

}

/**
* Custom field validation
*/

add_filter( 'preprocess_comment', 'cfb_verify_comment_meta_data' );

function cfb_verify_comment_meta_data( $commentdata ) {

	/**
	* Do not save custom field data for child comment
	*/

	if( !empty( $commentdata['comment_parent'] ) ){
		return $commentdata;
	}

	$post_id = $commentdata['comment_post_ID'];
	$comment_form_id = cfb_get_comment_form_id( get_post( $post_id ) );

	$custom_fields_details = get_post_meta( $comment_form_id, 'comment_custom_fields' , true );

	if( empty( $custom_fields_details ) || !is_array( $custom_fields_details ) ){
		return $commentdata;
	}

	foreach ( $custom_fields_details as $key => $value) {
		
		if( !empty( $value['required'] ) && $value['required'] == 'yes' ){

			switch ( $value['type'] ) {

				case 'text':
				case 'textarea':

					$name = $value['name'];
					if( empty( $_POST[$name] ) ){
						
						wp_die(

							sprintf(
								__( "<strong>ERROR</strong>: Please fill the required field ( %s )" ),
								esc_html( $value['label'] )
							),
							'Error',
							array( 'back_link' => true )

						);
					}

					break;

				case 'radio':
				case 'checkbox':
				case 'dropdown':
					cfb_get_error_msg_for_select_radio_checkbox( $value );
					break;

				case 'multi_select':
					cfb_get_error_msg_for_multi_select( $value );
					break;

				case 'taxonomy':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					if( !empty( $value['display_option'] ) && $value['display_option'] == 'multi_select' ){
						cfb_get_error_msg_for_multi_select( $value );
					} else {
						cfb_get_error_msg_for_select_radio_checkbox( $value );
					}
	
					break;

				case 'url':

					$name = $value['name'];
					$escape_url = !empty( $_POST[$name] ) ? esc_url( $_POST[$name] ) : '';

					if( empty( $escape_url ) || cfb_check_valid_url( $escape_url ) == false ){

						wp_die(

							sprintf(
								__( "<strong>ERROR</strong>: Please add a valid URL ( %s )" ),
								esc_html( $value['label'] )
							),
							'Error',
							array( 'back_link' => true )

						);
					}

					break;
				
				case 'email':

					$name = $value['name'];
					if( empty( $_POST[$name] ) || !is_email( $_POST[$name] ) ){
						
						wp_die(

							sprintf(
								__( "<strong>ERROR</strong>: Please enter your email ( %s )" ),
								esc_html( $value['label'] )
							),
							'Error',
							array( 'back_link' => true )

						);
					}

					break;

				case 'date':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					$name = $value['name'];

					if( empty( $_POST[$name] ) || cfb_validateDateTime( $_POST[$name] , $value ) == false ){
						
						wp_die(

							sprintf(
								__( "<strong>ERROR</strong>: Please add a proper date ( %s )" ),
								esc_html( $value['label'] )
							),
							'Error',
							array( 'back_link' => true )

						);
					}

					break;

				case 'file_upload':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					$name = $value['name'];

					// Check empty files
					if( empty( $_FILES[$name]['name'][0] ) ){
						$message = esc_html__( 'Please add some files' , 'cfb' );
						cfb_get_error_message_files( $message , $value );
					}

					cfb_validate_files( $value , $name );

					break;

				case 'user_image':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					$name = $value['name'];

					// Limit only one image
					if( count( $_FILES[$name]['name'] ) > 1 ){
						$message = esc_html__( 'Please upload only one image' , 'cfb' );
						cfb_get_error_message_files( $message , $value );
					}

					// Check empty image
					if( empty( $_FILES[$name]['name'][0] ) ){
						$message = esc_html__( 'Please add your image' , 'cfb' );
						cfb_get_error_message_files( $message , $value );
					}

					// Check file types
					$allowed_ext = "'jpg', 'jpeg', 'gif', 'png', 'bmp'";
					cfb_check_file_types( $_FILES[$name]['name'] , $value , $allowed_ext );

					break;

				case 'google_maps':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					$name = $value['name'];
					$latitude_name = 'lat_wpad_map_' . $name;
					$longitude_name = 'lng_wpad_map_' . $name;

					$map_details = array();

					$map_details['long_address'] = !empty( $_POST[$name] ) ? sanitize_text_field( $_POST[$name] ) : '';
					$map_details['lat'] = !empty( $_POST[$latitude_name] ) ? sanitize_text_field( $_POST[$latitude_name] ) : '';
					$map_details['lng'] = !empty( $_POST[$longitude_name] ) ? sanitize_text_field( $_POST[$longitude_name] ) : '';

					if( count( array_filter( $map_details ) ) < 3 ){
						$message = esc_html__( 'Please add the address properly' , 'cfb' );
						cfb_get_error_message_files( $message , $value );
					}

					break;

				default:
					# code...
					break;
			}

		} else {

			switch ( $value['type'] ) {

				case 'url':

					$name = $value['name'];
					$escape_url = !empty( $_POST[$name] ) ? esc_url( $_POST[$name] ) : '';

					if( !empty( $escape_url ) && cfb_check_valid_url( $escape_url ) == false ){

						wp_die(

							sprintf(
								__( "<strong>ERROR</strong>: Please add a valid URL ( %s )" ),
								esc_html( $value['label'] )
							),
							'Error',
							array( 'back_link' => true )

						);
					}

					break;

				case 'email':

					$name = $value['name'];
					if( !empty( $_POST[$name] ) && !is_email( $_POST[$name] ) ){
						
						wp_die(

							sprintf(
								__( "<strong>ERROR</strong>: Please enter your email ( %s )" ),
								esc_html( $value['label'] )
							),
							'Error',
							array( 'back_link' => true )

						);
					}

					break;

				case 'date':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}
					
					$name = $value['name'];

					if( !empty( $_POST[$name] ) && cfb_validateDateTime( $_POST[$name] , $value ) == false ){
						
						wp_die(

							sprintf(
								__( "<strong>ERROR</strong>: Please add a proper date ( %s )" ),
								esc_html( $value['label'] )
							),
							'Error',
							array( 'back_link' => true )

						);
					}

					break;

				case 'file_upload':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					$name = $value['name'];

					if( !empty( $_FILES[$name]['name'][0] ) ){
						cfb_validate_files( $value , $name );
					}

					break;

				case 'reCaptcha':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
						continue;
					}

					require_once __DIR__ . '/../recaptcha/autoload.php';

					$security_code = !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
					
					$secret = esc_html( get_option( 'recaptcha_secret_key' ) );
					$recaptcha = new \ReCaptcha\ReCaptcha($secret);		
					$resp = $recaptcha->verify( $security_code , $_SERVER['REMOTE_ADDR'] );	

					if ( !$resp->isSuccess() ){
						$message = esc_html__( 'Verification failed. Please try again' , 'cfb' );
						cfb_get_error_message_files( $message , $value );
					} 

					break;

				case 'really_simple_captcha':

					$name = $value['name'];
					$user_input = sanitize_text_field( $_POST[$name] );

					$db_prefix_name = $name . '_captcha_prefix';
					$db_prefix = sanitize_text_field( $_POST[$db_prefix_name] );

					$captcha_instance = new ReallySimpleCaptcha();
					$correct = $captcha_instance->check( $db_prefix, $user_input );

					if( $correct != true ){

						$message = esc_html__( 'Verification failed. Please try again' , 'cfb' );
						cfb_get_error_message_files( $message , $value );

					}

					break;

			}

		}

	}

    return $commentdata;
}

function cfb_get_error_msg_for_multi_select( $value ){

	$name = $value['name'];
	$removeEmptyKeys = !empty( $_POST[$name] ) ? array_filter( $_POST[$name] ) : '';

	if( empty( $removeEmptyKeys ) ){

		wp_die(

			sprintf(
				__( "<strong>ERROR</strong>: Please select one from the given options ( %s )" ),
				esc_html( $value['label'] )
			),
			'Error',
			array( 'back_link' => true )

		);
	}

}

function cfb_get_error_msg_for_select_radio_checkbox( $value ){

	$name = $value['name'];
	if( empty( $_POST[$name] ) ){
		
		wp_die(

			sprintf(
				__( "<strong>ERROR</strong>: Please select one from the given options ( %s )" ),
				esc_html( $value['label'] )
			),
			'Error',
			array( 'back_link' => true )

		);
	}

}

/**
* Regex to check url
*/

function cfb_check_valid_url( $url ){

	$pattern = '%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu';

	return preg_match( $pattern, $url );

}

/**
* Save the data
*/

add_action( 'comment_post', 'cfb_save_comment_meta_data' , 10 , 3 );
function cfb_save_comment_meta_data( $comment_id, $comment_approved, $commentdata ) {
    
    //print_r($_FILES);die;
    // Don't save custom field data for child comment
	if( !empty( $commentdata['comment_parent'] ) ){
		return;
	}

    $post_id = $commentdata['comment_post_ID'];

    $comment_form_id = cfb_get_comment_form_id( get_post( $post_id ) );

	$custom_fields_details = get_post_meta( $comment_form_id, 'comment_custom_fields' , true );

	if( empty( $custom_fields_details ) || !is_array( $custom_fields_details ) ){
		return;
	}

	foreach ( $custom_fields_details as $key => $value) {

		switch ( $value['type'] ) {

			case 'text':
			case 'textarea':
			case 'radio':
			case 'dropdown':
			case 'date':
				cfb_save_textbox_data( $value , $comment_id );
				break;

			case 'checkbox':
				cfb_save_checkbox_data( $value , $comment_id );
				break;

			case 'multi_select':
				cfb_save_multiselect_data( $value , $comment_id );
				break;

			case 'url':
				$name = $value['name'];
				update_comment_meta( $comment_id, $name, esc_url( $_POST[ $name ] ) );
				break;

			case 'email':
				$name = $value['name'];
				update_comment_meta( $comment_id, $name, sanitize_email( $_POST[ $name ] ) );
				break;

			case 'taxonomy':

				if( $value['display_option'] == 'multi_select' ){
					cfb_save_multiselect_data( $value , $comment_id );
				} elseif( $value['display_option'] == 'checkbox' ){
					cfb_save_checkbox_data( $value , $comment_id );
				} else{
					cfb_save_textbox_data( $value , $comment_id );
				}

				break;

			case 'file_upload':
			case 'user_image':

				$name = $value['name'];
				$file_id = array();

				if( !empty( $_FILES[$name]['name'][0] ) ){

					$no_of_files = count( $_FILES[$name]['name'] );

					for ( $i=0; $i < $no_of_files; $i++) { 
						
						$file = array();
						$file['name'] = $_FILES[$name]['name'][$i];
						$file['type'] = $_FILES[$name]['type'][$i];
						$file['tmp_name'] = $_FILES[$name]['tmp_name'][$i];
						$file['error'] = $_FILES[$name]['error'][$i];
						$file['size'] = $_FILES[$name]['size'][$i];

						$file_id[] = cfb_upload_files( $file , $post_id );

					}

					$prev_files = get_comment_meta( $comment_id, $name, true );

					if( !empty( $prev_files ) && is_array( $prev_files ) ){

						$file_id = array_merge( $prev_files , $file_id );

					}

					update_comment_meta( $comment_id, $name, array_filter( $file_id ) );

				}

				break;

			case 'google_maps':

				$name = $value['name'];
				$latitude_name = 'lat_wpad_map_' . $name;
				$longitude_name = 'lng_wpad_map_' . $name;

				$map_details = array();

				$map_details['long_address'] = !empty( $_POST[$name] ) ? sanitize_text_field( $_POST[$name] ) : '';
				$map_details['lat'] = !empty( $_POST[$latitude_name] ) ? sanitize_text_field( $_POST[$latitude_name] ) : '';
				$map_details['lng'] = !empty( $_POST[$longitude_name] ) ? sanitize_text_field( $_POST[$longitude_name] ) : '';

				/**
				* For admin edit all fields are not required
				*/

				if( count( array_filter( $map_details ) ) == 3 || ( is_admin() && !empty( $comment_id ) ) ){
					update_comment_meta( 
						$comment_id, 
						$name, 
						array_map( 
							'sanitize_text_field', 
							$map_details 
						) 
					);
				}

				break;

		}

	}

}

function cfb_save_textbox_data( $value , $comment_id ){

	$name = $value['name'];
	$meta_value = !empty( $_POST[ $name ] ) ? sanitize_text_field( $_POST[ $name ] ) : ''; 
	update_comment_meta( $comment_id, $name, $meta_value );

}

function cfb_save_checkbox_data( $value , $comment_id ){

	$name = $value['name'];
	$meta_value = !empty( $_POST[ $name ] ) ? array_map( 'sanitize_text_field' , $_POST[ $name ] ) : ''; 
	update_comment_meta( $comment_id, $name, $meta_value );

}

function cfb_save_multiselect_data( $value , $comment_id ){

	$name = $value['name'];
	$removeEmptyKeys = !empty( $_POST[$name] ) ? array_filter( $_POST[$name] ) : array();
	update_comment_meta( $comment_id, $name, array_map( 'sanitize_text_field' , $removeEmptyKeys ) );

}

/**
* Get the comment metas
*/

add_filter( 'comment_text', 'cfb_modify_comment');
function cfb_modify_comment( $text ){

	$extra_fields_html = '<div class="cfb_custom_fields_wrapper">';
	$comment_id = get_comment_ID();
	$comment = get_comment( $comment_id ); 

	// Show custom fields to parent comment
	if( !is_object( $comment ) || $comment->comment_parent > 0 ){
		return $text;
	}

	global $post;
	$comment_form_id = cfb_get_comment_form_id( $post );
	$extra_fields = get_post_meta( $comment_form_id , 'comment_custom_fields' , true );

	if( !empty( $extra_fields ) && is_array( $extra_fields ) ){

		foreach ( $extra_fields as $key => $value ) {
			
			switch ( $value['type'] ) {

				case 'text':
				case 'textarea':
				case 'email':
				case 'date':

					if( !empty( $value['show_to_admin'] ) && !current_user_can('administrator') ){
						continue;
					}

					$class = !empty( $value['name'] ) ? 'cfb_' . sanitize_text_field( $value['name'] ) . '_wrapper' : ''; 
					$key = !empty( $value['name'] ) ? sanitize_text_field( $value['name'] ) : '';
					$label = !empty( $value['label'] ) ? sanitize_text_field( $value['label'] ) : '';

					$meta_value = get_comment_meta( $comment_id , $key, true );

					if( !empty( $meta_value ) ){
						$extra_fields_html .= '<div class="' . $class . '"><strong>' . $label . ' : </strong><span class="cfb_meta_value">' . $meta_value . '</span></div><div class="cfb_clear"></div>';	
					}
					
					break;

				case 'radio':
				case 'dropdown':

					if( !empty( $value['show_to_admin'] ) && !current_user_can('administrator') ){
						continue;
					}

					$extra_fields_html .= cfb_display_radio_dropdown_frontend( $value , $comment_id );
					
					break;

				case 'checkbox':
				case 'multi_select':

					if( !empty( $value['show_to_admin'] ) && !current_user_can('administrator') ){
						continue;
					}

					$extra_fields_html .= cfb_display_checkbox_multiselect_frontend( $value , $comment_id );
					
					break;

				case 'url':

					if( !empty( $value['show_to_admin'] ) && !current_user_can('administrator') ){
						continue;
					}

					$class = !empty( $value['name'] ) ? 'cfb_' . sanitize_text_field( $value['name'] ) . '_wrapper' : ''; 
					$key = !empty( $value['name'] ) ? sanitize_text_field( $value['name'] ) : '';
					$label = !empty( $value['label'] ) ? sanitize_text_field( $value['label'] ) : '';

					$meta_value = get_comment_meta( $comment_id , $key, true );

					if( !empty( $meta_value ) ){
						$extra_fields_html .= '<div class="' . $class . '"><strong>' . $label . ' : </strong><span class="cfb_meta_value">' . $meta_value . '</span></div><div class="cfb_clear"></div>';
					}
					
					break;

				case 'taxonomy':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) )
						continue;

					if( !empty( $value['show_to_admin'] ) && !current_user_can('administrator') ){
						continue;
					}

					if( $value['display_option'] == 'checkbox' || $value['display_option'] == 'multi_select' ){
						$extra_fields_html .= cfb_display_checkbox_multiselect_frontend( $value , $comment_id );
					} else {
						$extra_fields_html .= cfb_display_radio_dropdown_frontend( $value , $comment_id );
					}

					break;

				case 'file_upload':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) )
						continue;

					if( !empty( $value['show_to_admin'] ) && !current_user_can('administrator') ){
						continue;
					}

					$class = !empty( $value['name'] ) ? 'cfb_' . sanitize_text_field( $value['name'] ) . '_wrapper' : ''; 
					$key = !empty( $value['name'] ) ? sanitize_text_field( $value['name'] ) : '';
					$label = !empty( $value['label'] ) ? sanitize_text_field( $value['label'] ) : '';

					$meta_value = get_comment_meta( $comment_id , $key, true );

					if( !empty( $meta_value ) && is_array( $meta_value ) ){
						$extra_fields_html .= '<div class="' . $class . '"><strong>' . $label . ' : </strong><div class="cfb_meta_value">' . cfb_get_files_content( $meta_value ) . '</div></div><div class="cfb_clear"></div>';	
					}
						
					break;

				case 'google_maps':

					if( !cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) )
						continue;

					if( !empty( $value['show_to_admin'] ) && !current_user_can('administrator') ){
						continue;
					}

					$class = !empty( $value['name'] ) ? 'cfb_' . sanitize_text_field( $value['name'] ) . '_wrapper' : ''; 
					$key = !empty( $value['name'] ) ? sanitize_text_field( $value['name'] ) : '';
					$label = !empty( $value['label'] ) ? sanitize_text_field( $value['label'] ) : '';

					$meta_value = get_comment_meta( $comment_id , $key, true );

					if( !empty( $meta_value ) && is_array( $meta_value ) && count( array_filter( $meta_value ) ) == 3 ){

						// Display as text
						if( !empty( $value['display_as'] ) && $value['display_as'] == 'address' ){
							$extra_fields_html .= '<div class="' . $class . '"><strong>' . $label . ' : </strong><span class="cfb_meta_value">' . $meta_value['long_address'] . '</span></div><div class="cfb_clear"></div>';
						} else {
							$extra_fields_html .= '<div class="' . $class . '"><strong>' . $label . ' : </strong><span class="cfb_meta_value">' . cfb_display_google_map( $meta_value ) . '</span></div><div class="cfb_clear"></div>';
						}

					}				

					break;

			}

		}

	}

	/* Get the position of the custom fields */
	$position = get_option( 'cfb_show_custom_fields_position' , 'after_comment' );

	if( $position == 'after_comment' ){
		return '<p class="comment_description">' . $text . '</p>' . $extra_fields_html . '</div>';
	} else {
		return $extra_fields_html . '<div class="cfb-mb-10"></div><p class="comment_description">' . $text . '</p></div>';
	}	

}

function cfb_display_google_map( $value ){

	$key = wp_generate_password( 20 , false );

	ob_start(); ?>

	<div id="<?php echo 'map_' . $key; ?>" class="cfb_comment_list_google_map"></div>
	<script>
	  	function <?php echo 'display_google_' . $key; ?>() {

	    	mapCanvas = document.getElementById( "<?php echo 'map_' . $key; ?>" );
	    	var mapOptions = {
	      		center: new google.maps.LatLng( <?php echo $value['lat']; ?>, <?php echo $value['lng']; ?> ),
	      		zoom: 14,
		      	mapTypeId: google.maps.MapTypeId.ROADMAP,
		      	mapTypeControl: false,
		    }
		    var map = new google.maps.Map(mapCanvas, mapOptions);

		    var marker = new google.maps.Marker({
			    position: new google.maps.LatLng( <?php echo $value['lat']; ?>, <?php echo $value['lng']; ?> ),
			    map: map,
			});

		    marker.info = new google.maps.InfoWindow({
			  	content: "<?php echo $value['long_address']; ?>"
			});

			google.maps.event.addListener(marker, 'click', function() {
			  	marker.info.open(map, marker);
			});

		}

		jQuery(document).ready(function(){
			<?php echo 'display_google_' . $key; ?>();
		});

	</script>

	<?php

	return ob_get_clean();

}

function cfb_display_checkbox_multiselect_frontend( $value , $comment_id ){

	$class = !empty( $value['name'] ) ? 'cfb_' . sanitize_text_field( $value['name'] ) . '_wrapper' : ''; 
	$key = !empty( $value['name'] ) ? sanitize_text_field( $value['name'] ) : '';
	$label = !empty( $value['label'] ) ? sanitize_text_field( $value['label'] ) : '';

	$meta_value = get_comment_meta( $comment_id , $key, true );
	$meta_value = !is_array( $meta_value ) ? array() : $meta_value;

	$choices_array = cfb_get_choices_to_array( $value );
	$display_value = array();

	if( !empty( $meta_value ) && !empty( $choices_array ) ){

		foreach ( $choices_array as $key2 => $value2 ) {
			
			if( in_array( $key2 , $meta_value ) ){

				$display_value[] = $value2;

			}

		}

		return '<div class="' . $class . '"><strong>' . $label . ' : </strong><span class="cfb_meta_value">' . implode( ', ' , $display_value ) . '</span></div><div class="cfb_clear"></div>';	
	}

	return;

}

function cfb_display_radio_dropdown_frontend( $value , $comment_id ){

	$class = !empty( $value['name'] ) ? 'cfb_' . sanitize_text_field( $value['name'] ) . '_wrapper' : ''; 
	$key = !empty( $value['name'] ) ? sanitize_text_field( $value['name'] ) : '';
	$label = !empty( $value['label'] ) ? sanitize_text_field( $value['label'] ) : '';

	$meta_value = get_comment_meta( $comment_id , $key, true );

	$choices_array = cfb_get_choices_to_array( $value );

	if( !empty( $meta_value ) && !empty( $choices_array ) ){

		foreach ( $choices_array as $key2 => $value2 ) {
			
			if( $key2 == $meta_value ){
				$display_value = $value2;
			}

		}

		return '<div class="' . $class . '"><strong>' . $label . ' : </strong><span class="cfb_meta_value">' . $display_value . '</span></div><div class="cfb_clear"></div>';	
	}

	return;

}

function cfb_is_plugin_active( $plugin ) {
    return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
}

function cfb_get_choices_to_array( $value ){

	/* Filter Choices */

	if( !empty( $value['type'] ) && $value['type'] == 'taxonomy' && cfb_is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){
		$choices = cfb_get_taxonomy_choices( $value );
	} else {
		$choices = !empty( $value['choices'] ) ? esc_html( $value['choices'] ) : '';
	}	

	$choicesArray = explode( "\n", $choices );
	$radio_choices = array();

	if( !empty( $choicesArray ) && is_array( $choicesArray ) ){

		$explode_key_value = array();		

		foreach( $choicesArray as $value1 ){

			$explode_key_value = explode( ':' , $value1 , 2 );

			if( !empty( $explode_key_value[0] ) && !empty( $explode_key_value[1] ) ){
				
				$kirki_key =  sanitize_text_field( $explode_key_value[0] );
				$kirki_value =  sanitize_text_field( $explode_key_value[1] );

				$radio_choices[ $kirki_key ] = $kirki_value;

			}			

		}

	}

	return $radio_choices;

}

/**
* Hide Name, Email, Comment and Website
*/

add_action( 'wp_footer' , 'cfb_hide_predefined_fields' );
function cfb_hide_predefined_fields(){

	if( is_single() || is_page() ){

		global $post;
		
		$comment_form_id = cfb_get_comment_form_id( $post );
		$data = array();

		if( is_numeric( $comment_form_id ) ){ 

			$comment_form_details = get_post_meta( $comment_form_id, 'comment_custom_fields', true ); 

			if( !empty( $comment_form_details ) && is_array( $comment_form_details ) ){

				echo '<script>
				jQuery( document ).ready(function(){';

				foreach( $comment_form_details as $value ){

					switch ( $value['type'] ) {

						case 'name':
							
							if( !empty( $value['hide_field'] ) ){

								$default_name = !empty( $value['default'] ) ? esc_html( $value['default'] ) : 'Anonymous';

								echo "jQuery( '[name=author]' ).val( '" . $default_name . "' );";
								echo "jQuery( '[name=author]' ).hide();";
								echo "jQuery( '[name=author]' ).closest( 'p,div' ).hide();";

							}

							$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : 'Name';
							$label = $label . '<span class="required"> *</span>'; 

							echo "jQuery( '.comment-form-author label[for=author]' ).html('" . $label . "');";

							break;

						case 'predefined_email':
							
							if( !empty( $value['hide_field'] ) ){

								$default_name = !empty( $value['default'] ) ? sanitize_email( $value['default'] ) : 'anonymous@gmail.com';

								echo "jQuery( '[name=email]' ).val( '" . $default_name . "' );";
								echo "jQuery( '[name=email]' ).hide();";
								echo "jQuery( '[name=email]' ).closest( 'p,div' ).hide();";
							}

							$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : 'Email';
							$label = $label . '<span class="required"> *</span>'; 
							
							echo "jQuery( '.comment-form-email label[for=email]' ).html('" . $label . "');";

							break;

						case 'website':
							
							if( !empty( $value['hide_field'] ) ){

								$default_name = !empty( $value['default'] ) ? esc_url( $value['default'] ) : '';

								echo "jQuery( '[name=url]' ).val( '" . $default_name . "' );";
								echo "jQuery( '[name=url]' ).hide();";
								echo "jQuery( '[name=url]' ).closest( 'p,div' ).hide();";
							}

							$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : 'Website';
							
							echo "jQuery( '.comment-form-url label[for=url]' ).html('" . $label . "');";

							break;

						case 'comment':
							
							if( !empty( $value['hide_field'] ) ){

								$default_name = !empty( $value['default'] ) ? esc_html( $value['default'] ) : 'This is a test comment';
								$default_name = $default_name . rand(0,999999999999);

								echo "jQuery( '[name=comment]' ).val( '" . $default_name . "' );";
								echo "jQuery( '[name=comment]' ).hide();";
								echo "jQuery( '[name=comment]' ).closest( 'p,div' ).hide();";
								echo "jQuery( '.comment_description' ).css( 'display', 'none' );";
								echo "window.commentHide = true;";
								echo "window.commentDefaultValue = '" . $default_name . "';";

							}

							$label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : 'Comment';
							$label = $label . '<span class="required"> *</span>'; 
							
							echo "jQuery( '.comment-form-comment label[for=comment]' ).html('" . $label . "');";

							break;
						
						default:
							# code...
							break;
					}

				}

				echo '});</script>';

			}

		}

	}
} 