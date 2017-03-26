<?php

add_action( 'add_meta_boxes_comment', 'cfb_extend_comment_add_meta_box' );
function cfb_extend_comment_add_meta_box() {
    add_meta_box( 
    	'cfb-comment-edit', 
    	__( 'Comment Metadata - Comment Form Builder' ), 
    	'cfb_extend_comment_meta_box', 
    	'comment', 
    	'normal', 
    	'high' 
    );
}

function cfb_extend_comment_meta_box( $comment ) {

	//echo '<pre>'; print_r($comment); echo '</pre>';

	/**
	* Show to the parent comment
	* Disable for the child comment
	*/

	if( !empty( $comment->comment_parent ) ){
		echo '<div class="hide_meta_data_comment_holder"></div>';
		return;
	}

	$post_id = $comment->comment_post_ID;
	$comment_id = $comment->comment_ID;

	$object = get_post( $post_id ); 

	$comment_form_id = cfb_get_comment_form_id( $object );

	if( empty( $comment_form_id ) ){
		echo '<div class="hide_meta_data_comment_holder"></div>';
		return;
	}

	$meta_fields = cfb_get_extra_fields( $object , $comment_id );

	if( empty( $meta_fields ) || !is_array( $meta_fields ) ){
		echo '<div class="hide_meta_data_comment_holder"></div>';
		return;
	}

	echo '<div class="edit_comment_wrapper">';
	foreach ( $meta_fields as $key => $value) {
		if( !empty( $value ) ){
			echo $value;
		}
	}
	echo '</div>';

}

/**
* Save edit comment data
*/

add_action( 'edit_comment', 'cfb_save_comment_edit_metafields' );
function cfb_save_comment_edit_metafields( $comment_id ){

	$commentdata = get_comment( $comment_id , ARRAY_A );
	cfb_save_comment_meta_data( $comment_id, $comment_approved = null , $commentdata );

}