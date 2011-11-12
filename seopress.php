<?php
/*
Plugin Name: SeoPress
Plugin URI: http://themekraft.com/plugin/seopress/
Description: Searchengine optimization plugin for Wordpress & Buddypress
Author: Sven Lehnert, Sven Wagener
Author URI: http://themekraft.com/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 1.2.2
Text Domain: seopress
Site Wide Only: true
*/
//
// This is an add-on for WordPress Single, MU and Buddypress
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

global $blog_id, $meta, $docroot, $seopress_plugin_url, $seopress_plugin_url, $wpdb;

$seopress_plugin_url = plugin_dir_url( __FILE__ );

// loading langauge engine
load_plugin_textdomain( 'seopress', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

// Loading libraries
require_once( 'includes/lib/io.inc.php' );
require_once( 'includes/lib/wordpress/io.inc.php' );
// require_once( 'includes/lib/wordpress/wp.inc.php' );
require_once( 'includes/lib/wordpress/wp_url.inc.php' );
require_once( 'includes/lib/wordpress/functions.php' );

// require_once( 'includes/lib/buddypress/bp.inc.php' );
require_once( 'includes/lib/buddypress/bp-functions.php' );

// Loading css and js
require_once( 'includes/css/loader.php' );

// Special tag engine
require_once( 'special-tags/special-tag-core.php' );

require_once( 'special-tags/wp/page_types.php' );
require_once( 'special-tags/wp/sets.php' );
require_once( 'special-tags/wp/functions.php' );

require_once( 'special-tags/bp/page_types.php' );
require_once( 'special-tags/bp/sets.php' );
require_once( 'special-tags/bp/functions.php' );

// Admin pages
require_once( 'admin/sp_admin_core.php' );
require_once( 'admin/seo.php' );
require_once( 'admin/options.php' );
require_once( 'admin/single_metabox.php' );

require_once( 'sp-core.php' );
require_once( 'sp-update.php' );

require_once( 'facebook/loader.php' );

function tk_framework_init(){
	// Registering the form where the data have to be saved
	// $args['forms'] = array( 'myform' );
	
	$args['text_domain'] = 'seopress';
	 
	require_once( 'includes/lib/tkf/loader.php' );
	tk_framework( $args );
}
add_action( 'init', 'tk_framework_init' );

function sp_setup_redirect( $plugin ){
	if( basename( $plugin ) == 'seopress.php' ){
		update_option( 'seopress_setup', array( 'activation_run' => false ) );
		wp_redirect( get_bloginfo('home') . '/wp-admin/admin.php?page=seopress_seo&sp_activate=true' );	
		exit;
	}
}
add_action( 'activated_plugin', 'sp_setup_redirect');

add_action( 'wp_loaded' , 'seopress_init' , 3 );
/*
function test_profiles( $user ){
	echo '<pre>';
	print_r( $user );
	echo '</pre>';
}
add_action( 'personal_options', 'test_profiles', 1 );
*/
?>