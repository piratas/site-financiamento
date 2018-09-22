<?php

/**
 * @package PermalinksCustomizer\Admin
 */

class Permalinks_Customizer_Admin {
  
	/**
	 * Initializes WordPress hooks
	 */
	function __construct() {
		add_action( 'admin_menu', array($this, 'permalinks_customizer_menu') );
		add_action( 'admin_init', array($this, 'register_permalinks_customizer_settings') );
  }

	/**
	 * Added Pages in Menu for Settings
	 */
	public function permalinks_customizer_menu() {
		add_menu_page( 'Set Your Permalinks', 'Permalinks Customizer', 'administrator', 'permalinks-customizer-posts-settings', array($this,'permalinks_customizer_posts_settings') );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'Set PostTypes Permalinks', 'PostTypes Permalinks', 'administrator', 'permalinks-customizer-posts-settings', array($this, 'permalinks_customizer_posts_settings') );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'Structure Tags for PostTypes', 'Tags for PostTypes', 'administrator', 'permalinks-customizer-post-tags', array( $this, 'permalinks_customizer_post_tags') );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'Set Taxonomies Permalinks', 'Taxonomies Permalinks', 'administrator', 'permalinks-customizer-taxonomies-settings', array($this, 'permalinks_customizer_taxonomies_settings') );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'Structure Tags for Taxonomies', 'Tags for Taxonomies', 'administrator', 'permalinks-customizer-taxonomy-tags', array( $this, 'permalinks_customizer_taxonomy_tags') );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'Convert Custom Permalink', 'Convert Custom Permalink', 'administrator', 'permalinks-customizer-convert-url', array($this, 'permalinks_customizer_convert_url') );		
	}

	/**
	 * Register Fields to save settings of Post Types
	 */
	public function register_permalinks_customizer_settings() {
		$post_types = get_post_types( '', 'names' );
		foreach ( $post_types as $post_type ) {
			if ( $post_type == 'revision' || $post_type == 'nav_menu_item' || $post_type == 'attachment' ) {
				continue;
			}
			register_setting( 'permalinks-customizer-settings-group', 'permalinks_customizer_'.$post_type);
		}
	}

	/**
	 * Shows the main Settings Page Where user can provide different Permalink Structure for their Post Types
	 */
	public function permalinks_customizer_posts_settings() {   
		$post_types = get_post_types( '', 'objects' );
		echo '<div class="wrap">';
		echo '<h2>PostTypes Permalinks Settings</h2>';
		echo '<div>
						<p>Define the Permalinks for each post type. You can define different structures for each post type.</p>
						<p>Please check all the <a href="'.site_url().'/wp-admin/admin.php?page=permalinks-customizer-post-tags" title="structured tags">structured tags</a> which can be used with this plugin, <a href="'.site_url().'/wp-admin/admin.php?page=permalinks-customizer-post-tags" title="here">here</a>.</p>
					</div>';
		echo '<form method="post" action="options.php">';
		settings_fields( 'permalinks-customizer-settings-group' );
		do_settings_sections( 'permalinks-customizer-settings-group' );
		echo '<table class="form-table">';
		foreach ( $post_types as $post_type ) {
			if ( $post_type->name == 'revision' || $post_type->name == 'nav_menu_item' || $post_type->name == 'attachment' ) {
				continue;
			}
			$perm_struct = 'permalinks_customizer_'.$post_type->name;
			echo '<tr valign="top">
							<th scope="row">'.$post_type->labels->name.'</th>
							<td>'.site_url().'/<input type="text" name="'.$perm_struct.'" value="'.esc_attr( get_option($perm_struct) ) .'" class="regular-text" /></td>
						</tr>';
		}
		echo '</table>';
		submit_button(); 
		echo '</form>';
		echo '</div>';
	}

	/**
	 * Shows all the Tags which this Plugin Supports for Posts
	 */
	public function permalinks_customizer_post_tags() {
		$html  = '<div class="wrap">';
		$html .= '<h2>Structure Tags for Posts</h2>';
		$html .= '<div>These tags can be used to create Permalink Customizers for each post type.</div>';
		$html .= '<table class="form-table">';
		$html .= '<tr valign="top">
								<th scope="row">%title%</th>
								<td>Title of the post. let&#039;s say the title is "This Is A Great Post!" so, it becomes this-is-a-great-post in the URI.</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%year%</th>
								<td>The year of the post, four digits, for example 2004</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%monthnum%</th>
								<td>Month of the year, for example 05</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%day%</th>
								<td>Day of the month, for example 28</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%hour%</th>
								<td>Hour of the day, for example 15</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%minute%</th>
								<td>Minute of the hour, for example 43</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%second%</th>
								<td>Second of the minute, for example 33</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%post_id%</th>
								<td>The unique ID # of the post, for example 423</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%postname%</th>
								<td>A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI.</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%parent_postname%</th>
								<td>A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI. This <strong>Tag</strong> contains Immediate <strong>Parent Page Slug</strong> if any parent page is selected before publishing.</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%all_parents_postname%</th>
								<td>A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI. This <strong>Tag</strong> contains all the <strong>Parent Page Slugs</strong> if any parent page is selected before publishing.</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%category%</th>
								<td>A sanitized version of the category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI.</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%child-category%</th>
								<td>A sanitized version of the category name (category slug field on New/Edit Category panel).</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%product_cat%</th>
								<td>A sanitized version of the product category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI. <i>This <strong>tag</strong> is specially used for WooCommerce Products.</i></td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%author%</th>
								<td>A sanitized version of the author name.</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%author_firstname%</th>
								<td>A sanitized version of the author first name. If author first name is not available so, it uses the author\'s username.</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%author_lastname%</th>
								<td>A sanitized version of the author last name. If author last name is not available so, it uses the author\'s username.</td>
							</tr>';
		$html .= '</table>';
		$html .= '<p><b>Note:</b> "%postname%" is similar as of the "%title%" tag but the difference is that "%postname%" can only be set once whereas "%title%" can be changed. let&#039;s say the title is "This Is A Great Post!" so, it becomes "this-is-a-great-post" in the URI(At the first time, "%postname%" and "%title%" works same) but if you edit and change title let&#039;s say "This Is A WordPress Post!" so, "%postname%" in the URI remains same "this-is-a-great-post" whereas "%title%" in the URI becomes "this-is-a-wordpress-post" </p>';
		$html .= '</div>';
		echo $html;
	}

	/**
	 * Shows the main Settings Page Where user can provide different Permalink Structure for their Taxonomies
	 */
	public function permalinks_customizer_taxonomies_settings() {   
		if (isset($_POST['submit'])) {
			$permalinks_customizer_taxes = array();
			foreach ($_POST as $key => $value) {
				if ($key === 'submit')
					continue;
				$permalinks_customizer_taxes[$key.'_settings'] = array(
					'structure' => $value,
				);
			}
			update_option('permalinks_customizer_taxonomy_settings', serialize( $permalinks_customizer_taxes ) );
		}
		$permalinks_customizer_settings = unserialize( get_option('permalinks_customizer_taxonomy_settings') );
		echo '<div class="wrap">';
		echo '<h2>Taxonomies Permalinks Settings</h2>';
		echo '<div>
						<p>Define the Permalinks for each taxonomy type so, the Permalinks would be created automatically on creating the Term/Category. Otherwise, you need to create the Permalinks manually from the Edit Term/Category Page.</p>
						<p>Please check all the <a href="'.site_url().'/wp-admin/admin.php?page=permalinks-customizer-taxonomy-tags" title="structured tags">structured tags</a> which can be used with this plugin, <a href="'.site_url().'/wp-admin/admin.php?page=permalinks-customizer-taxonomy-tags" title="here">here</a>.</p>
					</div>';
		echo '<form enctype="multipart/form-data" action="" method="POST">';
		echo '<table class="form-table">';
    $taxonomies = get_taxonomies(); 
		foreach ( $taxonomies as $taxonomy ) {
			if ( $taxonomy == 'nav_menu' ) {
				continue;
			}
			$value = '';
			if ( isset($permalinks_customizer_settings[$taxonomy.'_settings']) && isset($permalinks_customizer_settings[$taxonomy.'_settings']['structure']) && !empty($permalinks_customizer_settings[$taxonomy.'_settings']['structure']) ) {
				$value = $permalinks_customizer_settings[$taxonomy.'_settings']['structure'];
			}
			echo '<tr valign="top">
							<th scope="row">'.$taxonomy.'</th>
							<td>'.site_url().'/<input type="text" name="'.$taxonomy.'" value="'.$value.'" class="regular-text" /></td>
						</tr>';
		}
		echo '</table>';
		submit_button(); 
		echo '</form>';
		echo '</div>';
	}

	/**
	 * Shows all the Tags which this Plugin Supports for Taxonomies
	 */
	public function permalinks_customizer_taxonomy_tags() {
		$html  = '<div class="wrap">';
		$html .= '<h2>Structure Tags for Taxonomies</h2>';
		$html .= '<div>These tags can be used to create Permalink Customizers for each post type.</div>';
		$html .= '<table class="form-table">';
		$html .= '<tr valign="top">
								<th scope="row">%name%</th>
								<td>Name of the Term/Category. let&#039;s say the name is "External API" so, it becomes external-api in the URI.</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%term_id%</th>
								<td>The unique ID # of the Term/Category, for example 423</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%slug%</th>
								<td>A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI.</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%parent_slug%</th>
								<td>A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI. This <strong>Tag</strong> contains Immediate <strong>Parent Term/Category Slug</strong> if any parent Term/Category is selected before adding it.</td>
							</tr>';
		$html .= '<tr valign="top">
								<th scope="row">%all_parents_slug%</th>
								<td>A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI. This <strong>Tag</strong> contains all the <strong>Parent Term/Category Slug</strong> if any parent Term/Category is selected before adding it.</td>
							</tr>';
		$html .= '</table>';
		$html .= '</div>';
		echo $html;
	}
	
	/**
	 * This Function Calls the another which provide the functionality to convert Custom Permalink URLs to Permalinks Customizer
	 */
	public function permalinks_customizer_convert_url() {
		require_once(PERMALINKS_CUSTOMIZER_PATH.'admin/class.permalinks-customizer-batch-script.php');
		new Permalinks_Customizer_Batch_Script();
	}
}
