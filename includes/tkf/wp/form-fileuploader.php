<?php
class TK_WP_Jquery_Fileuploader extends TK_WP_Form_Textfield{
		$before_element.= '';
		$after_element = '<input class="tk_fileuploader" type="button" value="' . __( 'Browse ...' ) . '" /><br /><img class="tk_image_preview" id="image_' . $id . '" />' . $after_element;	
		$args['before_element'] = $before_element;
		parent::__construct( $name, $args );
function tk_form_fileuploader( $name, $args = array(), $return_object = FALSE ){