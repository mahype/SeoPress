<?php
/**
* SeoPress Value class
*
* @author    Sven Wagener <svenw_at_themekraft_dot_com>
* @copyright Themekraft.com
* @include   Funktion:_include_
*/
class SP_Head{
	
	var $settings;
	var $page_type;
	var $special_tags;
	var $used_tags;
	var $meta;
	
	/**
    * Constructor of class
    * @desc Constructor of class
    * */
	public function __construct(){
		global $special_tags;
		
		$this->seo_settings = get_blog_option( SITE_ID_CURRENT_SITE , 'seopress_seo_settings_values' );
		$this->options = get_blog_option( SITE_ID_CURRENT_SITE , 'seopress_options_values' );
		$this->init_special_tags();
		
		// Initialising data for frontend	
		if( !is_admin() ){
			
			global $wp_filter, $merged_filters;
			
			if( tk_is_buddypress() ){ // Should be reworked <- BP have to be hooked in
				remove_all_filters( 'bp_page_title' );				
				add_filter( 'bp_page_title' , array(&$this, 'init_seo') , 1 ); // Filtering buddypress title
			}
			
			remove_all_filters( 'wp_title' );
			add_filter( 'wp_title' ,  array( &$this, 'init_seo') , 1, 3 );  // Filtering wordpress title
			add_filter( 'bloginfo', array( &$this, 'delete_bloginfo_name') , 1, 2 );
			
			$this->used_tags = $special_tags->get_tags( $this->page_type ); // ???? Here ????
			
			do_action( 'sp_init' );
			
		// Initialising data for admin			
		}else{
			
			$this->init_admin();
			
			add_action( 'admin_init', 'sp_register_seo_settings_form' );
			add_action( 'admin_init', 'sp_register_options_form' );
			add_action( 'admin_init', 'sp_register_post_metabox_form' );
			
			add_action( 'admin_menu', 'sp_admin_menue');
			
			## Setting up post & page forms
			if( !isset( $this->options['metabox_post'] ) ){
				add_action('edit_form_advanced', 'sp_post_metabox');
			}
			if( !isset( $this->options['metabox_page'] ) ){
				add_action('edit_page_form', 'sp_page_metabox');
			}
			
			$this->special_tags = $special_tags->get_tags();
			
			add_action( 'sp_seo_settings_tabs', 'sp_get_pro_tab', 10 );
			add_action( 'sp_options_tabs', 'sp_get_pro_tab', 10 );
			add_action( 'admin_head', 'sp_setup', 10 );
			
			add_action( 'admin_notices', array( &$this, 'seopress_warnings') );
			
			do_action( 'sp_admin_init' );
		}
	}

	/**
    * Filtering blogname on Website
	* @param $output string The output of the filter 'bloginfo'
	* @param $show string The show parameter of the filter 'bloginfo'  
    * @desc Initializes data for site and sets title
    * */
	public function delete_bloginfo_name( $output = '', $show = '' ){
		if( $show == 'name' || $output == get_option('blogname') || $output == get_option('blogdescription') )
			$output = '';

		return $output;
	}
	
	/**
    * Initializing seo data for reuested page
    * @desc Initializes data for site and sets title
    * */	
	public function init_seo( $title, $sep = '' , $seplocation = '' ){
		
		if( !is_404() && FALSE != $sep ){
			global $page, $paged;
			
			// Setup meta data and getting title
			$new_title = $this->get_seo_data( 'title' );
			
			// Adding Pagination
			if ( ( $paged >= 2 || $page >= 2 ) && isset( $this->options['show_pagination'] ) )
				$new_title.= ' | ' . sprintf( __( 'Page %s', 'seopress' ), max( $paged, $page ) );
			
			// Adding meta tags to wp head
			add_action( 'wp_head' , array(&$this, 'insert_meta') , 1 );
			
			if( $new_title != '' ) $title =  apply_filters('sp_title', $this->filter_for_html_output( $new_title ) ) ;
				
		}
		return $title;	
	}
	
	public function filter_for_html_output( $string ){
		$string = strip_tags( $string ); // Filtering HTML Tags
		$string = htmlentities( $string, ENT_QUOTES, 'UTF-8'  ); // Changning all special chanrs to HTML chars
		$string = stripslashes( $string ); // Stripping slashes 
		
		return $string;
	}
	
	/**
    * Gets the SEO data
    * @desc Initializes data for head and sets title
    * */
	public function get_seo_data( $key = false ){
		global $bp;
		
		if( ( is_single() || is_page() ) && $bp->current_component=='' ) $meta = $this->get_post_meta();
		
		if( $meta == '' ){
			$template = $this->get_template();
			$meta = $this->replace_template( $template );
		}
		
		$meta = $this->filter_meta( $meta );
		
		$meta['title'] = $meta['title'];
		$meta['description'] = apply_filters( 'sp_description', $meta['description'] );
		$meta['keywords'] = apply_filters( 'sp_keywords', $meta['keywords'] );
		$meta['noindex'] = apply_filters( 'sp_noindex', $meta['noindex'] );	
		
		$this->meta = $meta; // Writing meta results in global seopress meta variable
				
		if($key!=false){
			return $meta[ $key ];
		}else{
			return $meta;			
		}
		
	}
	
	public function get_template( $page_type = '' ){
		
		if( $page_type == '' ){
			$page_type = tk_get_page_type();
		}
				
		$template['title'] = $this->seo_settings[ $page_type . '-title' ];
		$template['description'] = $this->seo_settings[ $page_type . '-description' ];
		$template['keywords'] = $this->seo_settings[ $page_type . '-keywords' ];
		$template['noindex'] = $this->seo_settings[ $page_type . '-noindex' ];		
		
		return $template;
	} 
	
	public function update_template( $page_type, $template ){
		if( $page_type != "" ){
			$this->seo_settings['templates'][ $page_type ] = $template;
			update_blog_option ( SITE_ID_CURRENT_SITE, 'seopress_seo_settings' , $this->seo_settings ); // !!!!!!!!!!!!!!!!!! OLD
		}
	}

	public function insert_meta(){
	
		if( $this->meta['noindex']==true ) echo '<meta name="robots" content="noindex" />' . chr(10); 

	    if( trim( $this->meta['description'] ) != "" || trim( $this->meta['description'] ) == ","){	
	    	echo '<meta name="description" content="' . $this->filter_for_html_output( $this->meta['description'] ) . '" />' . chr(10);
		} 
	    if( trim( $this->meta['keywords'] ) != '' ){ 
	    	if(trim( $this->meta['keywords'] ) != ',' ){ //////////////////////////////////// Whats up here? Bad programming?
	    		echo '<meta name="keywords" content="' . $this->filter_for_html_output( $this->meta['keywords'] ) . '" />' . chr(10);
	    	}
		}
		do_action( 'sp_insert_meta' );
	}
	
	public function replace_template( $template ){
		
		global $special_tags;	
	  	$newmeta = Array();
	  	
	  	if( tk_is_buddypress() ){
	  		$fallback_type = 'bp-component-unknown'; // Should be hooked in
	  	}
	  	
	  	if( is_array( $template ) ){
	  		
	  		// Getting meta by replacing special tags in each temlate field
		  	foreach( $template as $key => $meta_field_template ){
		   		$newmeta[ $key ] = $special_tags->replace( $meta_field_template, tk_get_page_type(), $fallback_type );
		  	} 		  	
	  	}
	  	
	  	return $newmeta; 
	}
	
	//////////////////////////////////// Should go to Hook
	public function filter_meta( $meta ){
		
		if( $this->options['std_title_legth'] != '' && $this->options['std_title_legth'] != 0 ){
			$meta['title'] =  substr( $meta['title']  ,0 , $this->options['std_title_legth'] );
		}
		if( $this->options['std_metadesc_legth'] != '' && $this->options['std_metadesc_legth'] != 0 ){
			$meta['description'] = substr( $meta['description']  ,0, $this->options['std_metadesc_legth'] );
		}
		
		$meta = apply_filters( 'sp_filter_meta', $meta );
		
		return $meta;
	}

	public function get_post_meta( $post_id = FALSE ){
		global $post;
		
		if( !$post_id )	$post_id = $post->ID;

		/*
		 * Title
		 */
		
		// SeoPress
		$post_meta = get_post_meta( $post_id, 'sp_post_metabox', TRUE );

		$title[0] = $post_meta['title'];
		
		// WPSEO
		if( $title[0] == '' ) $title= get_post_meta( $post_id, "_wpseo_edit_title" );
		// All in one seopack
		if( $title[0] == '' ) $title=get_post_meta( $post_id, "_aioseop_title" );
				
		// If title isn't empty, fill meta with it
		if( $title[0] != '' ) $meta['title'] = $title[0];
		
		/*
		 * Description
		 */
				
		// SeoPress
		$description[0] = $post_meta['description'];
		
		// WPSEO
		if( $description[0] == '' ) $description = get_post_meta( $post_id, "_wpseo_edit_description" );
		// All in one seopack
		if( $description[0] == '' ) $description = get_post_meta( $post_id, "_aioseop_description" );
		
		// If description isn't empty, fill meta with it
		if( $description[0] != '' ) $meta['description'] = $description[0]; 

		/*
		 * Description
		 */
		// SeoPress
		$keywords[0] = $post_meta['keywords'];
		
		// WPSEO
		if( $keywords[0] == '' ) $keywords = get_post_meta( $post_id, "_wpseo_edit_keywords" );
		// All in one seopack
		if( $keywords[0] == '' ) $keywords = get_post_meta( $post_id, "_aioseop_keywords" );
		
		if( $keywords[0] != '' ) $meta['keywords'] = $keywords[0];
		
		/*
		 * NoIndex
		 */
		$noindex[0] = $post_meta['noindex'];
		
		if( $noindex[0] != '' ){
			$meta['noindex'] = $noindex[0];
		}
		
		return apply_filters( 'sp_post_meta', $meta );
	}
	
	private function init_special_tags(){
		global $special_tags;
		$special_tags = new TK_SPECIAL_TAGS();

		sp_init_special_tags(); // Initializung Special tag sets & tags 		
		sp_init_special_tags_pt(); // Initializung Special tag types
		
		if( tk_is_buddypress() ){ // Should be reworked <- BP have to be hooked in
			sp_init_bp_special_tags(); // Initializung Special tag sets & tags for buddypress
			sp_init_bp_special_tags_pt(); // Initializung Special tag types for buddypress
		}
		
		$special_tags->add_type( 'unknown' , array( 'global' ) );
		
		do_action( 'sp_init_special_tags' );
	}
	
	public function seopress_warnings(){
		$siteurl = get_bloginfo( 'siteurl' );
		
		if( 0 == get_option('blog_public') ){
			$privacy_url = $siteurl . '/wp-admin/options-privacy.php';			 
			echo '<div id="seopress-privacy-warning" class="error"><p>' . sprintf( __('Your blog is not public. Searchengines will be blocked. You can change this in your <a href="%s">privacy settings</a>.', 'seopress' ), $privacy_url ) . '</p></div>';
		}
	}
}