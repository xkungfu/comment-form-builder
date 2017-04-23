<?php

/**
* Rename meta box title
*/

add_action('in_admin_header','cfb_rename_metaboxes', 9999);
function cfb_rename_metaboxes(){
	global $wp_meta_boxes;
	$wp_meta_boxes['comment_form_builder']['side']['core']['submitdiv']['title'] = esc_html__( 'Form Elements', 'cfb' );
}

/**
 * Remove meta box(es).
 */

add_action( 'do_meta_boxes', 'cfb_remove_meta_boxes' , 9999 );
function cfb_remove_meta_boxes() {
	remove_meta_box( 'slugdiv', 'comment_form_builder', 'normal' );
	remove_meta_box( 'wpuf-custom-fields', 'comment_form_builder', 'normal' );
	remove_meta_box( 'wpuf-select-form', 'comment_form_builder', 'side' );
}

/**
* Register meta box(es).
*/

function cfb_register_editor_screen() {

    add_meta_box( 
    	'form_builder_screen', 
    	__( 'Form Builder Screen', 'cfb' ), 
    	'cfb_screen_editor_callback', 
    	'comment_form_builder',
    	'normal'
    );

}
add_action( 'add_meta_boxes', 'cfb_register_editor_screen' );
 
/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */

function cfb_screen_editor_callback( $post ) { ?>
   	
	<a href="javascript:void(0)" class="button toggle_all"><?php esc_html_e( 'Toggle All' , 'cfb' ); ?></a>

    <div class="wpuf-updated" style="display: block;">
        <p>Click on a form element to add to the editor</p>
    </div>

	<ul class="wpuf-form-editor">
		<?php
		cfb_get_saved_comment_custom_field( $post );
		?>
	</ul>
	
	<?php
}

function cfb_get_input_hide_field( $count , $value ){ ?>
    
    <div class="wpuf-form-rows">
        <label>&nbsp;</label>
        <p class="description">The default value will be used if below <code>Hide Field</code> label is checked.</p>
    </div>
    <div class="wpuf-form-rows required-field">
                
        <label>&nbsp;</label>

        <div class="wpuf-form-sub-fields">
            <label>
                <input 
                type="checkbox" 
                name="cfb_input[<?php echo $count; ?>][hide_field]" 
                value="1"
                <?php checked( ( $value ? 1 : 0 ) , 1 ); ?>> Hide Field
            </label>
        </div>

    </div>

    <?php
}

function cfb_get_author_name_backend( $count , $type , $value = null ){ ?>

    <li class="custom_field cf_author_name_field">

        <div class="wpuf-legend" title="Click and Drag to rearrange">
            <div class="wpuf-label"><?php echo ucfirst( str_replace( '_' , ' ' , $type ) ); ?></div>
            <div class="wpuf-actions">
                <a href="javascript:void(0)" class="wpuf-toggle">Toggle</a>
            </div>
        </div> 

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 

            $default = !empty( $value['default'] ) ? esc_html( $value['default'] ) : 'Anonymous';
            $label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : 'Name';

            cfb_get_field_input_label( $count , $label );
            cfb_get_input_default_value( $count , $default , $required = true );
            cfb_get_input_hide_field( $count , (!empty( $value['hide_field'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>                  
                                
        </div> 

    </li>

    <?php
}

function cfb_get_author_email_backend( $count , $type , $value = null ){ ?>

    <li class="custom_field cf_author_email_field">

        <div class="wpuf-legend" title="Click and Drag to rearrange">
            <div class="wpuf-label"><?php echo ucfirst( str_replace( '_' , ' ' , 'Email' ) ); ?></div>
            <div class="wpuf-actions">
                <a href="javascript:void(0)" class="wpuf-toggle">Toggle</a>
            </div>
        </div>  

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 

            $default = !empty( $value['default'] ) ? esc_html( $value['default'] ) : 'anonymous@gmail.com';
            $label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : 'Email';

            cfb_get_field_input_label( $count , $label );

            cfb_get_input_default_value( $count , $default , $required = true , $input_type = 'email' );
            cfb_get_input_hide_field( $count , (!empty( $value['hide_field'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>                  
                                
        </div> 

    </li>

    <?php
}

function cfb_get_author_website_backend( $count , $type , $value = null ){ ?>

    <li class="custom_field cf_author_website_field">

        <div class="wpuf-legend" title="Click and Drag to rearrange">
            <div class="wpuf-label"><?php echo ucfirst( str_replace( '_' , ' ' , $type ) ); ?></div>
            <div class="wpuf-actions">
                <a href="javascript:void(0)" class="wpuf-toggle">Toggle</a>
            </div>
        </div>  

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 

            $default = !empty( $value['default'] ) ? esc_url( $value['default'] ) : '';
            $label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : 'Website';

            cfb_get_field_input_label( $count , $label );

            cfb_get_input_default_value( $count , $default , $required = false , $input_type = 'url' );
            cfb_get_input_hide_field( $count , (!empty( $value['hide_field'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>                  
                                
        </div> 

    </li>

    <?php
}

function cfb_get_author_comment_backend( $count , $type , $value = null ){ ?>

    <li class="custom_field cf_author_comment_field">

        <div class="wpuf-legend" title="Click and Drag to rearrange">
            <div class="wpuf-label"><?php echo ucfirst( str_replace( '_' , ' ' , $type ) ); ?></div>
            <div class="wpuf-actions">
                <a href="javascript:void(0)" class="wpuf-toggle">Toggle</a>
            </div>
        </div>  

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 

            $default = !empty( $value['default'] ) ? esc_html( $value['default'] ) : 'This is a test comment';

            $label = !empty( $value['label'] ) ? esc_html( $value['label'] ) : 'Comment';

            cfb_get_field_input_label( $count , $label );

            cfb_get_input_default_value( $count , $default , $required = true , $input_type = 'text' );
            cfb_get_input_hide_field( $count , (!empty( $value['hide_field'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>                  
                                
        </div> 

    </li>

    <?php
}

function cfb_get_saved_comment_custom_field( $post ){

	$data = get_post_meta( $post->ID , 'comment_custom_fields', true );

	if( empty( $data ) || !is_array( $data ) ){
        cfb_get_author_comment_backend( $count = 0, $type = 'comment' , $value = null );
        cfb_get_author_name_backend( $count = 1, $type = 'name' , $value = null );
        cfb_get_author_email_backend( $count = 2, $type = 'predefined_email' , $value = null );
        cfb_get_author_website_backend( $count = 3, $type = 'website' , $value = null );
		return;
	}

	foreach( $data as $key => $value ){

		switch ( $value['type'] ) {

            case 'comment':
                cfb_get_author_comment_backend( $key , $type = 'comment' , $value );
                break;

            case 'name':
                cfb_get_author_name_backend( $key , $type = 'name' , $value );
                break;

            case 'predefined_email':
                cfb_get_author_email_backend( $key , $type = 'predefined_email' , $value );
                break;

            case 'website':
                cfb_get_author_website_backend( $key , $type = 'website' , $value );
                break;

			case 'text':
				cfb_get_textbox_backend( $key , $type = 'text' , $value );
				break;

			case 'textarea':
				cfb_get_textarea_backend( $key , $type = 'textarea' , $value );
				break;

			case 'radio':
				cfb_get_radio_backend( $key , $type = 'radio' , $value );
				break;

			case 'checkbox':
				cfb_get_checkbox_backend( $key , $type = 'checkbox' , $value );
				break;

			case 'dropdown':
				cfb_get_dropdown_backend( $key , $type = 'dropdown' , $value );
				break;	

			case 'multi_select':
				cfb_get_multi_select_backend( $key , $type = 'multi_select' , $value );
				break;	

			case 'url':
				cfb_get_url_backend( $key , $type = 'url' , $value );
				break;	

			case 'email':
				cfb_get_email_backend( $key , $type = 'email' , $value );
				break;

			case 'section_break':
				cfb_get_section_break_backend( $key , $type = 'section_break' , $value );
				break;	

			case 'HTML':
				cfb_get_html_backend( $key , $type = 'HTML' , $value );
				break;

            case 'taxonomy':

                if( is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) )
                    cfb_get_taxonomy_backend( $key , $type = 'taxonomy' , $value , $value['taxonomy'] );

                break;

            case 'date':

                if( is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) )
                    cfb_get_date_backend( $key , $type = 'date' , $value );

                break;

            case 'file_upload':

                if( is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) )
                    cfb_get_file_upload_backend( $key , $type = 'file_upload' , $value );

                break;

            case 'user_image':

                if( is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) )
                    cfb_get_user_image_backend( $key , $type = 'user_image' , $value );
                
                break;

            case 'google_maps':

                if( is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) )
                    cfb_get_google_maps_backend( $key , $type = 'google_maps' , $value );

                break;

            case 'reCaptcha':

                if( is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) )
                    cfb_get_recaptcha_backend( $key , $type = 'reCaptcha' , $value );

                break;

            case 'really_simple_captcha':

                if( is_plugin_active( 'pro-addons-comment-form-builder/index.php' ) )
                    cfb_get_really_simple_captcha_backend( $key , $type = 'really_simple_captcha' , $value );

                break;

			default:
				# code...
				break;
		}

	}

}

function cfb_get_field_label( $type ){ ?>

	<div class="wpuf-legend" title="Click and Drag to rearrange">
        <div class="wpuf-label">Custom Field : <?php echo ucfirst( str_replace( '_' , ' ' , $type ) ); ?></div>
        <div class="wpuf-actions">
            <a href="javascript:void(0)" class="wpuf-remove">Remove</a>
            <a href="javascript:void(0)" class="wpuf-toggle">Toggle</a>
        </div>
    </div>	

	<?php
}

function cfb_get_input_show_to_admin( $count , $value ){ ?>
    
    <div class="wpuf-form-rows required-field">
                
        <label>&nbsp;</label>

        <div class="wpuf-form-sub-fields">
            <label>
                <input 
                type="checkbox" 
                name="cfb_input[<?php echo $count; ?>][show_to_admin]" 
                value="1"
                <?php checked( ( $value ? 1 : 0 ) , 1 ); ?>> Show to admin only
            </label>
        </div>

    </div>

    <?php
}

function cfb_get_required_field( $count , $value ){ 

	if( $value == 'yes' ){
		$checked_yes = 'checked="checked"';
		$checked_no = '';
	} else {
		$checked_no = 'checked="checked"';
		$checked_yes = '';
	}?>
	
	<div class="wpuf-form-rows required-field">
            	
    	<label>Required</label>

        <div class="wpuf-form-sub-fields">
            <label>
            	<input 
            	type="radio" 
            	name="cfb_input[<?php echo $count; ?>][required]" 
            	value="yes" 
            	<?php echo $checked_yes; ?>>Yes
            </label>
            <label>
            	<input 
            	type="radio" 
            	name="cfb_input[<?php echo $count; ?>][required]" 
            	value="no"
            	<?php echo $checked_no; ?>>No
            </label>
        </div>

    </div>

	<?php
}

function cfb_get_field_input_label( $count , $value ){ ?>
	
	<div class="wpuf-form-rows">
        <label>Field Label <span class="required">*</span></label>
        <input type="text" data-type="label" name="cfb_input[<?php echo $count; ?>][label]" value="<?php echo $value; ?>" class="" required>
    </div>

	<?php
}

function cfb_get_field_title( $count , $value ){ ?>
	
	<div class="wpuf-form-rows">
        <label>Title <span class="required">*</span></label>
        <input type="text" data-type="label" name="cfb_input[<?php echo $count; ?>][title]" value="<?php echo $value; ?>" class="" required>
    </div>

	<?php
}

function cfb_get_input_meta_key( $count , $value ){ ?>
	
	<div class="wpuf-form-rows">
        <label>Meta Key <span class="required">*</span></label>
        <input 
        type="text" 
        data-type="name" 
        name="cfb_input[<?php echo $count; ?>][name]" value="<?php echo $value; ?>" class="meta_key" title="" required>
    </div>

	<?php
}

function cfb_get_input_help_text( $count , $value ){ ?>
	
	<div class="wpuf-form-rows">
        <label>Help text</label>
        <textarea name="cfb_input[<?php echo $count; ?>][help]" class="" title=""><?php echo $value; ?></textarea>
    </div>

	<?php
}

function cfb_get_input_html_text( $count , $value ){ ?>
	
	<div class="wpuf-form-rows">
        <label>HTML Codes</label>
        <textarea name="cfb_input[<?php echo $count; ?>][html]" class="" title=""><?php echo $value; ?></textarea>
    </div>
    
    <div class="wpuf-form-rows">
        <label>&nbsp;</label>
        <p class="description">You can also add shortcodes.</p>
    </div>

	<?php
}

function cfb_get_input_choices( $count , $value ){ ?>
	
	<div class="wpuf-form-rows">
        <label>
        	Choices <span class="required">*</span>
        	<div class="help_text_backend">
	        	<br>Enter your choices one per line
	        	<br><br>
	        	red : Red
	        	<br>
	        	blue : Blue
	        </div>
        </label>
        <textarea required name="cfb_input[<?php echo $count; ?>][choices]" class="textarea_choices" title=""><?php echo $value; ?></textarea>
    </div>

	<?php
}

function cfb_get_input_css( $count , $value ){ ?>
	<div class="wpuf-form-rows">
        <label>CSS Class Name</label>
        <input type="text" name="cfb_input[<?php echo $count; ?>][css]" value="<?php echo $value; ?>" class="" title="">
    </div>
	<?php
}

function cfb_get_input_placeholder( $count , $value ){ ?>
	<div class="wpuf-form-rows">
        <label>Placeholder text</label>
        <input type="text" class="" name="cfb_input[<?php echo $count; ?>][placeholder]" title="" value="<?php echo $value; ?>">
    </div>
	<?php
}

function cfb_get_input_default_value( $count , $value , $required = false , $type = 'text' ){ ?>
	<div class="wpuf-form-rows">
        <label>Default value <?php echo ( $required == true ? '<span class="required"> *</span>' : '' );  ?></label>
        <input 
        type="<?php echo $type; ?>" 
        class="" 
        name="cfb_input[<?php echo $count; ?>][default]" 
        <?php echo ( $required == true ? 'required' : '' );  ?>  
        value="<?php echo $value; ?>">
    </div>
	<?php
}

function cfb_get_input_size( $count , $value ){ ?>
    <div class="wpuf-form-rows">
        <label>Size</label>
        <input type="number" name="cfb_input[<?php echo $count; ?>][size]" value="<?php echo empty( $value ) ? 40 : (int) $value; ?>">
    </div>
    <?php
}

function cfb_get_input_rows( $count , $value ){ ?>
	<div class="wpuf-form-rows">
        <label>Rows</label>
        <input type="number" class="" name="cfb_input[<?php echo $count; ?>][rows]" title="" value="<?php echo empty( $value ) ? 5 : $value; ?>">
    </div>
	<?php
}

function cfb_get_input_columns( $count , $value ){ ?>
	<div class="wpuf-form-rows">
        <label>Columns</label>
        <input type="number" class="" name="cfb_input[<?php echo $count; ?>][columns]" title="" value="<?php echo empty( $value ) ? 45 : $value; ?>">
    </div>
	<?php
}

function cfb_get_select_text( $count , $value ){ ?>
	<div class="wpuf-form-rows">
        <label>Select Text</label>
        <input type="text" class="" name="cfb_input[<?php echo $count; ?>][select_text]" title="" value="<?php echo !empty( $value ) ? $value : '- select -'; ?>">
    </div>
	<?php
}

function cfb_get_input_type( $count, $type ){ ?>
	<input type="hidden" name="cfb_input[<?php echo $count; ?>][type]" value="<?php echo $type; ?>">
	<?php
}

function cfb_get_taxonomy_type( $count, $taxonomy ){ ?>
    <input type="hidden" name="cfb_input[<?php echo $count; ?>][taxonomy]" value="<?php echo $taxonomy; ?>">
    <?php
}

function cfb_get_textbox_backend( $count , $type , $value = null ){ ?>

	<li class="custom_field cf_text_field">

		<?php cfb_get_field_label( $type ); ?>

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 
            cfb_get_required_field( $count , $value['required'] );
            cfb_get_field_input_label( $count , $value['label'] );
            cfb_get_input_meta_key( $count , $value['name'] );
            cfb_get_input_help_text( $count , $value['help'] );
            cfb_get_input_css( $count , $value['css'] );
            cfb_get_input_placeholder( $count , $value['placeholder'] );
            cfb_get_input_default_value( $count , $value['default'] );
            cfb_get_input_size( $count , $value['size'] );
            cfb_get_input_show_to_admin( $count , (!empty( $value['show_to_admin'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>	       	        
                                
        </div>

	</li>

	<?php
}

function cfb_get_url_backend( $count , $type , $value = null ){ ?>

	<li class="custom_field cf_url">

		<?php cfb_get_field_label( $type ); ?>

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 
            cfb_get_required_field( $count , $value['required'] );
            cfb_get_field_input_label( $count , $value['label'] );
            cfb_get_input_meta_key( $count , $value['name'] );
            cfb_get_input_help_text( $count , $value['help'] );
            cfb_get_input_css( $count , $value['css'] );
            cfb_get_input_placeholder( $count , $value['placeholder'] );
            cfb_get_input_default_value( $count , $value['default'] );
            cfb_get_input_size( $count , $value['size'] );
            cfb_get_input_show_to_admin( $count , (!empty( $value['show_to_admin'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>	       	        
                                
        </div>

	</li>

	<?php
}

function cfb_get_email_backend( $count , $type , $value = null ){ ?>

	<li class="custom_field cf_email">

		<?php cfb_get_field_label( $type ); ?>

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 
            cfb_get_required_field( $count , $value['required'] );
            cfb_get_field_input_label( $count , $value['label'] );
            cfb_get_input_meta_key( $count , $value['name'] );
            cfb_get_input_help_text( $count , $value['help'] );
            cfb_get_input_css( $count , $value['css'] );
            cfb_get_input_placeholder( $count , $value['placeholder'] );
            cfb_get_input_default_value( $count , $value['default'] );
            cfb_get_input_size( $count , $value['size'] );
            cfb_get_input_show_to_admin( $count , (!empty( $value['show_to_admin'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>	       	        
                                
        </div>

	</li>

	<?php
}

function cfb_get_section_break_backend( $count , $type , $value = null ){ ?>

	<li class="custom_field cf_section_break">

		<?php cfb_get_field_label( $type ); ?>

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 
            cfb_get_field_title( $count , $value['title'] );
            cfb_get_input_help_text( $count , $value['help'] );
            cfb_get_input_type( $count, $type );
            ?>	       	        
                                
        </div>

	</li>

	<?php
}

function cfb_get_html_backend( $count , $type , $value = null ){ ?>

	<li class="custom_field cf_html">

		<?php cfb_get_field_label( $type ); ?>

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 
            cfb_get_input_html_text( $count , $value['html'] );
            cfb_get_input_type( $count, $type );
            ?>	       	        
                                
        </div>

	</li>

	<?php
}

function cfb_get_dropdown_backend( $count , $type , $value = null ){ ?>

	<li class="custom_field cf_dropdown">

		<?php cfb_get_field_label( $type ); ?>

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 
            cfb_get_required_field( $count , $value['required'] );
            cfb_get_field_input_label( $count , $value['label'] );
            cfb_get_input_meta_key( $count , $value['name'] );
            cfb_get_select_text( $count , $value['select_text'] );
            cfb_get_input_choices( $count , $value['choices'] );
            cfb_get_input_help_text( $count , $value['help'] );
            cfb_get_input_css( $count , $value['css'] );
            cfb_get_input_show_to_admin( $count , (!empty( $value['show_to_admin'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>	       	        
                                
        </div>

	</li>

	<?php
}

function cfb_get_multi_select_backend( $count , $type , $value = null ){ ?>

	<li class="custom_field cf_multi_select">

		<?php cfb_get_field_label( $type ); ?>

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 
            cfb_get_required_field( $count , $value['required'] );
            cfb_get_field_input_label( $count , $value['label'] );
            cfb_get_input_meta_key( $count , $value['name'] );
            cfb_get_select_text( $count , $value['select_text'] );
            cfb_get_input_choices( $count , $value['choices'] );
            cfb_get_input_help_text( $count , $value['help'] );
            cfb_get_input_css( $count , $value['css'] );
            cfb_get_input_show_to_admin( $count , (!empty( $value['show_to_admin'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>	       	        
                                
        </div>

	</li>

	<?php
}

function cfb_get_textarea_backend( $count , $type , $value = null ){ ?>

	<li class="custom_field cf_textarea">

		<?php cfb_get_field_label( $type ); ?>

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 
            cfb_get_required_field( $count , $value['required'] );
            cfb_get_field_input_label( $count , $value['label'] );
            cfb_get_input_meta_key( $count , $value['name'] );
            cfb_get_input_help_text( $count , $value['help'] );
            cfb_get_input_css( $count , $value['css'] );
            cfb_get_input_placeholder( $count , $value['placeholder'] );
            cfb_get_input_default_value( $count , $value['default'] );
            cfb_get_input_rows( $count , $value['rows'] );
            cfb_get_input_columns( $count , $value['columns'] );
            cfb_get_input_show_to_admin( $count , (!empty( $value['show_to_admin'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>	       	        
                                
        </div>

	</li>

	<?php
}

function cfb_get_radio_backend( $count , $type , $value = null ){ ?>

	<li class="custom_field cf_radio">

		<?php cfb_get_field_label( $type ); ?>

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 
            cfb_get_required_field( $count , $value['required'] );
            cfb_get_field_input_label( $count , $value['label'] );
            cfb_get_input_meta_key( $count , $value['name'] );
            cfb_get_input_choices( $count , $value['choices'] );
            cfb_get_input_help_text( $count , $value['help'] );
            cfb_get_input_default_value( $count , $value['default'] );
            cfb_get_input_css( $count , $value['css'] );
            cfb_get_input_show_to_admin( $count , (!empty( $value['show_to_admin'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>	       	        
                                
        </div>

	</li>

	<?php
}

function cfb_get_checkbox_backend( $count , $type , $value = null ){ ?>

	<li class="custom_field cf_checkbox">

		<?php cfb_get_field_label( $type ); ?>

        <div class="wpuf-form-holder" style="display: block;">
            
            <?php 
            cfb_get_required_field( $count , $value['required'] );
            cfb_get_field_input_label( $count , $value['label'] );
            cfb_get_input_meta_key( $count , $value['name'] );
            cfb_get_input_choices( $count , $value['choices'] );
            cfb_get_input_help_text( $count , $value['help'] );
            cfb_get_input_default_value( $count , $value['default'] );
            cfb_get_input_css( $count , $value['css'] );
            cfb_get_input_show_to_admin( $count , (!empty( $value['show_to_admin'] ) ? 1 : 0) );
            cfb_get_input_type( $count, $type );
            ?>	       	        
                                
        </div>

	</li>

	<?php
}

function cfb_reset_comment_display( $comment_display , $post_id ){

    if( is_array( $comment_display ) && !empty( $comment_display ) ){

        foreach ( $comment_display as $key => $value) {
            
            if( $key != 'page' && $value == $post_id ){

                unset( $comment_display[$key] );

            } elseif(  $key == 'page' ) {

                foreach( $value as $key_2 => $page_id ){

                    if( $post_id == $page_id ){

                        unset( $comment_display['page'][$key_2] );

                    }

                }

            }

        }

    }

    return $comment_display;

}

/**
* Save meta box content.
*
* @param int $post_id Post ID
*/

add_action( 'save_post_comment_form_builder', 'cfb_save_meta_box' );
function cfb_save_meta_box( $post_id ) {
    
	$comment_display_db = get_option( 'cfb_comment_display' );
	$comment_display = !empty( $comment_display_db ) ? $comment_display_db : array();

    $comment_display = cfb_reset_comment_display( $comment_display , $post_id );

	if( !empty( $_POST['display_position'] ) && is_array( $_POST['display_position'] ) ){

		foreach ( $_POST['display_position'] as $key => $value ) {
			
			if( $key != 'pages' ){
				$comment_display[$value] = $post_id;
			} elseif( $key == 0 && !is_array( $value ) ){
				$comment_display[$value] = $post_id;
			} 

			if( $key == 'pages' && is_array( $value ) ){

				foreach( $value as $key_2 => $page_id ){

					$comment_display['page'][$page_id] = $post_id;

				}

			}		

		}

	}

	update_option( 'cfb_comment_display' , $comment_display );

	if( !empty( $_POST['cfb_input'] ) && is_array( $_POST['cfb_input'] ) ){

		update_post_meta( $post_id, 'comment_custom_fields' , cfb_sanitize_array( $_POST['cfb_input'] ) );

	}

}

/**
* Sanitize multidimentional array
*/

function cfb_sanitize_array($data = array()) {
	if (!is_array($data) || !count($data)) {
		return array();
	}
	foreach ($data as $k => $v) {
		if (!is_array($v) && !is_object($v)) {

			if( $k == 'choices' ){
				$data[$k] = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $v ) ) );
			} elseif( $k == 'html' ){
				$data[$k] = wp_kses_post( $v );
			} else {
				$data[$k] = sanitize_text_field( $v );
			}			

		}
		if (is_array($v)) {
			$data[$k] = cfb_sanitize_array($v);
		}
	}
	return $data;
}

add_action( 'post_submitbox_minor_actions' , 'cfb_custom_fields' );
function cfb_custom_fields( $post ){ 

    if( !is_object( $post ) || $post->post_type != 'comment_form_builder' ){
        return;
    }

	foreach( cfb_get_backend_custom_fields_btn() as $value ){ ?>

		<div class="<?php echo $value['class']; ?>">
			
			<?php 
			echo $value['before_title'] . $value['title'] . $value['after_title'];

			foreach( $value['fields'] as $field => $field_value ){ 

                $tax_status = !empty( $field_value[0] ) ? 'true' : 'false'; ?>

				<button
				type="button" 
				href="javascript:void(0)" 
                data-taxonomy="<?php echo $tax_status; ?>" 
				class="button"
				value="<?php echo str_replace( ' ' , '_', $field ); ?>"
				<?php 
				if( !empty( $field_value['attr'] ) ){
					echo esc_html( $field_value['attr'] );
				} ?>
				>
					<?php echo str_replace( '_', ' ', $field ); ?>
				</button>

				<?php
			}
			?>

		</div>

		<?php

	} ?>

	<div class="custom_field_display_wrapper mb-20">
		<h4>Where do you want to show the custom fields?</h4>
		<?php 
		cfb_get_all_public_post_types();
		?>
	</div>

	<?php

}

function cfb_get_all_public_post_types(){

	$args = array(
		'public'   => true,
	);

	$post_types = get_post_types( $args , 'names' );

	unset( $post_types['attachment'] , $post_types['revision'], $post_types['nav_menu_item'], $post_types['comment_form_builder'], $post_types['page'] , $post_types['product'] );

	$comment_display = get_option( 'cfb_comment_display' );

	if( empty( $comment_display ) || !is_array( $comment_display ) ){
		$default_checked = 'checked';
	} 

	foreach( $post_types as $key => $value ){ 

		$strip_underscore = str_replace( '_' , ' ' , $value );
		$strip_dash = str_replace( '-' , ' ' , $strip_underscore ); 
		$default_checked = cfb_get_post_type_checked( $comment_display , $key ); ?>

		<label>
			<input type="checkbox" name="display_position[]" value="<?php echo $key; ?>" <?php echo $default_checked; ?>>
			<?php echo ucwords( $strip_dash ); ?>
		</label>

		<?php
	} 

	$pages_check = cfb_pages_check( $comment_display ); ?>

	<label>
		<input type="checkbox" class="pages_checkbox" <?php echo $pages_check; ?>>
		Page
	</label>

	<div 
	class="all_pages_for_comments" 
	style="display: <?php echo ( $pages_check == 'checked' ? 'block' : 'none' ); ?>;">
		<?php 
		cfb_get_all_pages_backend( $comment_display );
		?>
	</div>

	<?php

}

function cfb_pages_check( $comment_display ){

	if( is_array( $comment_display ) && array_key_exists( 'page' , $comment_display ) ){
		return 'checked';
	}
	return;

}

function cfb_get_post_type_checked( $comment_display , $selected ){

	global $post;

	if( !empty( $comment_display[$selected] ) && $comment_display[$selected] == $post->ID ){
		return 'checked';
	}

	//echo '<pre>'; print_r( $comment_display ); echo '</pre>';
	//echo '<pre>'; print_r( $selected ); echo '</pre>';

	return;

}

function cfb_get_all_pages_backend( $comment_display ){

	$args = array(
		'post_type' => 'page',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'order' => 'ASC',
		'orderby' => 'name'
	);

	$query_pages = new WP_Query( $args );

	if( $query_pages->have_posts() ):

		foreach ( $query_pages->get_posts() as $p) {

	      	echo '<label><input ' . cfb_single_page_checked( $p->ID , $comment_display ) . ' class="all_pages" type="checkbox" name="display_position[pages][]" value="' . $p->ID . '">' . get_the_title( $p->ID ) . '</label>';
	    }

	endif;

}

function cfb_single_page_checked( $page_id , $comment_display ){

	global $post;

	if( !empty( $comment_display['page'] ) && is_array( $comment_display['page'] ) ){

		foreach( $comment_display['page'] as $db_page_id => $db_post_id ){

			if( $post->ID == $db_post_id && $db_page_id == $page_id ){

				return 'checked';

			}

		}

	}

	return;
}

function cfb_get_backend_custom_fields_btn(){

	$data = array(
		array(
			'class' => 'cfb_custom_fields_wrapper mt-10 mb-20',
			'title' => esc_html__( 'Custom Fields', 'cfb' ),
			'before_title' => '<h4>',
			'after_title' => '</h4>',
			'fields' => array(
				'text' => array(),
				'textarea' => array(),
				'radio' => array(),
				'checkbox' => array(),
				'dropdown' => array(),
				'multi select' => array(),
				'url' => array(),
				'email' => array(),
			)
		),
		array(
			'class' => 'cfb_custom_fields_wrapper mt-15 mb-10 custom_taxomomies',
			'title' => esc_html__( 'Custom Taxonomies', 'cfb' ),
			'before_title' => '<h4>',
			'after_title' => '</h4>',
			'fields' => array(
				'Available in PRO VERSION' => array(
					'attr' => ''
				),
			)
		),
		array(
			'class' => 'cfb_custom_fields_wrapper mt-15 mb-20',
			'title' => esc_html__( 'Others', 'cfb' ),
			'before_title' => '<h4>',
			'after_title' => '</h4>',
			'fields' => array(
				'section break' => array(),
				'HTML' => array(),
			)
		),
		array(
			'class' => 'cfb_custom_fields_wrapper mt-10 mb-20',
			'title' => esc_html__( 'PRO Custom Fields', 'cfb' ),
			'before_title' => '<h4>',
			'after_title' => '</h4>',
			'fields' => array(
				'date' => array(
					'attr' => ' disabled="disabled" '
				),
				'file upload' => array(
					'attr' => ' disabled="disabled" '
				),
				'google maps' => array(
					'attr' => ' disabled="disabled" '
				),
				'reCaptcha' => array(
					'attr' => ' disabled="disabled" '
				),
				'really simple captcha' => array(
					'attr' => ' disabled="disabled" '
				),
				'user image' => array(
					'attr' => ' disabled="disabled" '
				)
			)
		),
	);

	return apply_filters( 'cfb_backend_custom_fields_btn' , $data );

}

add_action( 'wp_ajax_cfb_get_form_elements_html' , 'cfb_get_form_elements_html' );
function cfb_get_form_elements_html(){

	$field_types = array(
		array(
			'field' => 'text',
			'callback' => 'cfb_get_textbox_backend'
		),
		array(
			'field' => 'textarea',
			'callback' => 'cfb_get_textarea_backend'
		),
		array(
			'field' => 'radio',
			'callback' => 'cfb_get_radio_backend'
		),
		array(
			'field' => 'checkbox',
			'callback' => 'cfb_get_checkbox_backend'
		),
		array(
			'field' => 'dropdown',
			'callback' => 'cfb_get_dropdown_backend'
		),
		array(
			'field' => 'multi_select',
			'callback' => 'cfb_get_multi_select_backend'
		),
		array(
			'field' => 'url',
			'callback' => 'cfb_get_url_backend'
		),
		array(
			'field' => 'email',
			'callback' => 'cfb_get_email_backend'
		),
		array(
			'field' => 'section_break',
			'callback' => 'cfb_get_section_break_backend'
		),
		array(
			'field' => 'HTML',
			'callback' => 'cfb_get_html_backend'
		),
        array(
            'field' => 'taxonomy',
            'callback' => 'cfb_get_taxonomy_backend'
        ),
	);

	$data = array_map( 'sanitize_text_field' , $_POST );
	$content = '';

	$available_field_types = apply_filters( 'available_field_types' , $field_types );

	foreach( $available_field_types as $field_type ){

		if( $field_type['field'] == $data['field'] ){

			$callback = $field_type['callback'];

			ob_start();

            if( $data['field'] == 'taxonomy' ){
                $callback( $data['size'] , $data['field'] , $value = null , $data['taxonomy_name'] ); 
            } else {
                $callback( $data['size'] , $data['field'] );    
            }			

			$content = ob_get_clean();

		}

	}

	echo json_encode(
		array(
			'content' => $content
		)
	);

	die;

}

add_action( 'add_meta_boxes', 'cfb_register_meta_boxes' );
function cfb_register_meta_boxes() {
    add_meta_box( 
        'cfb_about_plugin', 
        'Support/Bugs/Extra Features', 
        'cfb_about_plugin', 
        'comment_form_builder',
        'side',
        'high'
    );
}

function cfb_about_plugin( $post ) { ?>
    
    <br>
    <strong>Did you find a Bug / Want Extra Features on the plugin?</strong>

    <p>Please report any bug on the <a target="_blank" href="https://github.com/ravishakya/comment-form-builder/issues/new?title=Bug%20Report:%20%3Cshort%20description%3E&labels=bug">GitHub Page</a> rather than on the WordPress Support page.</p>

    <?php
}