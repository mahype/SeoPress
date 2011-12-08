<?php
/**
 * SeoPress settings page in admin
 *
 * @package SeoPress
 * @author Sven Lehnert, Sven Wagener
 * @copyright Copyright (C) Themekraft.com
 **/
function seopress_options( $content ){
	
	global $seopress_plugin_url;
	
	/*
	 * Adding display
	 */	
	
	$form = new TK_WP_Form( 'seopress_options', 'seopress_options' );
	
	/*
	 * Adding jqueryui tabs
	 */		
	$tabs = new	TK_Jqueryui_Tabs();
	
	require_once( dirname(__FILE__) . '/options_seo.tab.php' );
	
	$tabs->add_tab( 'cap_main_blog', __ ('Seo', 'seopress'), sp_admin_settings_tab() );
	
	do_action( 'sp_options_tabs', $tabs );	
	
	$form->add_element( $tabs->get_html() );
	
	$html.= $form->get_html();
	
	$content.= apply_filters( 'sp_seo_settings_bottom', $html );
	
	echo $content;	
	
	include( 'footer.php' );
	
}

?>