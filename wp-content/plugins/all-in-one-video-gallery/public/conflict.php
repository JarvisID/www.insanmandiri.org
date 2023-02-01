<?php

/**
 * Fixes for third-party plugin/theme conflict.
 *
 * @link    https://plugins360.com
 * @since   2.4.0
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Public_Conflict class.
 *
 * @since 1.0.0
 */
class AIOVG_Public_Conflict {
	
	/**
	 * Stop autoptimize from optimizing the player page.
	 *
	 * @since 2.4.0
	 * @param bool  "true" to stop optimizing, "false" to optimize.
	 */
	public function noptimize() {
		$page_settings = get_option( 'aiovg_page_settings' );

		if ( $page_settings['player'] > 0 ) {
			$post = get_post( $page_settings['player'] );
			
			if ( strpos( $_SERVER['REQUEST_URI'], $post->post_name ) !== false ) {
				return true;
			}
		}

		return false;
	}
}
