<?php
/**
 * Register WebHook Endpoint: '/wp-json/webhook/v1/cleanup'.
 *
 * @package wp-api-cleanup
 * @since 1.0.0
 */

namespace WP_API_CLEANUP\Api\Webhook;

use WP_REST_Server, WP_REST_Request, WP_REST_Response, RecursiveIteratorIterator, RecursiveDirectoryIterator;

/**
 * Class Cleanup_WebHook
 */
class Cleanup_WebHook {

	/**
	 * Namespace for the endpoint.
	 *
	 * @var string
	 */
	protected static $namespace = 'webhook/v1';

	/**
	 * Base URL for endpoint.
	 *
	 * @var string
	 */
	protected static $webhook_base = 'cleanup';

	/**
	 * Register Routes for Cleanup data: '/wp-json/webhook/v1/cleanup'.
	 *
	 * @return void
	 */
	public static function register_routes() {

		register_rest_route(
			static::$namespace,
			static::$webhook_base,
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => __CLASS__ . '::cleanup_data',
				'args'     => array(
					'token'    => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => function ( $param ) {
							return sanitize_text_field( $param );
						},
						'validate_callback' => function ( $value ) {
							if ( defined( 'WP_API_CLEANUP_TOKEN' ) ) {
								return constant( 'WP_API_CLEANUP_TOKEN' ) === $value;
							}

							return false;
						},
					)
				),
			)
		);
	}

	/**
	 * Cleanup triggered.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public static function cleanup_data( WP_REST_Request $request ) {

		$response      = new WP_REST_Response( '', 400 );
		$response_data = array();

		self::empty_posts();
		self::empty_taxonomies();
		self::empty_uploads_dir();

		wp_cache_flush();

		$response_data['message'] = 'Data Cleanup is done successfully.';
		$response->set_data( $response_data );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Delete all posts.
	 */
	private static function empty_posts() {
		global $wpdb;

		$wpdb->query( "TRUNCATE $wpdb->posts" );
		$wpdb->query( "TRUNCATE $wpdb->postmeta" );
	}

	/**
	 * Delete terms, taxonomies, and tax relationships.
	 */
	private static function empty_taxonomies() {
		global $wpdb;

		$taxonomies = get_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			delete_option( "{$taxonomy}_children" );
		}

		$wpdb->query( "TRUNCATE $wpdb->terms" );
		$wpdb->query( "TRUNCATE $wpdb->term_taxonomy" );
		$wpdb->query( "TRUNCATE $wpdb->term_relationships" );

		if ( ! empty( $wpdb->termmeta ) ) {
			$wpdb->query( "TRUNCATE $wpdb->termmeta" );
		}
	}

	/**
	 * Delete all media files from current site dir.
	 *
	 * @see https://github.com/wp-cli/entity-command/blob/de4d8b507cf1e91958900990e6ae246022319488/src/Site_Command.php#L191-L222
	 */
	private static function empty_uploads_dir() {
		$upload_dir = wp_upload_dir();
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $upload_dir['basedir'], RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		$files_to_unlink = $directories_to_delete = array();
		$is_main_site = is_main_site();
		foreach ( $files as $fileinfo ) {
			$realpath = $fileinfo->getRealPath();
			// Don't clobber subsites when operating on the main site
			if ( $is_main_site && false !== stripos( $realpath, '/sites/' ) ) {
				continue;
			}
			if ( $fileinfo->isDir() ) {
				$directories_to_delete[] = $realpath;
			} else {
				$files_to_unlink[] = $realpath;
			}
		}
		foreach( $files_to_unlink as $file ) {
			unlink( $file );
		}
		foreach( $directories_to_delete as $directory ) {
			// Directory could be main sites directory '/sites' which may be non-empty.
			@rmdir( $directory ); // @codingStandardsIgnoreLine
		}
		// May be non-empty if '/sites' still around.
		@rmdir( $upload_dir['basedir'] ); // @codingStandardsIgnoreLine
	}

}

// Register route.
Cleanup_WebHook::register_routes();
