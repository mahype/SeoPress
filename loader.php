<?php

/**
 * 
 * Plugin Name: SeoPress
 * Plugin URI: http://themekraft.com/plugin/seopress/
 * Description: Searchengine optimization plugin for Wordpress & Buddypress
 * Author: Sven Lehnert, Sven Wagener
 * Author URI: http://themekraft.com/
 * License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
 * Version: 2.0 alpha
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

class SeoPress{
	
	/**
	 * PHP5 constructor
	 * 
	 * @since 	1.3
	 * @access 	public
	 * @uses	plugin_basename()
	 * @uses	add_action()
	 */
	function SeoPress(){
		$this->__construct();
	}
	
	function __construct(){
		// Loading base
		$this->constants();
		$this->includes();
		
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) ); // Loading Textdomain
		
		if( is_admin() ):
			add_action( 'network_admin_menu', 	array( $this, 'admin_menu_network' 	)	, 0 ); // Multisite admin menu
			add_action( 'admin_menu', 			array( $this, 'admin_menu' 			)	, 0 ); // Normal admin menu
			
			add_action( 'plugins_loaded', 		array( $this, 'admin_framework' 	) 	, 0 ); // Loading Framework for admin
			add_action( 'admin_head', 			array( $this, 'activation_script' 	) 	, 10 );
			add_action( 'activated_plugin', 	array( $this, 'activate'			)	, 10 );		
		else:
			add_action( 'plugins_loaded', 		array( $this, 'framework'  			) 	, 0 );
		endif;
	}
	
	function admin_menu_network(){
		add_submenu_page( 'sites.php', __( 'SeoPress', 'default-blog-options' ), __( 'SeoPress', 'default-blog-options' ), 'manage_options', 'defaultblog', array( $this, 'admin_page' ) );
	}
	
	function admin_menu(){
		add_submenu_page( 'tools.php', __( 'SeoPress', 'default-blog-options' ), __( 'SeoPress', 'default-blog-options' ), 'manage_options', 'defaultblog', array( $this, 'admin_page' ) );
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
		$args['forms'] = array( 'sp_seo_settings', 'sp_options' );
		tk_framework( $args );
	}

	/**
	 * Starting framework in admin
	 * 
	 * Loading the themekraft framework and starting it
	 * 
	 * @since 	1.3
	 * @access 	public
	 * @global 	string 	$wp_version 	Current WordPress version
	 * @return 	boolean
	 */
	public function admin_framework(){
		$args['jqueryui_components'] = array( 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-ui-autocomplete' );
		$args['forms'] = array( 'sp_seo_settings', 'sp_options' );
		tk_framework( $args );
	}

	public function admin_init(){
		if( !current_user_can( 'level_10' ) ){ 
			return false;
		} else {
			if( defined('SITE_ID_CURRENT_SITE') ){	
		  		if( $blog_id != SITE_ID_CURRENT_SITE ){
		    		return false;
		   		}
			}
		}
		
		add_filter( 'tk_wp_jqueryui_tabs_after_content_sp_page_types_plugins', 'sp_admin_bp_plugins_tabs' );
		
		$wml = SEOPRESS_FOLDER . 'components/admin/backend.xml' ;
		tk_wml_parse_file( $wml );
		
		tk_register_wp_option_group( 'sp_post_metabox' );
		
		add_thickbox();
	}
	
	public function set_header(){
		
	}
	
	/**
	 * Load all related files
	 * 
	 * Attached to bp_include. Stops the plugin if certain conditions are not met.
	 * 
	 * @since 	1.3
	 * @access 	public
	 */
	public function includes(){
		require_once SEOPRESS_FOLDER . '/includes/tkf/loader.php';
		
		// Functions (Should move to Framework!)
		require_once SEOPRESS_FOLDER . '/includes/lib/io.inc.php';
		
		require_once SEOPRESS_FOLDER . '/includes/lib/wordpress/io.inc.php';
		require_once SEOPRESS_FOLDER . '/includes/lib/wordpress/wp_url.inc.php';
		require_once SEOPRESS_FOLDER . '/includes/lib/wordpress/functions.php';
			
		require_once SEOPRESS_FOLDER . '/includes/lib/buddypress/bp-functions.php';
		
		// Admin pages
		require_once SEOPRESS_FOLDER . '/components/admin/single_metabox.php';
		
		require_once SEOPRESS_FOLDER . '/update.php';
		
		// Buddypress Admin
		require_once SEOPRESS_FOLDER . '/components/admin/seo_buddypress_plugins.tab.php';
		
		// Loading css and js
		require_once SEOPRESS_FOLDER . '/includes/css/loader.php';
		
		// Components - Special tag engine
		require_once SEOPRESS_FOLDER . '/components/header/header.php';
		
		require_once SEOPRESS_FOLDER . '/components/special-tags/special-tag-core.php';
		require_once SEOPRESS_FOLDER . '/components/special-tags/wp/page_types.php';
		require_once SEOPRESS_FOLDER . '/components/special-tags/wp/sets.php';
		require_once SEOPRESS_FOLDER . '/components/special-tags/wp/functions.php';
		
		require_once SEOPRESS_FOLDER . '/components/special-tags/bp/page_types.php';
		require_once SEOPRESS_FOLDER . '/components/special-tags/bp/sets.php';
		require_once SEOPRESS_FOLDER . '/components/special-tags/bp/functions.php';
		
		// Components - Facebook
		require_once SEOPRESS_FOLDER . '/components/facebook/loader.php';
	}

	public function activation_script(){
		$sp_setup = get_option( 'seopress_setup' );
		
		if( (bool) $sp_setup['activation_run'] == false ){
			if( true == (bool) $_GET[ 'sp_activate' ] ){
			
				echo '<script type="text/javascript">
						  jQuery(document).ready(function($){
							 imgLoader = new Image(); // preload image
							 imgLoader.src = tb_pathToImage;
						     tb_show("SeoPress - by themekraft.com", "' . SEOPRESS_URLPATH . 'setup.php?page=tk_framework?TB_iframe=true&amp;width=482&amp;height=385" );
						     // placed right after tb_show call
						     
						     // Workaround for getting tabs running
						     
						     // See here: http://themeforest.net/forums/thread/wordpress-32-admin-area-thickbox-triggering-unload-event/46916?page=1#434388
						     // http://wordpress.org/support/topic/wp-32-thickbox-jquery-ui-tabs-conflict
						     
							 $("#TB_window,#TB_overlay,#TB_HideSelect").one("unload",killTheDamnUnloadEvent);
							
							 function killTheDamnUnloadEvent(e) {
							    // you
							    e.stopPropagation();
							    // must
							    e.stopImmediatePropagation();
							    // DIE!
							    return false;
							 }
						  });
					  	</script>';
			}
			update_option( 'seopress_setup',  array( 'activation_run' => true ) );
		}
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
			wp_redirect( get_bloginfo( 'home' ) . '/wp-admin/admin.php?page=seopress_seo&sp_activate=true' );	
			exit;
		}
	}

	/**
	 * Load the languages
	 * 
	 * @since 	1.0
	 * @uses 	load_plugin_textdomain()
	 */
	public function load_textdomain(){
		load_plugin_textdomain( 'seopress', false, SEOPRESS_FOLDER . '/languages/' );
	}
	
	private function constants(){
		define( 'SEOPRESS_FOLDER', 	$this->get_folder() );
		define( 'SEOPRESS_URLPATH', $this->get_url_path() );
	}
	
	/**
	* Getting URL Path
	*
	* @package Default Blog
	* @since 1.0
	*
	*/
	private function get_url_path(){
		$sub_path = substr( SEOPRESS_FOLDER, strlen( ABSPATH ), ( strlen( SEOPRESS_FOLDER ) ) );
		$script_url = get_bloginfo( 'wpurl' ) . '/' . $sub_path;
		return $script_url;
	}
	
	/**
	* Getting URL Path of theme
	*
	* @package Default Blog
	* @since 1.0
	*
	*/
	private function get_folder(){
		$sub_folder = substr( dirname(__FILE__), strlen( ABSPATH ), ( strlen( dirname(__FILE__) ) - strlen( ABSPATH ) ) );
		$script_folder = ABSPATH . $sub_folder;
		return $script_folder;
	}
}

// Get it on!!
$seopress = new SeoPress();
