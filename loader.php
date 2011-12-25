<?php

/**
 * 
 * Plugin Name: SeoPress
 * Plugin URI: http://themekraft.com/plugin/seopress/
 * Description: Searchengine optimization plugin for Wordpress & Buddypress
 * Author: Sven Lehnert, Sven Wagener
 * Author URI: http://themekraft.com/
 * License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
 * Version: 1.3
 * Text Domain: seopress
 * Site Wide Only: true
 * 
 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $blog_id, $meta, $docroot, $seopress_plugin_url, $seopress_plugin_url, $wpdb;

class SeoPress_Loader{
	/**
	 * The plugin version
	 */
	const VERSION 	= '1.3';
	
	/**
	 * Minimum required WP version
	 */
	const MIN_WP 	= '3.2.1';
	
	/**
	 * Minimum required BP version
	 */
	const MIN_BP 	= '1.5';

	/**
	 * Minimum required PHP version
	 */
	const MIN_PHP 	= '5.2.1';

	/**
	 * Name of the plugin folder
	 */
	static $plugin_name;

	/**
	 * Can the plugin be executed
	 */
	static $active = false;
	
	/**
	 * PHP5 constructor
	 * 
	 * @since 	1.3
	 * @access 	public
	 * @uses	plugin_basename()
	 * @uses	add_action()
	 */
	public function init()
	{
		self::$plugin_name = plugin_basename( __FILE__ );

		self::constants();
		
		add_action( 'plugins_loaded', 	array( __CLASS__, 'framework'  ), 0 );
		add_action( 'plugins_loaded', 	array( __CLASS__, 'check_requirements' ), 10 );
		add_action( 'plugins_loaded', 	array( __CLASS__, 'start' 			   ), 20 );
		
		add_action( 'activated_plugin', array( __CLASS__, 'activate'	       ), 10 );
	}
		
	/**
	 * Check for required versions
	 * 
	 * Checks for WP, BP, PHP and Jigoshop versions
	 * 
	 * @since 	1.3
	 * @access 	public
	 * @global 	string 	$wp_version 	Current WordPress version
	 * @return 	boolean
	 */
	public function check_requirements()
	{
		self::$active = ( ! $error ) ? true : false;
	}
	
	/**
	 * Starting framework
	 * 
	 * Loading the themekraft framework and starting it
	 * 
	 * @since 	1.3
	 * @access 	public
	 * @global 	string 	$wp_version 	Current WordPress version
	 * @return 	boolean
	 */
	public function framework(){
		require_once SEOPRESS_ABSPATH . 'includes/tkf/loader.php';

		$args['text_domain'] = 'seopress';
		tk_framework( $args );
	}
	
	/**
	 * Load all related files
	 * 
	 * Attached to bp_include. Stops the plugin if certain conditions are not met.
	 * 
	 * @since 	1.3
	 * @access 	public
	 */
	public function start()
	{
		global $seopress;
		
		if( self::$active === false )
			return false;
		
		// Functions (Should move to Framework!)
		require_once SEOPRESS_ABSPATH . 'includes/lib/io.inc.php';
		
		require_once SEOPRESS_ABSPATH . 'includes/lib/wordpress/io.inc.php';
		require_once SEOPRESS_ABSPATH . 'includes/lib/wordpress/wp_url.inc.php';
		require_once SEOPRESS_ABSPATH . 'includes/lib/wordpress/functions.php';
			
		require_once SEOPRESS_ABSPATH . 'includes/lib/buddypress/bp-functions.php';
		
		// Admin pages
		require_once SEOPRESS_ABSPATH . 'components/admin/sp_admin_core.php';
		require_once SEOPRESS_ABSPATH . 'components/admin/seo.php';
		require_once SEOPRESS_ABSPATH . 'components/admin/options.php';
		require_once SEOPRESS_ABSPATH . 'components/admin/single_metabox.php';
		
		require_once SEOPRESS_ABSPATH . 'update.php';
		
		// Loading css and js
		require_once SEOPRESS_ABSPATH . 'includes/css/loader.php';
		
		// Components - Special tag engine
		require_once SEOPRESS_ABSPATH . 'components/special-tags/special-tag-core.php';
		require_once SEOPRESS_ABSPATH . 'components/special-tags/wp/page_types.php';
		require_once SEOPRESS_ABSPATH . 'components/special-tags/wp/sets.php';
		require_once SEOPRESS_ABSPATH . 'components/special-tags/wp/functions.php';
		
		require_once SEOPRESS_ABSPATH . 'components/special-tags/bp/page_types.php';
		require_once SEOPRESS_ABSPATH . 'components/special-tags/bp/sets.php';
		require_once SEOPRESS_ABSPATH . 'components/special-tags/bp/functions.php';
		
		// Components - Facebook
		require_once SEOPRESS_ABSPATH . 'components/facebook/loader.php';
		
		require_once SEOPRESS_ABSPATH . 'core.php';
		
		$seopress = new SP_CORE();
		
	}
	
	/**
	 * On plugin activations
	 * 
	 * @since 	1.3
	 * @access 	public
	 */
	public function activate( $plugin ){
		
		// Redirect to plugin page and setup parameters
		if( basename( $plugin ) == 'seopress.php' ){
			update_option( 'seopress_setup', array( 'activation_run' => false ) );
			wp_redirect( get_bloginfo('home') . '/wp-admin/admin.php?page=seopress_seo&sp_activate=true' );	
			exit;
		}
	}

	/**
	 * Load the languages
	 * 
	 * @since 	1.0
	 * @uses 	load_plugin_textdomain()
	 */
	public function translate()
	{
		load_plugin_textdomain( 'seopress', false, dirname( self::$plugin_name ) . '/languages/' );
	}
	
	/**
	 * Declare all constants
	 * 
	 * @since 	1.3
	 * @access 	private
	 */
	private function constants()
	{
		define( 'SEOPRESS_PLUGIN', 	self::$plugin_name );
		define( 'SEOPRESS_VERSION',	self::VERSION );
		define( 'SEOPRESS_FOLDER',	plugin_basename( dirname( __FILE__ ) ) );
		define( 'SEOPRESS_ABSPATH',	trailingslashit( str_replace( "\\", "/", WP_PLUGIN_DIR .'/'. SEOPRESS_FOLDER ) ) );
		define( 'SEOPRESS_URLPATH',	trailingslashit( plugins_url( '/'. SEOPRESS_FOLDER ) ) );
	}
}

// Get it on!!
SeoPress_Loader::init();