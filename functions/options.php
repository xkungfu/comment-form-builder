<?php

add_action( 'admin_menu' , 'cfb_comment_form_options' , 999 );
function cfb_comment_form_options(){
	
	add_submenu_page( 
		'options-general.php', 
		'Comment Form Builder', 
		'Comment Form Builder',
    	'manage_options', 
    	'cfb',
    	'cfb_settings_callback'
    );

    add_action( 'admin_init', 'cfb_register_cfb_settings' );

}

function cfb_register_cfb_settings() {
	
	$settings = array(
		array(
			'name' => 'google_map_api',
			'sanitize_callback' => 'sanitize_text_field'
		),
		array(
			'name' => 'cfb_show_custom_fields_position',
			'sanitize_callback' => 'sanitize_text_field'
		),
		array(
			'name' => 'recaptcha_site_key',
			'sanitize_callback' => 'sanitize_text_field'
		),
		array(
			'name' => 'recaptcha_secret_key',
			'sanitize_callback' => 'sanitize_text_field'
		),
		array(
			'name' => 'cfb_comment_form_id',
			'sanitize_callback' => 'sanitize_text_field'
		),
		array(
			'name' => 'cfb_error_offset',
			'sanitize_callback' => 'intval'
		),
	);

	foreach ( $settings as $value) {
		register_setting( 'acf_settings_group', $value['name'] , $value['sanitize_callback'] );
	}

}

function cfb_settings_callback(){ 

	$cfb_show_custom_fields_position = get_option( 'cfb_show_custom_fields_position' , 'after_comment' ); ?>
	
	<div class="wrap">
	<h1>Comment Form Builder Settings</h1>

		<form method="post" action="options.php">

		    <?php settings_fields( 'acf_settings_group' ); ?>
		    <?php do_settings_sections( 'acf_settings_group' ); ?>

		    <table class="form-table">

		    	<tr valign="top">
		    		<th scope="row" class="heading">General Settings</th>
		    	</tr>

		        <tr valign="top">

			        <th scope="row">Show Custom Fields</th>

			        <td>
			        	<fieldset>

	                        <label>
	                            <input 
	                            name="cfb_show_custom_fields_position" 	                            
	                            type="radio" 
	                            class="cfb_show_custom_fields_position" 
	                            value="before_comment" 
	                            <?php checked( $cfb_show_custom_fields_position , 'before_comment' ); ?>>
	                            Before Comment 
	                        </label>

	                        <br>
	                        
	                        <label>
	                            <input 
	                            name="cfb_show_custom_fields_position" 
	                            type="radio" 
	                            class="cfb_show_custom_fields_position" 
	                            value="after_comment" 
	                            <?php checked( $cfb_show_custom_fields_position , 'after_comment' ); ?>>
	                                After Comment
	                        </label>

	                    </fieldset>
			        </td>

		        </tr>

		        <tr valign="top">

		       	 	<th scope="row">Comment Form ID</th>
		        	<td>
		        		<input size="50" type="text" name="cfb_comment_form_id" value="<?php echo esc_attr( get_option('cfb_comment_form_id') ); ?>" />
		        	</td>

		        </tr>

		        <tr valign="top">

		       	 	<th scope="row">Error Offset</th>
		        	<td>
		        		<input size="50" min="1" type="number" name="cfb_error_offset" value="<?php echo esc_attr( get_option('cfb_error_offset') ); ?>" />
		        	</td>

		        </tr>

		        <?php 
		        if( is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) ){ ?>

			        <tr valign="top">
			    		<th scope="row" class="heading">API's</th>
			    	</tr>
			         
			        <tr valign="top">

			       	 	<th scope="row">Google Map API Key</th>
			        	<td>
			        		<input size="50" type="text" name="google_map_api" value="<?php echo esc_attr( get_option('google_map_api') ); ?>" />
			        		<p class="description">Please provide a Google Map API. You can get Google Map API from <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key">here</a></p>
			        	</td>

			        </tr>

			        <tr valign="top">

			       	 	<th scope="row">reCAPTCHA Site Key</th>
			        	<td>
			        		<input size="50" type="text" name="recaptcha_site_key" value="<?php echo esc_attr( get_option('recaptcha_site_key') ); ?>" />
			        	</td>

			        </tr>

			        <tr valign="top">

			       	 	<th scope="row">reCAPTCHA Secret Key</th>
			        	<td>
			        		<input size="50" type="text" name="recaptcha_secret_key" value="<?php echo esc_attr( get_option('recaptcha_secret_key') ); ?>" />
			        		<p class="description">You can get reCAPTCHA API form <a target="_blank" href="https://www.google.com/recaptcha/">here</a></p>
			        	</td>

			        </tr>

			        <?php 

			    } ?>
		        
		    </table>
		    
		    <?php submit_button(); ?>

		</form>
	</div>

	<?php
}
