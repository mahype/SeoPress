<?php 

function sp_get_pro_tab( $tabs ){
	global $seopress_plugin_url;
	$html = '<div id="tab-head">
	      <div class="sfb-entry">
	      	  <div style="width:250px; padding:0 40px 100px 0; float:left;"><a href="http://themekraft.com/plugin/seopress-pro/" target="_blank"><img src="' . $seopress_plugin_url . 'includes/images/seopress-pro-package.jpg" border="0" /></a></div>
		      <h2>' . __('Pro Version now available!', 'seopress') . '</h2><br>
				<b>' . __('Get SeoPress Pro Version now, and benefit from more functionality, support and a clean UI.', 'seopress') . '</b><br>
				<br>
				<a href="http://themekraft.com/plugin/seopress-pro/" target="_blank">' . __('Upgrade now', 'seopress') . '</a>					
				<br><br>
				<h3>' . __('Pro Features', 'seopress') . '</h3>
				<ol>
					<li>' . __('Keyword generator', 'seopress') . '</li>
					<li>' . __('Text counter for meta title and description', 'seopress') . '</li>
					<li>' . __('No branding: the Pro Version comes without the "Get Pro" tabs.', 'seopress') . '</li>
					<li>' . __('Full support and help in the SeoPress group and forum.', 'seopress') . '</li>
				</ol>
				<br>
				<h3>' . __('Pro Roadmap', 'seopress') . '</h3>
				<ol>
					<li>' . __( 'Sitemap generator (including Buddypress urls)', 'seopress') . '</li>
					<li>' . __( 'Deeplink generator', 'seopress') . '</li>
					<li>' . __( 'Pages and posts optimizer', 'seopress') . '</li>
					<li>' . __( 'Searchengine preview for pages and posts', 'seopress') . '</li>
					<li>' . __( 'Google Pagerank checker', 'seopress') . '</li>
					<li>' . __( 'Canonical url support', 'seopress') . '</li>
					<li>' . __( 'Xprofile special tags for buddypress', 'seopress') . '</li>
				</ol>
				<br>
				<a href="http://themekraft.com/plugin/seopress-pro/" target="_blank">' . __('Upgrade now', 'seopress') . '</a>	
			</div>
	    </div>';
	
	$tabs->add_tab( 'cap_get_pro', __ ('Get Pro version!', 'seopress'), $html );	
}