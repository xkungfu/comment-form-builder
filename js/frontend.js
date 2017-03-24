jQuery(document).ready(function(){

	validateCommentForm();

	if( cfb_object.pro_version == 'active' ){

		var commentFormID = cfb_get_comment_form_id();
		if( jQuery( commentFormID ).length > 0 ){
			jQuery( commentFormID )[0].encoding = 'multipart/form-data';
		}

		// Set todays on datepicker when click on today's button
		jQuery.datepicker._gotoToday = function(id) { 
		    jQuery(id).datepicker('setDate', new Date()).datepicker('hide').blur(); 
		};

	}	

});

/**
* Add demo value to the recaptcha when click on reply comment on parent comment
*/

jQuery( document ).on( 'click' , '.comment-reply-link' , function(){
	jQuery( '#g-recaptcha-response' ).val('123456789');
});

function cfb_get_comment_form_id(){

	var commentFormID = ( cfb_object.comment_form_id == '' ? '#commentform' : '#' + cfb_object.comment_form_id );
	return commentFormID;

}

function validateCommentForm(){

	var commentFormID = cfb_get_comment_form_id();

	jQuery( commentFormID ).validate({
		errorClass : 'comment_fields_error error',
		ignore: ":hidden:not([name='g-recaptcha-response'])",
		errorElement: 'label',
		focusInvalid: false,
		errorPlacement: function(error, element) {

			if( element.attr('type') == 'radio' || element.attr('type') == 'checkbox' ) {
		    	jQuery(error).insertAfter(element.closest("span"));
		  	} else if( element.attr('type') == 'file' ){
		  		jQuery(error).insertAfter(element.closest(".jFiler"));
		  	} else if( element.hasClass( 'cfb_google_place_input' ) ){
		  		element.closest( '.extra_field_google_map' ).append( error );
		  	} else if( element.hasClass( 'g-recaptcha-response' ) ){
		  		element.closest( 'span' ).append( error );
		  	} else {
		    	error.insertAfter(element);
		  	}	

		},
		invalidHandler: function(form, validator) {

	        if (!validator.numberOfInvalids())
	            return;

	        setTimeout(function(){ 

	        	jQuery('html, body').animate({
		            scrollTop: jQuery( validator.errorList[0].element.closest('p') ).offset().top - cfb_object.offset
		        }, 1000);	

	       	}, 100);
	        

	    },
		rules : jQuery.parseJSON( cfb_object.rules ),
		messages : jQuery.parseJSON( cfb_object.messages ),
	});

}

jQuery.validator.addMethod( "email", function(value, element) {
	var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/i);
  	return this.optional( element ) || pattern.test(value);
});

jQuery.validator.addMethod( "checkSpaces" , function(value, element) { 	
  	var new_val = value.trim();
  	if( new_val.length > 0 ){
  		return true;
  	}
});

/**
* Doesn't need http:// or https:// or www.
*/

jQuery.validator.addMethod( "checkurl", function(value, element) {
	// now check if valid url
	var pattern = new RegExp(/(^|\s)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/);
  	return this.optional( element ) || pattern.test(value);
});

/**
* jQuery add method for multi select
*/ 

jQuery.validator.addMethod( "multi_select", function( value, element , params ) {
    var count = 0;

    jQuery(element).find('option:selected').each(function(){

    	if( jQuery(this).val() != '' ){
    		count++;
    	}

    });

    return count > 0;
});