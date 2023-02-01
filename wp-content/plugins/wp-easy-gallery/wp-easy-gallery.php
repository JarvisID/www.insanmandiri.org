<?php
	/*
	Plugin Name: WP Easy Gallery
	Plugin URI: https://plugingarden.com/wordpress-gallery-plugin/
	Description: Wordpress Plugin for creating dynamic photo galleries	
	Author: HahnCreativeGroup
	Text Domain: wp-easy-gallery
	Domain Path: /languages
	Version: 4.8.5
	Author URI: https://plugingarden.com/
	*/
	
if (!class_exists("WP_Easy_Gallery")) {
	class WP_Easy_Gallery {	
	
		//Constructor
		public function __construct() {
			$this->plugin_name = plugin_basename(__FILE__);
			$this->plugin_version = "4.8.5";
			$this->db_version = "1.3";
			
			$this->define_tables();
			$this->define_options();
			
			register_activation_hook( $this->plugin_name,  array(&$this, 'create_database') );
			
			add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
			add_action('wp_head', array($this, 'wp_custom_style'));
			add_action('wp_footer', array($this, 'wp_custom_scripts'));
			add_action( 'admin_menu', array($this, 'add_wpeg_menu'));
			add_action( 'admin_notices', array($this, 'wpeg_upgrade_notice_display') );
			add_action( 'wp_ajax_wp_easy_gallery_notice', array($this, 'wp_easy_gallery_notice') ); 
			add_action( 'admin_footer', array($this, 'wp_easy_gallery_notice_javascript') );
			
			add_shortcode('EasyGallery', array($this, 'EasyGallery_Handler'));
			add_action( 'wp_ajax_wpeg_shortcode', array($this, 'wpeg_shortcode_callback'));
			
			add_action( 'plugins_loaded', array($this, 'update_db_check') );
			add_action( 'plugins_loaded', array($this, 'load_text_domain') );
			
			add_action( 'wp_ajax_wpeg_settings', array($this, 'wpeg_settings') );
			add_action( 'wp_ajax_wpeg_add_gallery', array($this, 'wpeg_add_gallery') );
			add_action( 'wp_ajax_wpeg_edit_gallery', array($this, 'wpeg_edit_gallery') );
			add_action( 'wp_ajax_wpeg_add_images', array($this, 'wpeg_add_images') );
			add_action( 'wp_ajax_wpeg_edit_images', array($this, 'wpeg_edit_images') );
			add_action( 'wp_ajax_wpeg_delete_gallery', array($this, 'wpeg_delete_gallery') );
			add_action( 'wp_ajax_wpeg_delete_image', array($this, 'wpeg_delete_image') );
		}
		
		public function load_text_domain() {
			load_plugin_textdomain( 'wp-easy-gallery', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
		}
		
		public function define_tables() {
			global $wpdb;
			
			$wpdb->easyGalleries = $wpdb->prefix . 'easy_gallery';
			$wpdb->easyImages = $wpdb->prefix . 'easy_gallery_images';
		}
		
		public function define_options() {
			$d = strtotime('+2 Weeks');
			if(!get_option('wp_easy_gallery_defaults')) {
				$gallery_options = array(
					'version'		   		=> 'free',
					'thumbnail_width'  		=> 'auto',
					'thumbnail_height' 		=> 'auto',
					'gallery_theme'			=> 'light_rounded',
					'hide_overlay'	   		=> 'false',
					'hide_social'	   		=> 'false',
					'custom_style'	   		=> '',
					'use_default_style'		=> 'true',
					'drop_shadow'			=> 'true',
					'display_mode'	   => 'wp_easy_gallery',
					'num_columns'	   => 3,
					'show_gallery_name'=> 'true',
					'gallery_name_alignment' => 'left',
					'new_images_first' => 'true',
					'upgrade_notice'	=> array(
									"count" => 0,
									"date" => date('Y-m-d', $d)
								)
				);
				
				add_option('wp_easy_gallery_defaults', $gallery_options);
			}
			else {
				$wpEasyGalleryOptions	= get_option('wp_easy_gallery_defaults');
				$keys = array_keys($wpEasyGalleryOptions);
				
				if (!in_array('version', $keys)) {
					$wpEasyGalleryOptions['version'] = $this->plugin_version;	
				}
				if (!in_array('gallery_theme', $keys)) {
					$wpEasyGalleryOptions['gallery_theme'] = "light_rounded";	
				}
				if (!in_array('hide_overlay', $keys)) {
					$wpEasyGalleryOptions['hide_overlay'] = "false";	
				}
				if (!in_array('hide_social', $keys)) {
					$wpEasyGalleryOptions['hide_social'] = "false";	
				}
				if (!in_array('custom_style', $keys)) {
					$wpEasyGalleryOptions['custom_style'] = "";	
				}
				if (!in_array('use_default_style', $keys)) {
					$wpEasyGalleryOptions['use_default_style'] = "true";	
				}
				if (!in_array('drop_shadow', $keys)) {
					$wpEasyGalleryOptions['drop_shadow'] = "true";	
				}
				if (!in_array('display_mode', $keys)) {
					$wpEasyGalleryOptions['display_mode'] = "wp_easy_gallery";	
				}
				if (!in_array('num_columns', $keys)) {
					$wpEasyGalleryOptions['num_columns'] = 3;	
				}
				if (!in_array('thumbnail_height', $keys)) {
					$wpEasyGalleryOptions['thumbnail_height'] = $wpEasyGalleryOptions['thunbnail_height'];
					unset($wpEasyGalleryOptions['thunbnail_height']);
				}
				if (!in_array('show_gallery_name', $keys)) {
					$wpEasyGalleryOptions['show_gallery_name'] = "true";	
				}
				if (!in_array('gallery_name_alignment', $keys)) {
					$wpEasyGalleryOptions['gallery_name_alignment'] = "left";	
				}
				if (!in_array('new_images_first', $keys)) {
					$wpEasyGalleryOptions['new_images_first'] = "true";	
				}
				if (!in_array('upgrade_notice', $keys)) {					
					$wpEasyGalleryOptions['upgrade_notice'] = array(
									"count" => 0,
									"date" => date('Y-m-d', $d)
								);	
				}
				
				update_option('wp_easy_gallery_defaults', $wpEasyGalleryOptions);	
			}
		}
		
		public function create_database() {
			include_once (dirname (__FILE__) . '/lib/install.php');
			
			install_db();
		}
		
		public function update_database() {
			global $wpdb;			
			$installed_ver = get_option('easy_gallery_db_version');

			$easyGallery = $wpdb->prefix . 'easy_gallery';
			$easyImages = $wpdb->prefix . 'easy_gallery_images';
			
			//Upgrade version 1.2 -> 1.3
			if ( $wpdb->get_var( "show tables like '$easyGallery'" ) == $easyGallery && version_compare($installed_ver['version'], '1.3', '<')) {
				$wpdb->query("ALTER TABLE $easyGallery MODIFY name VARCHAR( 60 ) NOT NULL");
				$wpdb->query("ALTER TABLE $easyGallery MODIFY slug VARCHAR( 60 ) NOT NULL");
			}
			
			update_option('easy_gallery_db_version', $this->db_version);
		}
		
		public function update_db_check() {			
			if (get_option('easy_gallery_db_version') != $this->db_version) {				
				$this->update_database();
			}
		}
		
		public function add_scripts() {
			$wpEasyGalleryOptions = get_option('wp_easy_gallery_defaults');
			wp_enqueue_script('jquery');
			wp_register_script('prettyPhoto', plugins_url( '/js/jquery.prettyPhoto.js', __FILE__ ), array('jquery'));	
			wp_enqueue_script('prettyPhoto');
			wp_register_style( 'prettyPhoto_stylesheet', plugins_url( '/css/prettyPhoto.css', __FILE__ ));
			wp_enqueue_style('prettyPhoto_stylesheet');
			if ($wpEasyGalleryOptions['use_default_style'] == 'true') {
				wp_register_style('easy-gallery-style', plugins_url( '/css/default.css', __FILE__ ));
				wp_enqueue_style('easy-gallery-style');
			}
		}
		
		public function wp_custom_style() {
			$styles = get_option('wp_easy_gallery_defaults');			
			echo "<!-- WP Easy Gallery -->\n<style>.wp-easy-gallery img {".$styles['custom_style']."}</style>";	
		}
		
		public function wp_custom_scripts() {
			$styles = get_option('wp_easy_gallery_defaults');
			$show_overlay = ($styles['hide_overlay'] == 'true') ? 'false' : 'true';
			$show_social = ($styles['hide_social'] == 'true') ? ', show_social: false' : '';
			echo '<script type="text/javascript" async>';
			echo "var wpegSettings = {gallery_theme: '".$styles['gallery_theme']."', show_overlay: ".$show_overlay.$show_social."};";
			echo 'jQuery(document).ready(function(){	jQuery(".gallery a[rel^=\'prettyPhoto\']").prettyPhoto({counter_separator_label:\' of \', theme:wpegSettings.gallery_theme, overlay_gallery:wpegSettings.show_overlay, social_tools:wpegSettings.show_social});});';
			echo '</script>';
		}
		
		public function easy_gallery_admin_scripts() {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_register_script('easy-gallery-uploader', plugins_url( '/js/image-uploader.js', __FILE__ ), array('jquery','media-upload','thickbox'), '1.0.4');
			wp_enqueue_script('easy-gallery-uploader');	 
			wp_enqueue_media();
		}
		
		// Create Admin Panel
		public function add_wpeg_menu()
		{
			add_menu_page(__('WP Easy Gallery','menu-wpeg'), __('WP Easy Gallery','menu-wpeg'), 'manage_options', 'wpeg-admin', array($this, 'show_overview'), 'dashicons-format-gallery' );

			// Add a submenu to the custom top-level menu:
			add_submenu_page('wpeg-admin', __('WP Easy Gallery >> All Galleries','menu-wpeg'), __('All Galleries','menu-wpeg'), 'manage_options', 'wpeg-admin', array($this, 'show_overview'));
			
			// Add a submenu to the custom top-level menu:
			add_submenu_page('wpeg-admin', __('WP Easy Gallery >> Add Gallery','menu-wpeg'), __('Add Gallery','menu-wpeg'), 'manage_options', 'wpeg-add-gallery', array($this, 'add_gallery'));
		
			// Add a submenu to the custom top-level menu:
			add_submenu_page('options.php', __('WP Easy Gallery >> Edit Gallery','menu-wpeg'), __('Edit Gallery','menu-wpeg'), 'manage_options', 'wpeg-edit-gallery', array($this, 'edit_gallery'));

			// Add a second submenu to the custom top-level menu:
			add_submenu_page('wpeg-admin', __('WP Easy Gallery >> Images','menu-wpeg'), __('Images','menu-wpeg'), 'manage_options', 'wpeg-add-images', array($this, 'add_images'));
		
			// Add a second submenu to the custom top-level menu:
			add_submenu_page('wpeg-admin', __('WP Easy Gallery >> Settings','menu-wpeg'), __('Settings','menu-wpeg'), 'manage_options', 'wpeg-settings-page', array($this, 'wpeg_settings_page'));
		
			// Add a second submenu to the custom top-level menu:
			//add_submenu_page('wpeg-admin', __('WP Easy Gallery >> Help (FAQ)','menu-wpeg'), __('Help (FAQ)','menu-wpeg'), 'manage_options', 'wpeg-help', array($this, 'show_help'));
			
			// Add a second submenu to the custom top-level menu:
			add_submenu_page('wpeg-admin', __('WP Easy Gallery >> Tools','menu-wpeg'), __('<span style="color: #f18500;">Tools</span>','menu-wpeg'), 'manage_options', 'wpeg-tools', array($this, 'wpeg_tools'));
		
			wp_register_style('easy-gallery-admin-style', plugins_url( '/css/wp-easy-gallery-admin.css', __FILE__ ));
			wp_enqueue_style('easy-gallery-admin-style');
		}
	
		public function show_overview()
		{
			include("admin/overview.php");			
			add_action( 'admin_footer', array($this, 'wpeg_delete_gallery_javascript') );
		}
	
		public function add_gallery()
		{
			include("admin/add-gallery.php");
			$this->easy_gallery_admin_scripts();
			add_action( 'admin_footer', array($this, 'wpeg_add_gallery_javascript') );
		}
	
		public function edit_gallery()
		{
			include("admin/edit-gallery.php");
			$this->easy_gallery_admin_scripts();
			add_action( 'admin_footer', array($this, 'wpeg_edit_gallery_javascript') );
		}
	
		public function add_images()
		{
			include("admin/add-images.php");			
			add_action( 'admin_footer', array($this, 'wpeg_edit_images_javascript') );
			add_action( 'admin_footer', array($this, 'wpeg_delete_image_javascript') );
			$this->easy_gallery_admin_scripts();
			$this->add_scripts();
			$this->wp_custom_scripts();
		}
	
		public function wpeg_settings_page()
		{
			include("admin/wpeg-settings.php");			
			add_action( 'admin_footer', array($this, 'wpeg_settings_javascript') );	
		}
	
		/*
		public function show_help()
		{
			include("admin/help.php");
		}
		*/
		
		public function wpeg_tools()
		{
			include("admin/wpeg-tools.php");
		}
		
		//notice
		function wp_easy_gallery_notice() {
	check_ajax_referer( 'wp_easy_gallery', 'security' );
	
	$wpEasyGalleryOptions	= get_option("wp_easy_gallery_defaults");
	
	$upgradeObject = $wpEasyGalleryOptions['upgrade_notice'];
	$upgradeObject['count']++;
	$upgradeObject['date'] = date('Y-m-d', strtotime('+3 Month'));
	
	$wpEasyGalleryOptions['upgrade_notice'] = $upgradeObject;	
	
	update_option("wp_easy_gallery_defaults", $wpEasyGalleryOptions);

	wp_die(); // this is required to terminate immediately and return a proper response
}


function wp_easy_gallery_notice_javascript() { 
	$ajax_nonce = wp_create_nonce( "wp_easy_gallery" );
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		jQuery('#wp-easy-gallery-notice-btn').on('click', function() {
			var data = {
				'action': 'wp_easy_gallery_notice',
				'security': '<?php echo $ajax_nonce; ?>'
			};
			
			jQuery('#wp-easy-gallery-notice').hide();
			
			jQuery.post(ajaxurl, data, function(response) {
				
			});
		});
		
	});
	</script> <?php }

		public function wpeg_upgrade_notice_display() {
			$wpEasyGalleryOptions = get_option("wp_easy_gallery_defaults");
			$upgradeObject = $wpEasyGalleryOptions['upgrade_notice'];
			$today = strtotime(date('Y-m-d'));
			$noticeDate = strtotime($upgradeObject['date']);
			$showNotice = false;
	
			if ($today >= $noticeDate && $upgradeObject['count'] < 4) {
				$showNotice = true;
			}
	
			if ($showNotice) {
			?>
			<div id="wp-easy-gallery-notice" class="updated notice" style="clear: both;">
				<p style="float: left;">Need more image gallery features? Upgrade to <a href="http://plugingarden.com/wordpress-gallery-plugin/?src=wpeg" target="_blank">WP Easy Gallery Pro</a>.<br/><i></i></p>
				<button id="wp-easy-gallery-notice-btn" class="notice-dismiss" style="position: relative; float: right;"></button>
				<div style="clear: both;"></div>
			</div>
			<?php
			}
		}
				
		//settings
		public function wpeg_settings() {
			check_ajax_referer( 'wp_easy_gallery', 'security' );
			
			$wpeg_options = get_option('wp_easy_gallery_defaults');
			
			$wpeg_options['show_gallery_name'] = sanitize_text_field($_POST['show_gallery_name']);
			$wpeg_options['gallery_name_alignment'] = sanitize_text_field($_POST['gallery_name_alignment']);
			$wpeg_options['hide_overlay'] = sanitize_text_field($_POST['hide_overlay']);
			$wpeg_options['hide_social'] = sanitize_text_field($_POST['hide_social']);
			$wpeg_options['use_default_style'] = sanitize_text_field($_POST['use_default_style']);
			$wpeg_options['custom_style'] = sanitize_text_field($_POST['custom_style']);
			$wpeg_options['drop_shadow'] = sanitize_text_field($_POST['drop_shadow']);
			$wpeg_options['display_mode'] = sanitize_text_field($_POST['display_mode']);
			$wpeg_options['num_columns'] = intval(sanitize_text_field($_POST['num_columns']) );
	  
			update_option('wp_easy_gallery_defaults', $wpeg_options);
			
			$message = "WP Easy Gallery settings have been saved.";
    
			echo $message;

			wp_die();
		}
		
		public function wpeg_settings_javascript() {
			$ajax_nonce = wp_create_nonce( "wp_easy_gallery" );
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery('#btn-wp-easy-gallery-settings').on('click', function() {
					var display_mode = jQuery('#display_mode option:selected').val();
					var num_columns = jQuery('#num_columns').val();
					var show_gallery_name = jQuery('#show_gallery_name').is(':checked') ? 'true' : 'false';
					var gallery_name_alignment = jQuery('#gallery_name_alignment option:selected').val();					
					var hide_overlay = jQuery('#hide_overlay').is(':checked') ? 'true' : 'false';					
					var hide_social = jQuery('#hide_social').is(':checked') ? 'true' : 'false';
					var use_default_style = jQuery('#use_default_style').is(':checked') ? 'true' : 'false';
					var drop_shadow = jQuery('#drop_shadow').is(':checked') ? 'true' : 'false';					
					var editor_access = jQuery('#editor_access').is(':checked') ? 'true' : 'false';
					var custom_style = jQuery('#custom_style').val();
					
					//var gallery_theme = jQuery('#gallery_theme option:selected').val();
			
					var data = {
						'action': 'wpeg_settings',
						'security': '<?php echo $ajax_nonce; ?>',						
						'display_mode' : display_mode,
						'num_columns' : num_columns,
						'show_gallery_name' : show_gallery_name,
						'gallery_name_alignment' : gallery_name_alignment,
						'hide_overlay' : hide_overlay,
						'hide_social' : hide_social,
						'use_default_style' : use_default_style,
						'drop_shadow' : drop_shadow,
						'editor_access' : editor_access,
						'custom_style' : custom_style						
					};
					jQuery('#wp-easy-gallery-update-status').show();
					jQuery.post(ajaxurl, data, function(response) {				
						jQuery('#wp-easy-gallery-update-status').hide();									
					});
			
					return false;
				});
		
			});
			</script> <?php
		}
		
		//add gallery
		public function wpeg_add_gallery() {
			check_ajax_referer( 'wp_easy_gallery', 'security' );
			
			$galleryName = sanitize_text_field($_POST['galleryName']);
			$galleryDescription = sanitize_text_field($_POST['galleryDescription']);	  
			$slug = strtolower(str_replace(" ", "", $_POST['galleryName']));
			$imagepath = sanitize_text_field(str_replace("\\", "", $_POST['imagepath']));
			$thumbwidth = sanitize_text_field($_POST['thumbwidth']);
			$thumbheight = sanitize_text_field($_POST['thumbheight']);
			
			global $wpdb;
			
			$gallery = $wpdb->get_row( "SELECT * FROM $wpdb->easyGalleries WHERE slug = '".$slug."'" );
			
			if (count($gallery) > 0) {
				$slug = $slug."-".count($gallery);	
			}
			
			$galleryAdded = $wpdb->insert( $wpdb->easyGalleries, array( 'name' => $galleryName, 'slug' => $slug, 'description' => $galleryDescription, 'thumbnail' => $imagepath, 'thumbwidth' => $thumbwidth, 'thumbheight' => $thumbheight ) );
			
			$response = json_encode(array('message' => 'Please check values and try again.', 'success' => 'false'));
			
			if ($galleryAdded) {
				$id = $wpdb->insert_id;
				$message = "Gallery ".$id." has been added.";
				$response = json_encode(array('message' => $message, 'id' => $id, 'success' => 'true'));
			}
    
			echo $response;

			wp_die(); // this is required to terminate immediately and return a proper response
		}
		
		public function wpeg_add_gallery_javascript() {
			$ajax_nonce = wp_create_nonce( "wp_easy_gallery" );
			
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery('#btn-wp-easy-gallery-add-gallery').on('click', function() {
					
					var galleryName = jQuery('#galleryName').val();
					var galleryDescription = jQuery('#galleryDescription').val();  
					var slug = jQuery('#slug').val();
					var imagepath = jQuery('#upload_image').val();
					var thumbwidth = jQuery('#gallerythumbwidth').val();
					var thumbheight = jQuery('#gallerythumbheight').val();
			
					var data = {
						'action': 'wpeg_add_gallery',
						'security': '<?php echo $ajax_nonce; ?>',
						'galleryName' : galleryName,
						'galleryDescription' : galleryDescription,
						'slug' : slug,
						'imagepath' : imagepath,
						'thumbwidth' : thumbwidth,
						'thumbheight' : thumbheight
					};
					jQuery('#wp-easy-gallery-update-status').show();
					jQuery.post(ajaxurl, data, function(response) {	
						var r = JSON.parse(response);
						
						if (r.success == "true") {
							var shortcode = "[EasyGallery id='"+r.id+"']";
							jQuery('#galleryCode').val(shortcode);
							jQuery('#wpeg-gallery-added').show();
							
							jQuery('#btn-wp-easy-gallery-add-gallery').hide();
							jQuery('#btn-wp-easy-gallery-add-gallery-clear').show();
						}
						
						jQuery('#wp-easy-gallery-update-status').hide();
												
					});
			
					return false;
				});
				
				jQuery('#btn-wp-easy-gallery-add-gallery-clear').on('click', function() {
					jQuery('#btn-wp-easy-gallery-add-gallery').show();
					jQuery('#btn-wp-easy-gallery-add-gallery-clear').hide();
					
					jQuery('.wpeg-gallery-form').val("");
					jQuery('#wpeg-gallery-added').hide();
					
					return false;
				});
		
			});
			</script> <?php
		}
		
		//Edit gallery
		public function wpeg_edit_gallery() {
			check_ajax_referer( 'wp_easy_gallery_pro', 'security' );
			
			$galleryId = intval($_POST['galleryId']);
			$galleryName = sanitize_text_field($_POST['galleryName']);
			$galleryDescription = sanitize_text_field($_POST['galleryDescription']);	  
			$slug = strtolower(str_replace(" ", "", $_POST['galleryName']));
			$imagepath = sanitize_text_field(str_replace("\\", "", $_POST['imagepath']));
			$thumbwidth = sanitize_text_field($_POST['thumbwidth']);
			$thumbheight = sanitize_text_field($_POST['thumbheight']);
			
			global $wpdb;
			
			$imageEdited = $wpdb->update( $wpdb->easyGalleries, array( 'name' => $galleryName, 'slug' => $slug, 'description' => $galleryDescription, 'thumbnail' => $imagepath, 'thumbwidth' => $thumbwidth, 'thumbheight' => $thumbheight ), array( 'Id' => $galleryId ) );
			
			$message = "Gallery has been updated.";
			
			echo $message;
			
			wp_die();
		}
		
		public function wpeg_edit_gallery_javascript() {
			$ajax_nonce = wp_create_nonce( "wp_easy_gallery_pro" );
			
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery('#btn-wp-easy-gallery-edit-gallery').on('click', function() {
					
					var galleryId = jQuery('#easy_gallery_edit').val();
					var galleryName = jQuery('#galleryName').val();
					var galleryDescription = jQuery('#galleryDescription').val();
					var imagepath = jQuery('#upload_image').val();
					var thumbwidth = jQuery('#gallerythumbwidth').val();
					var thumbheight = jQuery('#gallerythumbheight').val();
			
					var data = {
						'action': 'wpeg_edit_gallery',
						'security': '<?php echo $ajax_nonce; ?>',
						'galleryId' : galleryId,
						'galleryName' : galleryName,
						'galleryDescription' : galleryDescription,
						'imagepath' : imagepath,
						'thumbwidth' : thumbwidth,
						'thumbheight' : thumbheight
					};
					jQuery('#wp-easy-gallery-update-status').show();
					jQuery.post(ajaxurl, data, function(response) {
						jQuery('#wp-easy-gallery-update-status').hide();
											
					});
			
					return false;
				});		
			});
			</script> <?php
		}
		
		//delete gallery
		public function wpeg_delete_gallery() {
			check_ajax_referer( 'wp_easy_gallery', 'security' );
			
			global $wpdb;
			
			$galleryId = intval($_POST['galleryId']);
			$wpdb->query( "DELETE FROM $wpdb->easyImages WHERE gid = '".$galleryId."'" );
			$wpdb->query( "DELETE FROM $wpdb->easyGalleries WHERE Id = '".$galleryId."'" );
			
			$message = "Gallery $galleryId has been deleted.";
    
			echo $message;

			wp_die(); // this is required to terminate immediately and return a proper response
		}
		
		public function wpeg_delete_gallery_javascript() {
			$ajax_nonce = wp_create_nonce( "wp_easy_gallery" );
			
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery('.btn-wp-easy-gallery-delete').on('click', function() {
					
					if (confirm("Are you sure you want to delete this gallery?")) {
					
					var galleryId = jQuery(this).data('galleryId');
			
					var data = {
						'action': 'wpeg_delete_gallery',
						'security': '<?php echo $ajax_nonce; ?>',
						'galleryId' : galleryId
					};
					jQuery('#wp-easy-gallery-update-status').show();
					jQuery.post(ajaxurl, data, function(response) {						
						var galleryRow = "#gallery-row-"+galleryId;
						$( galleryRow ).fadeOut(750);
						jQuery('#wp-easy-gallery-update-status').hide();											
					});
					}
			
					return false;
				});
		
			});
			</script> <?php
		}
		
		//Add single image
		public function wpeg_add_images() {
						
			$_POST = stripslashes_deep( $_POST );
		
			$galleryId = intval($_POST['galleryId']);			
			$submittedImages = $_POST['images'];
			
			global $wpdb;
			$default_options = get_option('wp_easy_gallery_defaults');

			$imageCounts = $wpdb->get_results( "SELECT Id FROM $wpdb->easyImages WHERE gid = $galleryId" );
			$imageCounter = count($imageCounts);
			$sortOrder = 0;
			$newImages = array();
			
			foreach($submittedImages as $image) {
				$imageData = explode('|', $image);				
				$imageCounter++;
				if ($default_options['new_images_first'] != 'true') {
					$sortOrder = $imageCounter;
				}
				$imageAdded = $wpdb->insert( $wpdb->easyImages, array( 'gid' => $galleryId, 'imagePath' => $imageData[0], 'title' => $imageData[1], 'description' => "", 'sortOrder' => $sortOrder ) );
		  
				$newImage = array(
					'id' => $wpdb->insert_id,
					'title' => $imageData[1],
					'imagePath' => $imageData[0],
					'sortOrder' => $sortOrder
				);
				
				array_push($newImages, $newImage);				
			}
			
			$response = json_encode(array('message' => 'Images has been saved.', 'success' => 'true', 'gId' => $galleryId, 'newImages' => $newImages ));	
			
			echo $response;
			
			wp_die();
		}
		
		public function wpeg_add_images_javascript() {
			$ajax_nonce = wp_create_nonce( "wp_easy_gallery_pro" );
			
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				
				jQuery('#btn-wp-easy-gallery-add-image').hide();
				
				jQuery('#btn-wp-easy-gallery-add-image').on('click', function() {
					
					var galleryId_add = jQuery('#galleryId_add').val();
					var upload_image = jQuery('#upload_image').val();
					var image_title = jQuery('#image_title').val();
					var image_description = jQuery('#image_description').val();
					var image_sortOrder = jQuery('#image_sortOrder').val();
			
					var data = {
						'action': 'wpeg_add_images',
						'security': '<?php echo $ajax_nonce; ?>',
						'galleryId_add' : galleryId_add,
						'upload_image' : upload_image,
						'image_title' : image_title,
						'image_description' : image_description,
						'image_sortOrder' : image_sortOrder
					};
					jQuery('#wp-easy-gallery-update-status').hide();
					jQuery.post(ajaxurl, data, function(response) {							
						var r = JSON.parse(response);
						
						if (r.success == "true") {
							//add image row to UI
							var row = $("<tr id='image-"+r.imageId+"'></tr>");
							var col1 = $("<td><a onclick=\"var images=['"+r.imagePath+"']; var titles=['"+r.imageTitle+"']; var descriptions=['"+r.imageDescription+"']; jQuery.prettyPhoto.open(images,titles,descriptions);\" style='cursor: pointer;'><img src='"+r.imagePath+"' width='75' alt='"+r.imageTitle+"' /></a><br />Click to preview<p style='margin-top: 15px;'><a class='button-link-delete delete-image' data-imageid='"+r.imageId+"'>Delete Image</a></p></td>");
						
							var gId = "<input type='hidden' name='edit_gId[]' value='"+r.gId+"' />";
							var imageId = "<input type='hidden' name='edit_imageId[]' value='"+r.imageId+"' />";
							var imagePath = "<p><strong>Image Path:</strong> <input type='text' name='edit_imagePath[]' size='75' value='"+r.imagePath+"' /></p>";
							var imageTitle = "<p><strong>Image Title:</strong> <input type='text' name='edit_imageTitle[]' size='75' value='"+r.imageTitle+"' /></p>";
							var imageDescription = "<p><strong>Description:</strong> <input type='text' name='edit_imageDescription[]' size='75' value='"+r.imageDescription+"' /></p>";
							var sortOrder = "<p><strong>Sort Order:</strong> <input type='number' name='edit_imageSort[]' size='4' value='"+r.imageOrder+"' /></p>";
					
							var col2 = $("<td>"+gId+imageId+imagePath+imageTitle+imageDescription+sortOrder+"</td>");
							var col3 = $("<td></td>");
							row.append(col1,col2,col3).prependTo("#imageResults");							
							
							jQuery('#upload_image').val('');
							jQuery('#image_title').val('');
							jQuery('#image_description').val('');
							jQuery('#image_sortOrder').val('');
						}
						
						jQuery('#wp-easy-gallery-update-status').hide();	
					});
			
					return false;
				});		
			});
			</script> <?php
		}
		
		//Edit images
		public function wpeg_edit_images() {
			check_ajax_referer( 'wp_easy_gallery', 'security' );			
			
			$_POST = stripslashes_deep( $_POST );
		
			$editImageIds = $_POST['edit_imageId'];
			$imagePaths = $_POST['edit_imagePath'];
			$imageTitles = $_POST['edit_imageTitle'];
			$imageDescriptions = $_POST['edit_imageDescription'];
			$sortOrders = $_POST['edit_imageSort'];
			$deleteIds = $_POST['edit_imageDelete'];
			
			global $wpdb;

			$i = 0;
			foreach($editImageIds as $editImageId) {
				if(in_array($editImageId, $deleteIds)) {
					$wpdb->query( "DELETE FROM $wpdb->easyImages WHERE Id = '".$editImageId."'" );				
				}
				else {
					$imageEdited = $wpdb->update( $wpdb->easyImages, array( 'imagePath' => $imagePaths[$i], 'title' => $imageTitles[$i], 'description' => $imageDescriptions[$i], 'sortOrder' => $sortOrders[$i] ), array( 'Id' => $editImageId ) );
				}		
				$i++;
			}
			
			$response = json_encode(array('message' => 'Image has been edited.', 'success' => 'true', 'deleteIds' => $deleteIds, 'deletecount' => count($deleteIds) ));	
			
			echo $response;
			
			wp_die();			
		}
		
		public function wpeg_edit_images_javascript() {
			$ajax_nonce = wp_create_nonce( "wp_easy_gallery" );
			
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				
				jQuery('#btn-wpeg-edit-images').on('click', function() {
					
					var galleryId = jQuery('#editing_gid').val();
					
					edit_imageId = $("input[name='edit_image[]']").map(function(){return $(this).val();}).get();
					edit_imagePath = $("input[name='edit_imagePath[]']").map(function(){return $(this).val();}).get();
					edit_imageTitle = $("input[name='edit_imageTitle[]']").map(function(){return $(this).val();}).get();
					edit_imageDescription = $("input[name='edit_imageDescription[]']").map(function(){return $(this).val();}).get();
					edit_imageSort = $("input[name='edit_imageSort[]']").map(function(){return $(this).val();}).get();
					edit_imageDelete = $("input[name='edit_imageDelete[]']").map(function(){if ($(this).is(':checked')) {return $(this).val();}}).get();
			
					var data = {
						'action': 'wpeg_edit_images',
						'security': '<?php echo $ajax_nonce; ?>',
						'galleryId' : galleryId,
						'edit_imageId' : edit_imageId,
						'edit_imagePath' : edit_imagePath,
						'edit_imageTitle' : edit_imageTitle,
						'edit_imageDescription' : edit_imageDescription,
						'edit_imageSort' : edit_imageSort,
						'edit_imageDelete' : edit_imageDelete
					};					
					jQuery('#wp-easy-gallery-update-status').show();		
					jQuery.post(ajaxurl, data, function(response) {	
						var r = JSON.parse(response);
						
						if (r.success == "true") {
							//remove image rows from UI
							if (r.deleteIds != null) {
								for (var i = 0; i < r.deleteIds.length; i++) {
									var imageRow = "#image-"+r.deleteIds[i];
									jQuery( imageRow ).fadeOut(750);
								}
							}
						}
						
						//ToDo: re-load edited version of image list
						
						jQuery('#wp-easy-gallery-update-status').hide();
					});
			
					return false;
				});		
			});
			</script> <?php
		}
		
		//Delete image
		public function wpeg_delete_image() {
			check_ajax_referer( 'wp_easy_gallery', 'security' );
			
			$deleteImageId = intval(sanitize_text_field($_POST['imageId']));
			
			global $wpdb;
			
			$wpdb->query( "DELETE FROM $wpdb->easyImages WHERE Id = '".$deleteImageId."'" );			
			
			$response = json_encode(array('message' => 'Image has been deleted.', 'success' => 'true', 'imageId' => $deleteImageId));			
						
			echo $response;
			
			wp_die();
		}
		
		public function wpeg_delete_image_javascript() {
			$ajax_nonce = wp_create_nonce( "wp_easy_gallery" );
			
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				
				jQuery(document).on('click', '.delete-image', function() {
					
					var imageId = jQuery(this).data('imageid');
			
					var data = {
						'action': 'wpeg_delete_image',
						'security': '<?php echo $ajax_nonce; ?>',
						'imageId' : imageId
					};
					jQuery('#wp-easy-gallery-update-status').show();	
					jQuery.post(ajaxurl, data, function(response) {							
						var r = JSON.parse(response);
						if (r.success == "true") {
							//remove image row from UI
							var imageRow = "#image-"+r.imageId;
							$( imageRow ).fadeOut(750);
						}
						jQuery('#wp-easy-gallery-update-status').hide();						
					});
			
					return false;
				});		
			});
			</script> <?php
		}
		
		public function EasyGallery_Handler($atts) {
			$atts = shortcode_atts( array( 'id' => '-1', 'key' => '-1'), $atts );
			return $this->createEasyGallery($atts['id'], $atts['key']);
		}
	
		// function creates the gallery
		public function createEasyGallery($galleryName, $id)	
		{			
			global $wpdb;
			global $easy_gallery_table;
			global $easy_gallery_image_table;
		
			if ($id != "-1") {
				$gallery = $wpdb->get_row( "SELECT Id, name, thumbnail, thumbwidth, thumbheight FROM $wpdb->easyGalleries WHERE Id = '$id'" );
			}
			else {
				$gallery = $wpdb->get_row( "SELECT Id, name, thumbnail, thumbwidth, thumbheight FROM $wpdb->easyGalleries WHERE slug = '$galleryName'" );
			}
			$imageResults = $wpdb->get_results( "SELECT * FROM $wpdb->easyImages WHERE gid = $gallery->Id ORDER BY sortOrder ASC" );
			$options = get_option('wp_easy_gallery_defaults');
			$galleryLink = "";
		
			switch($options['display_mode']) {
				case 'wp_easy_gallery':
					$galleryLink = $this->render_wpeg($gallery, $imageResults, $options);
					break;
				case 'wp_default':
					$galleryLink = $this->render_wp_gallery($gallery, $imageResults, $options);
					break;
				case 'wp_slider':
					$galleryLink = $this->render_wp_slider($gallery, $imageResults, $options);
					break;
				default:
					$galleryLink = $this->render_wpeg($gallery, $imageResults, $options);
					break;
			}
		
			return $galleryLink;
		}
		
		public function render_wpeg($gallery, $imageResults, $options) {
			$images = array();
			$descriptions = array();
			$titles = array();
			$i = 0;
			$thumbImage = $gallery->thumbnail;		
		
			foreach($imageResults as $image)
			{
				if($i == 0)
					$thumbImage = (strlen($gallery->thumbnail) > 0) ? $gallery->thumbnail : $image->imagePath;
				$images[$i] = "'".$image->imagePath."'";
				$descriptions[$i] = "'".$image->description."'";
				$titles[$i] = "'".$image->title."'";
				$i++;
			}
		
			$img = implode(", ", $images);
			$desc = implode(", ", $descriptions);
			$ttl = implode(", ", $titles);
		
			$thumbwidth = ($gallery->thumbwidth < 1 || $gallery->thumbwidth == "auto") ? "" : "width='".$gallery->thumbwidth."'";
			$thumbheight = ($gallery->thumbheight < 1 || $gallery->thumbheight == "auto") ? "" : "height='".$gallery->thumbheight."'";		
		
			$dShadow = ($options['drop_shadow'] == "true") ? "class=\"dShadow trans\"" : "";
			$showName = ($options['show_gallery_name'] == "true") ? "<span class=\"wpeg-gallery-name ".$options['gallery_name_alignment']."\">".$gallery->name."</span>" : "";
		
			$galleryMarkup = "<span class=\"wp-easy-gallery\"><a onclick=\"var images=[".$img."]; var titles=[".$ttl."]; var descriptions=[".$desc."]; jQuery.prettyPhoto.open(images,titles,descriptions);\" title=\"".$gallery->name."\" style=\"cursor: pointer;\"><img ".$dShadow." src=\"".$thumbImage."\" ".$thumbwidth." ".$thumbheight." border=\"0\" alt=\"".$gallery->name."\" />".$showName."</a></span>";
		
			return $galleryMarkup;
		}
		
		public function render_wp_gallery($gallery, $imageResults, $options) {
			$numColumns = $options['num_columns'];
			$showName = $options['show_gallery_name'];
			$galleryMarkup = "<style type='text/css'>#gallery-".$gallery->Id." {margin: auto;}	#gallery-".$gallery->Id." .gallery-item {float: left;margin-top: 10px;text-align: center;width: ".floor(100 / $numColumns)."%;} #gallery-".$gallery->Id." img {border: 2px solid #cfcfcf;}	#gallery-".$gallery->Id." .gallery-caption {margin-left: 0;}</style>";
			$galleryMarkup .= "<div id='gallery-".$gallery->Id."' class='gallery gallery-columns-".$numColumns." gallery-size-thumbnail'>";
			if ($showName == 'true') {
				$galleryMarkup .= "<h4 class=\"wpeg-gallery-name ".$options['gallery_name_alignment']."\">".$gallery->name."</h4>";
			}
		
			foreach($imageResults as $image) {
				$path = explode(".", $image->imagePath);
				$path = str_replace("-150x150","",$path);
				
				$galleryMarkup .= "<figure class=gallery-item>";
				$galleryMarkup .= "<div class='gallery-icon landscape'>";
				$galleryMarkup .= "<a href='".$image->imagePath."' rel='prettyPhoto' title='".$image->title."'>";
				$galleryMarkup .= "<img src='".$image->imagePath."' class='attachment-thumbnail size-thumbnail' alt='".$image->title."' srcset='".$image->imagePath." 150w, ".$image->imagePath." 100w' sizes='(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px'>";
				$galleryMarkup .= "</a>";
				$galleryMarkup .= "</div>";
				$galleryMarkup .= "<figcaption class='wp-caption-text gallery-caption'>";
				$galleryMarkup .= $image->title;
				$galleryMarkup .= "</figcaption>";
				$galleryMarkup .= "</figure>";
			}
		
			$galleryMarkup .= "<br style='clear: both'></div>";
		
			return $galleryMarkup;
		}
		
		public function render_wp_slider($gallery, $imageResults, $options) {
			$showName = ($options['show_gallery_name'] == "true") ? true : false;
			ob_start();
			?>
				<style>
					.wpeg-imageslider-wrap {box-sizing:border-box}

					/* Slideshow container */
					.wpeg-slideshow-container {
						max-width: 1000px;
						position: relative;
						margin: auto;
					}

					/* Hide the images by default */
					.wpeg-slides {
						display: none;
					}

					/* Next & previous buttons */
					.prev, .next {
						cursor: pointer;
						position: absolute;
						top: 50%;
						width: auto;
						margin-top: -22px;
						padding: 16px;
						color: white;
						font-weight: bold;
						font-size: 18px;
						transition: 0.6s ease;
						border-radius: 0 3px 3px 0;
						user-select: none;
					}

					/* Position the 'next button' to the right */
					.next {
						right: 0;
						border-radius: 3px 0 0 3px;
					}

					/* On hover, add a black background color with a little bit see-through */
					.prev:hover, .next:hover {
						background-color: rgba(0,0,0,0.8);
					}

					/* Caption text */
					.text {
						color: #f2f2f2;
						font-size: 15px;
						padding: 8px 12px;
						position: absolute;
						bottom: 8px;
						width: 100%;
						text-align: center;
					}

					/* Number text (1/3 etc) */
					.numbertext {
						color: #f2f2f2;
						font-size: 12px;
						padding: 8px 12px;
						position: absolute;
						top: 0;
					}

					/* The dots/bullets/indicators */
					.dot {
						cursor: pointer;
						height: 15px;
						width: 15px;
						margin: 0 2px;
						background-color: #bbb;
						border-radius: 50%;
						display: inline-block;
						transition: background-color 0.6s ease;
					}

					.active, .dot:hover {
						background-color: #717171;
					}

					/* Fading animation */
					.wpeg-fade-in {
						-webkit-animation-name: wpeg-fade-in;
						-webkit-animation-duration: 1.5s;
						animation-name: wpeg-fade-in;
						animation-duration: 1.5s;
					} 

					@-webkit-keyframes wpeg-fade-in {
						from {opacity: .4} 
						to {opacity: 1}
					}

					@keyframes wpeg-fade-in {
						from {opacity: .4} 
						to {opacity: 1}
					} 
				</style>
				<?php
				$galleryStyle = ob_get_clean();
		
		$galleryMarkup = "";
		$showName = $options['show_gallery_name'];
		if ($showName == 'true') {
			$galleryMarkup .= "<h4 class=\"wpeg-gallery-name ".$options['gallery_name_alignment']."\">".$gallery->name."</h4>";
		}
		
		$galleryMarkup .= "<div class='wpeg-imageslider-wrap'>
		<div class='wpeg-slideshow-container'>

  <!-- Full-width images with number and caption text -->";
		$imageIndex = 1;
		$imageCount = count($imageResults);
		$imageNav = "";
		foreach($imageResults as $image) {			
			$galleryMarkup .= "<div class='wpeg-slides wpeg-fade-in'>
									<div class='numbertext'>".$imageIndex." / ".$imageCount."</div>
									<img src='".$image->imagePath."' style='width:100%'>
									<div class='text'>".$image->title."</div>
								</div>";
			$imageNav .= "<span class='dot' onclick='WpegSlides.setCurrent(".$imageIndex.")'></span>";
			$imageIndex++;
		}
  
$galleryMarkup .= "
  <!-- Next and previous buttons -->
  <a class='prev' onclick='WpegSlides.previous()'>&#10094;</a>
  <a class='next' onclick='WpegSlides.next()'>&#10095;</a>
</div><!-- /wpeg-slideshow-container -->
<br>

<!-- The dots/circles -->
<div style='text-align:center'>
  ".$imageNav." 
</div>
		</div><!-- /wpeg-imageslider-wrap -->";
		ob_start();
		?>
		<script>
		var WpegSlides = (function(){
		var slideIndex = 1;
		
		function incrementSlides(n) {
			showSlide(slideIndex += n);
		};
		
		function showSlide(n) {
			var i;
			var slides = document.getElementsByClassName('wpeg-slides');
			var dots = document.getElementsByClassName('dot');
			if (n > slides.length) {slideIndex = 1} 
			if (n < 1) {slideIndex = slides.length}
			for (i = 0; i < slides.length; i++) {
				slides[i].style.display = 'none'; 
			}
			for (i = 0; i < dots.length; i++) {
				dots[i].className = dots[i].className.replace(' active', '');
			}
			slides[slideIndex-1].style.display = 'block'; 
			dots[slideIndex-1].className += ' active';
		};		
		
		this.setCurrent = function(n) {
			showSlide(slideIndex = n);
		};
		this.next = function() {
			incrementSlides(1);
		};
		this.previous = function() {
			incrementSlides(-1);
		};
		this.init = function() {
			showSlide(slideIndex);
		};

		return this;
	})();
	WpegSlides.init();
	</script><?php
	$galleryScript = ob_get_clean();

		return $galleryStyle.$galleryMarkup.$galleryScript;
		}
		
		public function wpeg_shortcode_callback() {
			global $wpdb; // this is how you get access to the database
			global $easy_gallery_table;

			$galleryResults = $wpdb->get_results( "SELECT Id, name FROM $wpdb->easyGalleries" );
			$count = 0;
		
			$result = '{ "wpEasyGallery": [';
			foreach($galleryResults as $gallery) {
				$count++;
				$result .= '{ "id": "'.$gallery->Id.'", "name": "'.$gallery->name.'"}';
				if ($count < count($galleryResults)) { $result .= ","; }
			} 
			$result .= ']}';

			echo $result;

			wp_die(); // this is required to terminate immediately and return a proper response
		}	
	}
}

if (class_exists("WP_Easy_Gallery")) {
    global $ob_WP_Easy_Gallery;
	$ob_WP_Easy_Gallery = new WP_Easy_Gallery();
}
	
	add_action( 'init', 'wpeg_code_button' );	
	function wpeg_code_button() {
		add_filter( "mce_external_plugins", "wpeg_code_add_button" );
		add_filter( 'mce_buttons', 'wpeg_code_register_button' );
	}
	function wpeg_code_add_button( $plugin_array ) {
		$plugin_array['wpegbutton'] = $dir = plugins_url( 'js/shortcode.js', __FILE__ );
		return $plugin_array;
	}
	function wpeg_code_register_button( $buttons ) {
		array_push( $buttons, 'wpegselector' );
		return $buttons;
	}
?>