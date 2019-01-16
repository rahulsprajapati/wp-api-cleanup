<?php
/**
 * Register API Endpoints.
 *
 * @package wp-api-cleanup
 * @since 1.0.0
 */

namespace WP_API_CLEANUP\Api;

if ( ! function_exists( __NAMESPACE__ . '\wp_api_cleanup_endpoint_init' ) ) {

	/**
	 * Initialize API Endpoints.
	 */
	function wp_api_cleanup_endpoint_init() {

		// Register route for WebHook endpoint '/wp-json/webhook/v1/cleanup'.
		require_once WP_API_CLEANUP_PATH . '/webhook/class-cleanup-webhook.php';

	}

	add_action( 'rest_api_init', __NAMESPACE__ . '\wp_api_cleanup_endpoint_init' );
}
