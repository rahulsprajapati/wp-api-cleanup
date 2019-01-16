<?php
/**
 * Plugin Name: WP API Cleanup
 * Plugin URI: https://github.com/rahulsprajapati/wp-api-cleanup
 * Description: Cleanup data of WP using Rest API Endpoint. ie. posts, media file, taxonomies.
 * Version: 1.0.0
 * Author: Rahul Prajapati
 * Author URI: https://github.com/rahulsprajapati
 * Text Domain: wp-api-cleanup
 * License: GPL2+
 *
 * @package wp-api-cleanup
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// do not run on wp cli.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	return;
}

define( 'WP_API_CLEANUP_VERSION', '1.0.0' );
define( 'WP_API_CLEANUP_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_API_CLEANUP_PATH', plugin_dir_path( __FILE__ ) );

require_once WP_API_CLEANUP_PATH . '/api.php';

