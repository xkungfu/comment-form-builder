jQuery(document).ready(function(){

	jQuery('.hide_meta_data_comment_holder').closest('.postbox-container').hide();

	jQuery('.wpuf-form-editor').sortable({
      	placeholder: "ui-state-highlight",
      	handle: '.wpuf-legend',
      	//start: gravaSelecoes,
      	stop: function( event, ui ) {
      		//atualizaSelecoes();
      		cfb_rearrange_order();
      	}
    });

    if( jQuery('form#post[action="comment.php"]').length > 0 ){
		jQuery('form#post[action="comment.php"]')[0].encoding = 'multipart/form-data';
	}

});

function storedCheckedValue() {
  	jQuery(".wpuf-form-editor li").find("input:radio").each(function () {
    	if ( jQuery(this).prop( "checked" ) )
      		jQuery(this).attr( "data-checked", "true" );
    	else
      		jQuery(this).attr( "data-checked", "false" );
  	});
}

function restoredCheckedValue() {
  	jQuery(".wpuf-form-editor li").find("input:radio").each(function () {
    	if (jQuery(this).attr("data-checked") == "true")
      		jQuery(this).prop("checked", true);
    	else
      		jQuery(this).prop("checked", false);
  	});
}

function cfb_check_duplicate_fields( $class , msg ){

	var status = false;
	jQuery( 'ul.wpuf-form-editor li' ).each( function(){

		if( jQuery(this).hasClass( $class ) ){

			alert( msg );
			status = true;

		}

	});

	if( status == true )
		return false;
	else
		return true;

}

/**
* Get custom fields by ajax
*/

jQuery( document ).on( 'click' , '.cfb_custom_fields_wrapper button:not(:disabled)' , function(){

	// If already has the user image field break the funtion
	if( jQuery(this).val() == 'user_image' && cfb_check_duplicate_fields( 'cf_user_image_field' , 'You cannot have multiple User Image field.' ) == false ){
		return;
	}

	// If already has the user image field break the funtion
	if( jQuery(this).val() == 'reCaptcha' && cfb_check_duplicate_fields( 'cfb_recaptcha_field' , 'You cannot have multiple reCaptcha.' ) == false ){
		return;
	}

	// If already has the Really Simple Captcha field break the funtion
	if( jQuery(this).val() == 'really_simple_captcha' && cfb_check_duplicate_fields( 'cfb_simple_captcha_field' , 'You cannot have multiple Really Simple Captcha.' ) == false ){
		return;
	}
	
	var selected = jQuery(this);
	var field_type,taxonomyName;

	// Check for taxonomy
	if( selected.attr('data-taxonomy') == 'true' ){
		field_type = 'taxonomy';
		taxonomyName = jQuery(this).val();
	} else {
		field_type = jQuery(this).val();
		taxonomyName = '';
	}
	
	var loader_html = '<span class="loader_gif"><img src="' + cfb_object.loader + '" /><span>';

	jQuery.ajax({
		url : cfb_object.ajaxUrl,
		type : 'post',
		dataType : 'json',
		data : {
			action : 'cfb_get_form_elements_html',
			field : field_type,
			taxonomy_name : taxonomyName,
			size : jQuery('.wpuf-form-editor li').length
		},
		beforeSend : function(){
			selected.prop( 'disabled' , true ).append( loader_html );
		},
		success : function( result ){
			selected.prop( 'disabled' , false ).find('.loader_gif').remove();
			jQuery('.wpuf-form-editor').append( result.content );
		}
	});

});

/**
* Toggle custom field
*/

jQuery(document).on( 'click' , '.wpuf-actions .wpuf-toggle', function(){
	jQuery(this).closest('li').find('.wpuf-form-holder').slideToggle();
})

/**
* Toggle All
*/

jQuery( document ).on( 'click' , '.toggle_all', function(){
	jQuery('.wpuf-form-holder').slideToggle();
});

/**
* Remove custom field 
*/

jQuery(document).on( 'click' , '.wpuf-actions .wpuf-remove', function(){
	var status = confirm( 'Are you sure you want to delete this?' );
	if( status == true ){
		jQuery(this).closest('li').remove();	
		cfb_rearrange_order();
	}	
});

/**
* Rearrange order of the array
*/

function cfb_rearrange_order(){

	// Store the checked radio properties
	storedCheckedValue();

	var i = 0;
	jQuery('.wpuf-form-editor li').each( function(){

		jQuery(this).find( 'input,textarea,select' ).each(function(){
			var name = jQuery(this).attr('name');
			jQuery(this).attr( 'name' , name.replace(/\d+/, i ) );
		});
		i++;

	});

	// Restore the checked radio properties
	restoredCheckedValue();

}

jQuery( document ).on( 'keyup' , '.meta_key' , function(){
	var value = jQuery(this).val();
	(/ /i).test(this.value)?this.value = this.value.replace(/ /ig,'_'):null;
});

jQuery( document ).on( 'change' , '.pages_checkbox' , function(){
	
	if( jQuery(this).is(':checked') ){
		jQuery('.all_pages_for_comments').slideToggle( 'fast' );
	} else {
		jQuery('.all_pages_for_comments').slideToggle( 'fast' );
	}

});

jQuery( document ).on( 'change' , '.select_all_pages' , function(){
	if( jQuery(this).is(':checked') ){
		jQuery('.all_pages').prop( 'checked' , true );
	} else {
		jQuery('.all_pages').prop( 'checked' , false );
	}
});

jQuery(document).on( 'change' , '.all_pages' , function(){

	if( !jQuery(this).is(':checked') ){
		jQuery('.select_all_pages').prop( 'checked' , false );
	}

});