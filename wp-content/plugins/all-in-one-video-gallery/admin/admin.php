<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Admin class.
 *
 * @since 1.0.0
 */
class AIOVG_Admin {
	
	/**
	 * Insert missing plugin options.
	 *
	 * @since 1.5.2
	 */
	public function wp_loaded() {		
		if ( AIOVG_PLUGIN_VERSION !== get_option( 'aiovg_version' ) ) {	
			$defaults = aiovg_get_default_settings();
			
			// Update the plugin version		
			update_option( 'aiovg_version', AIOVG_PLUGIN_VERSION );			
			
			// Insert the missing player settings
			$player_settings = get_option( 'aiovg_player_settings' );

			$new_player_settings = array();

			if ( ! array_key_exists( 'player', $player_settings ) ) {
				$new_player_settings['player'] = 'iframe';				
			}
			
			if ( ! array_key_exists( 'muted', $player_settings ) ) {
				$new_player_settings['muted'] = $defaults['aiovg_player_settings']['muted'];				
			}
			
			if ( ! array_key_exists( 'use_native_controls', $player_settings ) ) {
				$new_player_settings['use_native_controls'] = $defaults['aiovg_player_settings']['use_native_controls'];				
			}

			if ( count( $new_player_settings ) ) {
				update_option( 'aiovg_player_settings', array_merge( $player_settings, $new_player_settings ) );
			}			

			// Insert the missing videos settings
			$videos_settings = get_option( 'aiovg_videos_settings' );

			$new_videos_settings = array();

			if ( ! array_key_exists( 'template', $videos_settings ) ) {
				$new_videos_settings['template'] = $defaults['aiovg_videos_settings']['template'];				
			}

			if ( ! array_key_exists( 'thumbnail_style', $videos_settings ) ) {
				$new_videos_settings['thumbnail_style'] = $defaults['aiovg_videos_settings']['thumbnail_style'];
			}

			if ( count( $new_videos_settings ) ) {
				update_option( 'aiovg_videos_settings', array_merge( $videos_settings, $new_videos_settings ) );
			}

			// Insert the missing categories settings
			$categories_settings = get_option( 'aiovg_categories_settings' );

			$new_categories_settings = array();

			if ( ! array_key_exists( 'template', $categories_settings ) ) {
				$new_categories_settings['template'] = $defaults['aiovg_categories_settings']['template'];				
			}

			if ( ! array_key_exists( 'hierarchical', $categories_settings ) ) {
				$new_categories_settings['hierarchical'] = $defaults['aiovg_categories_settings']['hierarchical'];				
			}

			if ( count( $new_categories_settings ) ) {
				update_option( 'aiovg_categories_settings', array_merge( $categories_settings, $new_categories_settings ) );
			}

			// Insert the missing socialshare settings
			$socialshare_settings = get_option( 'aiovg_socialshare_settings' );

			$new_socialshare_settings = array();

			if ( ! array_key_exists( 'open_graph_tags', $socialshare_settings ) ) {
				$new_socialshare_settings['open_graph_tags'] = $defaults['aiovg_socialshare_settings']['open_graph_tags'];				
			}

			if ( ! array_key_exists( 'twitter_username', $socialshare_settings ) ) {
				$new_socialshare_settings['twitter_username'] = $defaults['aiovg_socialshare_settings']['twitter_username'];				
			}

			if ( count( $new_socialshare_settings ) ) {
				update_option( 'aiovg_socialshare_settings', array_merge( $socialshare_settings, $new_socialshare_settings ) );
			}
			
			// Insert the missing general settings
			$general_settings = get_option( 'aiovg_general_settings' );

			if ( ! array_key_exists( 'delete_media_files', $general_settings ) ) {
				$general_settings['delete_media_files'] = $defaults['aiovg_general_settings']['delete_media_files'];
				update_option( 'aiovg_general_settings', $general_settings );				
			}

			// Insert the privacy settings			
			if ( false == get_option( 'aiovg_privacy_settings' ) ) {
				add_option( 'aiovg_privacy_settings', $defaults['aiovg_privacy_settings'] );
			}					
		}
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( 
			AIOVG_PLUGIN_SLUG . '-magnific-popup', 
			AIOVG_PLUGIN_URL . 'public/assets/css/magnific-popup.css', 
			array(), 
			'1.1.0', 
			'all' 
		);
		
		wp_enqueue_style( 
			AIOVG_PLUGIN_SLUG . '-admin', 
			AIOVG_PLUGIN_URL . 'admin/assets/css/admin.css', 
			array(), 
			AIOVG_PLUGIN_VERSION, 
			'all' 
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_media();
        wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-magnific-popup', 
			AIOVG_PLUGIN_URL . 'public/assets/js/magnific-popup.min.js', 
			array( 'jquery' ), 
			'1.1.0', 
			false 
		);
		
		wp_enqueue_script( 
			AIOVG_PLUGIN_SLUG . '-admin', 
			AIOVG_PLUGIN_URL . 'admin/assets/js/admin.js', 
			array( 'jquery' ), 
			AIOVG_PLUGIN_VERSION, 
			false 
		);

		wp_localize_script( 
			AIOVG_PLUGIN_SLUG . '-admin', 
			'aiovg_admin', 
			array(
				'ajax_nonce' => wp_create_nonce( 'aiovg_ajax_nonce' ),
				'site_url'   => esc_url_raw( get_site_url() ),
				'i18n'       => array(
					'no_issues_slected' => __( 'Please select at least one issue.', 'all-in-one-video-gallery' )
				)				
			)
		);
	}	

	/**
	 * Manage form submissions.
	 *
	 * @since 1.6.5
	 */
	public function admin_init() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['issues'] ) && isset( $_POST['aiovg_issues_nonce'] ) ) {
			// Verify that the nonce is valid
    		if ( wp_verify_nonce( $_POST['aiovg_issues_nonce'], 'aiovg_fix_ignore_issues' ) ) {
				$redirect_url = admin_url( 'admin.php?page=all-in-one-video-gallery&tab=issues' );

				// Fix Issues
				if ( __( 'Apply Fix', 'all-in-one-video-gallery' ) == $_POST['action'] ) {
					$this->fix_issues();

					$redirect_url = add_query_arg( 
						array( 
							'section' => 'found',
							'success' => 1
						), 
						$redirect_url 
					);
				}

				// Ignore Issues
				if ( __( 'Ignore', 'all-in-one-video-gallery' ) == $_POST['action'] ) {
					$this->ignore_issues();

					$redirect_url = add_query_arg( 
						array( 
							'section' => 'ignored',
							'success' => 1
						), 
						$redirect_url 
					);
				}

				// Redirect
				wp_redirect( $redirect_url );
        		exit;
			}
		}		
	}

	/**
	 * Add plugin's main menu and "Dashboard" menu.
	 *
	 * @since 1.6.5
	 */
	public function admin_menu() {	
		add_menu_page(
            __( 'All-in-One Video Gallery', 'all-in-one-video-gallery' ),
            __( 'Video Gallery', 'all-in-one-video-gallery' ),
            'manage_aiovg_options',
            'all-in-one-video-gallery',
            array( $this, 'display_dashboard_content' ),
            'dashicons-playlist-video',
            5
		);	
		
		add_submenu_page(
			'all-in-one-video-gallery',
			__( 'All-in-One Video Gallery - Dashboard', 'all-in-one-video-gallery' ),
			__( 'Dashboard', 'all-in-one-video-gallery' ),
			'manage_aiovg_options',
			'all-in-one-video-gallery',
			array( $this, 'display_dashboard_content' )
		);
	}

	/**
	 * Display dashboard page content.
	 *
	 * @since 1.6.5
	 */
	public function display_dashboard_content() {
		$tabs = array(			
			'shortcode-builder' => __( 'Shortcode Builder', 'all-in-one-video-gallery' ),
			'faq'               => __( 'FAQ', 'all-in-one-video-gallery' )
		);
		
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'shortcode-builder';

		// Issues
		$issues = $this->check_issues();

		if ( count( $issues['found'] ) || 'issues' == $active_tab  ) {
			$tabs['issues'] = __( 'Issues Found', 'all-in-one-video-gallery' );
		}

		require_once AIOVG_PLUGIN_DIR . 'admin/partials/dashboard.php';	
	}

	/**
	 * Check for plugin issues.
	 *
	 * @since  1.6.5
	 * @return array $issues Array of issues found.
	 */
	public function check_issues() {
		$issues = array(
			'found'   => array(),
			'ignored' => array()
		);

		$_issues = get_option( 'aiovg_issues', $issues );
		$ignored = $_issues['ignored'];		

		// Check: pages_misconfigured
		$page_settings = get_option( 'aiovg_page_settings' );
		$pages = aiovg_get_custom_pages_list();

		foreach ( $pages as $key => $page ) {
			$issue_found = 0;
			$post_id = $page_settings[ $key ];			

			if ( $post_id > 0 ) {
				$post = get_post( $post_id );

				if ( empty( $post ) || 'publish' != $post->post_status ) {
					$issue_found = 1;
				} elseif ( ! empty( $pages[ $key ]['content'] ) && false === strpos( $post->post_content, $pages[ $key ]['content'] ) ) {
					$issue_found = 1;				
				}
			} else {
				$issue_found = 1;
			}

			if ( $issue_found ) {
				if ( in_array( 'pages_misconfigured', $ignored ) ) {
					$issues['ignored'][] = 'pages_misconfigured';
				} else {
					$issues['found'][] = 'pages_misconfigured';
				}

				break;
			}			
		}		

		$issues = apply_filters( 'aiovg_check_issues', $issues );

		// Update		
		update_option( 'aiovg_issues', $issues );

		// Return
		return $issues;
	}	

	/**
	 * Apply fixes.
	 *
	 * @since 1.6.5
	 */
	public function fix_issues() {		
		$fixed = array();

		// Apply the fixes
		$_issues = aiovg_sanitize_array( $_POST['issues'] );

		foreach ( $_issues as $issue ) {
			switch ( $issue ) {
				case 'pages_misconfigured':	
					global $wpdb;

					$page_settings = get_option( 'aiovg_page_settings' );

					$pages = aiovg_get_custom_pages_list();					

					foreach ( $pages as $key => $page ) {
						$issue_found = 0;
						$post_id = $page_settings[ $key ];			
			
						if ( $post_id > 0 ) {
							$post = get_post( $post_id );
			
							if ( empty( $post ) || 'publish' != $post->post_status ) {
								$issue_found = 1;
							} elseif ( ! empty( $pages[ $key ]['content'] ) && false === strpos( $post->post_content, $pages[ $key ]['content'] ) ) {
								$issue_found = 1;		
							}
						} else {
							$issue_found = 1;
						}	
						
						if ( $issue_found ) {
							$insert_id = 0;

							if ( ! empty( $pages[ $key ]['content'] ) ) {
								$query = $wpdb->prepare(
									"SELECT ID FROM {$wpdb->posts} WHERE `post_content` LIKE %s",
									sanitize_text_field( $pages[ $key ]['content'] )
								);

								$ids = $wpdb->get_col( $query );
							} else {
								$ids = array();
							}

							if ( ! empty( $ids ) ) {
								$insert_id = $ids[0];

								// If the page is not published
								if ( 'publish' != get_post_status( $insert_id ) ) {
									wp_update_post(
										array(
											'ID'          => $insert_id,
											'post_status' => 'publish'
										)
									);
								}
							} else {
								$insert_id = wp_insert_post(
									array(
										'post_title'     => $pages[ $key ]['title'],
										'post_content'   => $pages[ $key ]['content'],
										'post_status'    => 'publish',
										'post_author'    => 1,
										'post_type'      => 'page',
										'comment_status' => 'closed'
									)
								);
							}

							$page_settings[ $key ] = $insert_id;
						}
					}

					update_option( 'aiovg_page_settings', $page_settings );

					$fixed[] = $issue;
					break;
			}
		}

		$fixed = apply_filters( 'aiovg_fix_issues', $fixed );

		// Update
		$issues = get_option( 'aiovg_issues', array(
			'found'   => array(),
			'ignored' => array()
		));

		foreach ( $issues['found'] as $index => $issue ) {
			if ( in_array( $issue, $fixed ) ) {
				unset( $issues['found'][ $index ] );
			}
		}

		foreach ( $issues['ignored'] as $index => $issue ) {
			if ( in_array( $issue, $fixed ) ) {
				unset( $issues['ignored'][ $index ] );
			}
		}

		update_option( 'aiovg_issues', $issues );
	}

	/**
	 * Ignore issues.
	 *
	 * @since 1.6.5
	 */
	public function ignore_issues() {
		$ignored = array();

		// Ignore the issues
		$_issues = aiovg_sanitize_array( $_POST['issues'] );		

		foreach ( $_issues as $issue ) {
			switch ( $issue ) {
				case 'pages_misconfigured':					
					$ignored[] = $issue;
					break;
			}
		}

		$ignored = apply_filters( 'aiovg_ignore_issues', $ignored );

		// Update
		$issues = get_option( 'aiovg_issues', array(
			'found'   => array(),
			'ignored' => array()
		));

		foreach ( $issues['found'] as $index => $issue ) {
			if ( in_array( $issue, $ignored ) ) {
				unset( $issues['found'][ $index ] );
			}
		}

		$issues['ignored'] = array_merge( $issues['ignored'], $ignored );

		update_option( 'aiovg_issues', $issues );
	}	

	/**
	 * Get details of the given issue.
	 *
	 * @since  1.6.5
	 * @param  string $issue Issue code.
	 * @return array         Issue details.
	 */
	public function get_issue_details( $issue ) {
		$issues_list = array(
			'pages_misconfigured' => array(
				'title'       => __( 'Pages Misconfigured', 'all-in-one-video-gallery' ),
				'description' => sprintf(
					__( 'During activation, our plugin adds few <a href="%s" target="_blank">pages</a> dynamically on your website that are required for the internal logic of the plugin. We found some of those pages are missing, misconfigured or having a wrong shortcode.', 'all-in-one-video-gallery' ),
					esc_url( admin_url( 'admin.php?page=aiovg_settings&tab=advanced&section=aiovg_page_settings' ) )
				)
			)
		);

		$issues_list = apply_filters( 'aiovg_get_issues_list', $issues_list );
	
		return isset( $issues_list[ $issue ] ) ? $issues_list[ $issue ] : '';
	}
	
	/**
	 * Add a settings link on the plugin listing page.
	 *
	 * @since  1.0.0
	 * @param  array  $links An array of plugin action links.
	 * @return string $links Array of filtered plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( 
			'<a href="%s">%s</a>', 
			esc_url( admin_url( 'admin.php?page=aiovg_settings' ) ), 
			__( 'Settings', 'all-in-one-video-gallery' ) 
		);

        array_unshift( $links, $settings_link );
		
    	return $links;
	}

	/**
	 * Sets the extension and mime type for .vtt files.
	 *
	 * @since  1.5.7
	 * @param  array  $types    File data array containing 'ext', 'type', and 'proper_filename' keys.
     * @param  string $file     Full path to the file.
     * @param  string $filename The name of the file (may differ from $file due to $file being in a tmp directory).
     * @param  array  $mimes    Key is the file extension with value as the mime type.
	 * @return array  $types    Filtered file data array.
	 */
	public function add_filetype_and_ext( $types, $file, $filename, $mimes ) {
		if ( false !== strpos( $filename, '.vtt' ) ) {			
			$types['ext']  = 'vtt';
			$types['type'] = 'text/vtt';
		}
	
		return $types;
	}

}
